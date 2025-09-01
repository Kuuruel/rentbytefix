<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Renter;
use App\Models\Tenants;
use App\Models\Bill;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RentalController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Buat rental baru dan generate pembayaran
     */
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'renter_name' => 'required|string|max:255',
            'renter_phone' => 'required|string|max:20',
            'renter_email' => 'required|email|max:255',
            'renter_address' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        try {
            DB::beginTransaction();

            // Cek apakah properti masih available dan milik landlord yang login
            $property = Property::where('id', $request->property_id)
                ->where('status', 'Available') // Hanya Available yang bisa di-rent
                ->where('tenant_id', 2) // Pastikan milik landlord yang login
                ->lockForUpdate() // Lock row untuk mencegah race condition
                ->first();

            if (!$property) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Properti tidak tersedia, sedang dalam proses penyewaan, atau bukan milik Anda'
                ], 400);
            }

            // Update status property ke Processing SEGERA setelah validasi
            $property->update(['status' => 'Processing']);

            // Buat atau update data renter
            $renter = Renter::updateOrCreate(
                ['email' => $request->renter_email],
                [
                    'name' => $request->renter_name,
                    'phone' => $request->renter_phone,
                    'address' => $request->renter_address,
                    'tenant_id' => 2,
                    'start_date' => $request->start_date, 
                    'end_date' => $request->end_date, 
                ]
            );

            // Hitung durasi sewa dan total amount
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $endDate = \Carbon\Carbon::parse($request->end_date);
            
            if ($property->rent_type === 'Monthly') {
                $months = $startDate->diffInMonths($endDate);
                $amount = $property->price * max(1, $months);
            } else { // Yearly
                $years = $startDate->diffInYears($endDate);
                $amount = $property->price * max(1, $years);
            }

            // Buat bill terlebih dahulu (tanpa payment link dan snap_token)
            $bill = Bill::create([
                'tenant_id' => 2,
                'renter_id' => $renter->id,
                'property_id' => $property->id,
                'amount' => $amount,
                'due_date' => $startDate->copy()->addDays(3), // 3 hari untuk pembayaran
                'status' => 'pending',
            ]);

            // Generate payment token dari Midtrans dengan Bill instance
            $snapToken = $this->midtransService->createPayment($bill);

            // Generate payment link
            $paymentLink = "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$snapToken}";

            // Update bill dengan payment link dan snap_token
            $bill->update([
                'payment_link' => $paymentLink,
                'snap_token' => $snapToken
            ]);

            DB::commit();

            Log::info('Rental created successfully', [
                'property_id' => $property->id,
                'bill_id' => $bill->id,
                'status_changed' => 'Available -> Processing'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment link berhasil dibuat! Properti dalam status processing.',
                'data' => [
                    'bill_id' => $bill->id,
                    'payment_link' => $paymentLink,
                    'amount' => $bill->formatted_amount,
                    'property_name' => $property->name,
                    'renter_name' => $renter->name,
                    'due_date' => $bill->due_date->format('d M Y H:i'),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating rental: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook untuk notifikasi dari Midtrans (Updated)
     */
    public function webhook(Request $request)
{
    try {
        Log::info('=== WEBHOOK RECEIVED ===', [
            'order_id' => $request->order_id,
            'transaction_status' => $request->transaction_status,
            'gross_amount' => $request->gross_amount,
            'payment_type' => $request->payment_type
        ]);
        
        // Verify signature
        $serverKey = config('midtrans.server_key');
        $hashed = hash('sha512', 
            $request->order_id . 
            $request->status_code . 
            $request->gross_amount . 
            $serverKey
        );
        
        if ($hashed !== $request->signature_key) {
            Log::error('WEBHOOK SIGNATURE INVALID', [
                'order_id' => $request->order_id
            ]);
            return response()->json(['message' => 'Invalid signature'], 400);
        }
        
        // Process notification data
        $orderId = $request->order_id;
        $transactionStatus = $request->transaction_status;
        
        // Find bill by order_id
        $bill = Bill::with(['property', 'renter'])->find($orderId);
        
        if (!$bill) {
            Log::error('BILL NOT FOUND', ['order_id' => $orderId]);
            return response()->json(['message' => 'Bill not found'], 404);
        }
        
        Log::info('BILL FOUND', [
            'bill_id' => $bill->id,
            'current_bill_status' => $bill->status,
            'current_property_status' => $bill->property->status
        ]);
        
        DB::beginTransaction();
        
        // Prepare transaction data menggunakan field yang ada di model
        $transactionData = [
            'bill_id' => $bill->id,
            'midtrans_response' => $request->all(), // Simpan semua data response dari Midtrans
        ];
        
        // Determine status and actions based on transaction_status
        Log::info('PROCESSING TRANSACTION STATUS', ['transaction_status' => $transactionStatus]);
        
        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                Log::info('PAYMENT SUCCESS - UPDATING TO PAID/RENTED');
                
                $transactionData['status'] = Transaction::STATUS_SUCCESS;
                $transactionData['paid_at'] = now();
                
                // Update bill status to paid
                $bill->update([
                    'status' => 'paid',
                    'payment_date' => now()
                ]);
                
                // Update property status to Rented - INI YANG PALING PENTING
                $bill->property->update(['status' => 'Rented']);
                
                Log::info('SUCCESS UPDATES COMPLETED', [
                    'new_bill_status' => 'paid',
                    'new_property_status' => 'Rented',
                    'bill_id' => $bill->id,
                    'property_id' => $bill->property_id
                ]);
                break;
                
            case 'pending':
                Log::info('PAYMENT PENDING - MAINTAINING PROCESSING');
                
                $transactionData['status'] = Transaction::STATUS_PENDING;
                // Keep bill as pending, property stays Processing
                $bill->update(['status' => 'pending']);
                break;
                
            case 'deny':
            case 'cancel':
            case 'expire':
            case 'failure':
                Log::info('PAYMENT FAILED - REVERTING TO AVAILABLE', [
                    'transaction_status' => $transactionStatus
                ]);
                
                $transactionData['status'] = Transaction::STATUS_FAILED;
                
                // Revert bill and property status
                $bill->update(['status' => 'failed']);
                $bill->property->update(['status' => 'Available']);
                break;
                
            default:
                Log::warning('UNKNOWN TRANSACTION STATUS', ['status' => $transactionStatus]);
                $transactionData['status'] = Transaction::STATUS_PENDING;
                break;
        }
        
        // Update atau create transaction record dengan field yang tersedia
        $transaction = Transaction::updateOrCreate(
            ['bill_id' => $bill->id], // Find by bill_id
            $transactionData
        );
        
        Log::info('TRANSACTION RECORD SAVED', [
            'transaction_id' => $transaction->id,
            'status' => $transaction->status
        ]);
        
        DB::commit();
        
        // Final verification log
        $updatedBill = $bill->fresh();
        $updatedProperty = $bill->property->fresh();
        
        Log::info('=== WEBHOOK COMPLETED SUCCESSFULLY ===', [
            'bill_id' => $updatedBill->id,
            'final_bill_status' => $updatedBill->status,
            'final_property_status' => $updatedProperty->status,
            'transaction_status' => $transactionStatus
        ]);
        
        return response()->json(['status' => 'success']);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('=== WEBHOOK ERROR ===', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);
        return response()->json(['status' => 'error'], 500);
    }
}

public function testWebhookSettlement($billId)
{
    try {
        $bill = Bill::with('property')->find($billId);
        
        if (!$bill) {
            return response()->json(['error' => 'Bill not found'], 404);
        }
        
        Log::info('MANUAL TEST - Before Settlement', [
            'bill_id' => $bill->id,
            'bill_status' => $bill->status,
            'property_status' => $bill->property->status
        ]);
        
        DB::beginTransaction();
        
        // Simulate settlement webhook
        $bill->update([
            'status' => 'paid',
            'payment_date' => now()
        ]);
        
        $bill->property->update(['status' => 'Rented']);
        
        // Create transaction record dengan field yang tersedia
        Transaction::create([
            'bill_id' => $bill->id,
            'status' => Transaction::STATUS_SUCCESS,
            'paid_at' => now(),
            'midtrans_response' => [
                'transaction_status' => 'settlement',
                'order_id' => $bill->id,
                'gross_amount' => $bill->amount,
                'payment_type' => 'bank_transfer',
                'settlement_time' => now()->toISOString(),
                'test_mode' => true
            ]
        ]);
        
        DB::commit();
        
        $updatedBill = $bill->fresh();
        $updatedProperty = $bill->property->fresh();
        
        Log::info('MANUAL TEST - After Settlement', [
            'bill_id' => $updatedBill->id,
            'bill_status' => $updatedBill->status,
            'property_status' => $updatedProperty->status
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Settlement simulation completed',
            'data' => [
                'bill_id' => $updatedBill->id,
                'bill_status' => $updatedBill->status,
                'property_status' => $updatedProperty->status
            ]
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Manual settlement test error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
}