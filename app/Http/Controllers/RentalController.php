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
use App\Models\Transaction;
use Illuminate\Routing\Controller as BaseController;

class RentalController extends BaseController
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
        $this->middleware('auth:tenant');
    }

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

            $currentTenant = Auth::guard('tenant')->user();
            
            if (!$currentTenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - Please login first'
                ], 401);
            }

            Log::info('Rental request by tenant', [
                'tenant_id' => $currentTenant->id,
                'tenant_name' => $currentTenant->name,
                'property_id' => $request->property_id
            ]);

            $property = Property::where('id', $request->property_id)
                ->where('status', 'Available')
                ->where('tenant_id', $currentTenant->id)
                ->where('tenant_id', $currentTenant->id)
                ->lockForUpdate()
                ->first();

            if (!$property) {
                DB::rollBack();
                Log::warning('Property not available for tenant', [
                    'property_id' => $request->property_id,
                    'tenant_id' => $currentTenant->id
                ]);
                
                Log::warning('Property not available for tenant', [
                    'property_id' => $request->property_id,
                    'tenant_id' => $currentTenant->id
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Properti tidak tersedia, sedang dalam proses penyewaan, atau bukan milik Anda'
                ], 400);
            }

            $property->update(['status' => 'Processing']);

            $renter = Renter::updateOrCreate(
                ['email' => $request->renter_email],
                [
                    'name' => $request->renter_name,
                    'phone' => $request->renter_phone,
                    'address' => $request->renter_address,
                    'tenant_id' => $currentTenant->id,
                    'start_date' => $request->start_date, 
                    'end_date' => $request->end_date, 
                ]
            );

            $startDate = \Carbon\Carbon::parse($request->start_date);
            $endDate = \Carbon\Carbon::parse($request->end_date);
            
            if ($property->rent_type === 'Monthly') {
                $months = $startDate->diffInMonths($endDate);
                $amount = $property->price * max(1, $months);
            } else {
                $years = $startDate->diffInYears($endDate);
                $amount = $property->price * max(1, $years);
            }

            $bill = Bill::create([
                'tenant_id' => $currentTenant->id,
                'renter_id' => $renter->id,
                'property_id' => $property->id,
                'amount' => $amount,
                'due_date' => $startDate->copy()->addDays(3),
                'status' => 'pending',
            ]);

            $snapToken = $this->midtransService->createPayment($bill);
            $paymentLink = "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$snapToken}";

            $bill->update([
                'payment_link' => $paymentLink,
                'snap_token' => $snapToken
            ]);

            DB::commit();

            Log::info('Rental created successfully', [
                'tenant_id' => $currentTenant->id,
                'tenant_name' => $currentTenant->name,
                'property_id' => $property->id,
                'bill_id' => $bill->id,
                'amount' => $amount,
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
                    'tenant_name' => $currentTenant->name,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating rental', [
                'tenant_id' => $currentTenant->id ?? 'unknown',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMyProperties(Request $request)
    {
        try {
            $currentTenant = Auth::guard('tenant')->user();
            
            if (!$currentTenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $properties = Property::where('tenant_id', $currentTenant->id)
                ->with(['renter', 'bills'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => $properties
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching tenant properties: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching properties'
            ], 500);
        }
    }

    public function getStatistics(Request $request)
    {
        try {
            $currentTenant = Auth::guard('tenant')->user();
            
            if (!$currentTenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $stats = [
                'total_properties' => Property::where('tenant_id', $currentTenant->id)->count(),
                'available_properties' => Property::where('tenant_id', $currentTenant->id)
                    ->where('status', 'Available')->count(),
                'rented_properties' => Property::where('tenant_id', $currentTenant->id)
                    ->where('status', 'Rented')->count(),
                'processing_properties' => Property::where('tenant_id', $currentTenant->id)
                    ->where('status', 'Processing')->count(),
                'total_income' => Bill::where('tenant_id', $currentTenant->id)
                    ->where('status', 'paid')->sum('amount'),
                'pending_bills' => Bill::where('tenant_id', $currentTenant->id)
                    ->where('status', 'pending')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics'
            ], 500);
        }
    }

    public function webhook(Request $request)
    {
        try {
            Log::info('=== WEBHOOK RECEIVED ===', [
                'order_id' => $request->order_id,
                'transaction_status' => $request->transaction_status,
                'gross_amount' => $request->gross_amount,
                'payment_type' => $request->payment_type
            ]);
            
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
            
            $orderId = $request->order_id;
            $transactionStatus = $request->transaction_status;
            
            $bill = Bill::with(['property', 'renter', 'tenant'])->find($orderId);
            
            if (!$bill) {
                Log::error('BILL NOT FOUND', ['order_id' => $orderId]);
                return response()->json(['message' => 'Bill not found'], 404);
            }
            
            Log::info('BILL FOUND', [
                'bill_id' => $bill->id,
                'tenant_id' => $bill->tenant_id,
                'tenant_name' => $bill->tenant->name ?? 'N/A',
                'current_bill_status' => $bill->status,
                'current_property_status' => $bill->property->status
            ]);
            
            DB::beginTransaction();
            
            $transactionData = [
                'bill_id' => $bill->id,
                'midtrans_response' => $request->all(),
            ];
            
            Log::info('PROCESSING TRANSACTION STATUS', [
                'transaction_status' => $transactionStatus,
                'tenant_id' => $bill->tenant_id
            ]);
            
            switch ($transactionStatus) {
                case 'capture':
                case 'settlement':
                    Log::info('PAYMENT SUCCESS - UPDATING TO PAID/RENTED', [
                        'tenant_id' => $bill->tenant_id,
                        'property_id' => $bill->property_id
                    ]);
                    
                    $transactionData['status'] = Transaction::STATUS_SUCCESS;
                    $transactionData['paid_at'] = now();
                    
                    $bill->update([
                        'status' => 'paid',
                        'payment_date' => now()
                    ]);
                    
                    $bill->property->update(['status' => 'Rented']);
                    
                    Log::info('SUCCESS UPDATES COMPLETED', [
                        'new_bill_status' => 'paid',
                        'new_property_status' => 'Rented',
                        'bill_id' => $bill->id,
                        'property_id' => $bill->property_id,
                        'tenant_id' => $bill->tenant_id
                    ]);
                    break;
                    
                case 'pending':
                    Log::info('PAYMENT PENDING - MAINTAINING PROCESSING');
                    
                    $transactionData['status'] = Transaction::STATUS_PENDING;
                    $bill->update(['status' => 'pending']);
                    break;
                    
                case 'deny':
                case 'cancel':
                case 'expire':
                case 'failure':
                    Log::info('PAYMENT FAILED - REVERTING TO AVAILABLE', [
                        'transaction_status' => $transactionStatus,
                        'tenant_id' => $bill->tenant_id
                    ]);
                    
                    $transactionData['status'] = Transaction::STATUS_FAILED;
                    
                    $bill->update(['status' => 'failed']);
                    $bill->property->update(['status' => 'Available']);
                    break;
                    
                default:
                    Log::warning('UNKNOWN TRANSACTION STATUS', [
                        'status' => $transactionStatus,
                        'tenant_id' => $bill->tenant_id
                    ]);
                    $transactionData['status'] = Transaction::STATUS_PENDING;
                    break;
            }
            
            $transaction = Transaction::updateOrCreate(
                ['bill_id' => $bill->id],
                $transactionData
            );
            
            Log::info('TRANSACTION RECORD SAVED', [
                'transaction_id' => $transaction->id,
                'status' => $transaction->status,
                'tenant_id' => $bill->tenant_id
            ]);
            
            DB::commit();
            
            $updatedBill = $bill->fresh();
            $updatedProperty = $bill->property->fresh();
            
            Log::info('=== WEBHOOK COMPLETED SUCCESSFULLY ===', [
                'bill_id' => $updatedBill->id,
                'tenant_id' => $updatedBill->tenant_id,
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
            $bill = Bill::with(['property', 'tenant'])->find($billId);
            
            if (!$bill) {
                return response()->json(['error' => 'Bill not found'], 404);
            }
            
            Log::info('MANUAL TEST - Before Settlement', [
                'bill_id' => $bill->id,
                'tenant_id' => $bill->tenant_id,
                'bill_status' => $bill->status,
                'property_status' => $bill->property->status
            ]);
            
            DB::beginTransaction();
            
            $bill->update([
                'status' => 'paid',
                'payment_date' => now()
            ]);
            
            $bill->property->update(['status' => 'Rented']);
            
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
                'tenant_id' => $updatedBill->tenant_id,
                'bill_status' => $updatedBill->status,
                'property_status' => $updatedProperty->status
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Settlement simulation completed',
                'data' => [
                    'bill_id' => $updatedBill->id,
                    'tenant_id' => $updatedBill->tenant_id,
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