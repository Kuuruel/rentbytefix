<?php

// app/Http/Controllers/Landlord/RentalController.php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RentalController;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Renter;
use App\Models\Tenants;
use App\Models\Bill;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

            // Cek apakah properti masih available
            $property = Property::where('id', $request->property_id)
                ->where('status', 'Available')
                ->where('tenant_id', 2) // Pastikan milik landlord yang login
                ->first();

            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Properti tidak tersedia atau bukan milik Anda'
                ], 400);
            }

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
                'due_date' => $startDate->addDays(3), // 3 hari untuk pembayaran
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

            // Update status properti menjadi pending
            // $property->update(['status' => 'Pending']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment link berhasil dibuat! Silakan bagikan link ini kepada penyewa.',
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
            \Log::error('Error creating rental: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook untuk notifikasi dari Midtrans
     */
    public function webhook(Request $request)
    {
        try {
            $result = $this->midtransService->handleNotification($request->all());
            
            if ($result) {
                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'error'], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Halaman sukses pembayaran
     */
    public function paymentSuccess(Request $request)
    {
        return view('landlord.payments.success', [
            'order_id' => $request->order_id,
            'status_code' => $request->status_code,
            'transaction_status' => $request->transaction_status
        ]);
    }

    /**
     * Halaman error pembayaran
     */
    public function paymentError(Request $request)
    {
        return view('landlord.payments.error', [
            'order_id' => $request->order_id,
            'status_code' => $request->status_code,
            'transaction_status' => $request->transaction_status
        ]);
    }

    /**
     * Halaman pending pembayaran
     */
    public function paymentPending(Request $request)
    {
        return view('landlord.payments.pending', [
            'order_id' => $request->order_id,
            'status_code' => $request->status_code,
            'transaction_status' => $request->transaction_status
        ]);
    }

    /**
     * Cek status pembayaran
     */
    public function checkPaymentStatus($billId)
    {
        try {
            $bill = Bill::with(['transactions', 'property', 'renter'])
                ->where('id', $billId)
                ->where('tenant_id',2)
                ->first();

            if (!$bill) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bill tidak ditemukan'
                ], 404);
            }

            $latestTransaction = $bill->transactions()->latest()->first();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'bill' => [
                        'id' => $bill->id,
                        'amount' => $bill->formatted_amount,
                        'status' => $bill->status,
                        'due_date' => $bill->due_date->format('d M Y'),
                        'property_name' => $bill->property->name,
                        'renter_name' => $bill->renter->name,
                        'payment_link' => $bill->payment_link,
                    ],
                    'transaction' => $latestTransaction ? [
                        'status' => $latestTransaction->status,
                        'status_label' => $latestTransaction->status_label,
                        'payment_type' => $latestTransaction->payment_type,
                        'bank' => $latestTransaction->bank,
                        'va_number' => $latestTransaction->va_number,
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error checking payment status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengecek status pembayaran'
            ], 500);
        }
    }
}