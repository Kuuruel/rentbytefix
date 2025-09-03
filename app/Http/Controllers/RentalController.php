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

class RentalController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
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

            $property = Property::where('id', $request->property_id)
                ->where('status', 'Available')
                ->where('tenant_id', 2)
                ->lockForUpdate()
                ->first();

            if (!$property) {
                DB::rollBack();
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
                    'tenant_id' => 2,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'property_id' => $property->id,
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
                'tenant_id' => 2,
                'renter_id' => $renter->id,
                'property_id' => $property->id,
                'amount' => $amount,
                'due_date' => $startDate->copy()->addDays(3),
                'status' => 'pending',
                'order_id' => 'BILL-' . uniqid(),
            ]);

            $orderId = 'BILL-' . $bill->id . '-' . Str::random(6);
            $bill->update(['order_id' => $orderId]);

            $snapToken = $this->midtransService->createPayment($bill);
            $paymentLink = "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$snapToken}";

            $bill->update([
                'payment_link' => $paymentLink,
                'snap_token' => $snapToken
            ]);

            $transaction = Transaction::create([
                'bill_id' => $bill->id,
                'order_id' => $orderId,
                'amount'   => $bill->amount,
                'status'   => Transaction::STATUS_PENDING,
                'midtrans_response' => [
                    'snap_token'   => $snapToken,
                    'payment_link' => $paymentLink
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment link berhasil dibuat! Properti dalam status processing.',
                'data' => [
                    'bill_id' => $bill->id,
                    'payment_link' => $paymentLink,
                    'snap_token' => $snapToken,
                    'transaction' => $transaction,
                    'amount' => $bill->formatted_amount ?? $bill->amount,
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

    public function webhook(Request $request)
    {
        try {
            Log::info('=== WEBHOOK START ===', [
                'order_id' => $request->order_id,
                'transaction_status' => $request->transaction_status,
                'all_data' => $request->all()
            ]);

            $bill = Bill::with('property', 'renter')
                ->where('order_id', $request->order_id)
                ->first();

            if (!$bill) {
                Log::error('BILL NOT FOUND', ['order_id' => $request->order_id]);
                return response()->json(['message' => 'Bill not found'], 404);
            }

            Log::info('BEFORE UPDATE', [
                'bill_id' => $bill->id,
                'bill_status' => $bill->status,
                'property_id' => $bill->property_id,
                'property_status' => $bill->property->status
            ]);

            DB::beginTransaction();

            $transactionStatus = $request->transaction_status;

            switch ($transactionStatus) {
                case 'capture':
                case 'settlement':
                    Log::info('PROCESSING SETTLEMENT.');

                    $billUpdated = $bill->update([
                        'status' => 'paid',
                        'payment_date' => now()
                    ]);

                    $propertyUpdateResult = Property::where('id', $bill->property_id)
                        ->update(['status' => 'Rented']);

                    $property = Property::find($bill->property_id);
                    if ($property) {
                        $property->status = 'Rented';
                        $property->save();
                    }

                    Transaction::updateOrCreate(
                        ['bill_id' => $bill->id],
                        [
                            'bill_id' => $bill->id,
                            'status' => 'success',
                            'paid_at' => now(),
                            'midtrans_response' => $request->all()
                        ]
                    );

                    Log::info('SETTLEMENT COMPLETED');
                    break;

                case 'pending':
                    Log::info('PROCESSING PENDING.');
                    $bill->update(['status' => 'pending']);
                    break;

                case 'deny':
                case 'cancel':
                case 'expire':
                case 'failure':
                    Log::info('PROCESSING FAILED PAYMENT.');

                    $bill->update(['status' => 'failed']);

                    Property::where('id', $bill->property_id)
                        ->update(['status' => 'Available']);

                    Log::info('PROPERTY STATUS RESET TO AVAILABLE');
                    break;

                default:
                    Log::warning('UNKNOWN TRANSACTION STATUS', ['status' => $transactionStatus]);
                    break;
            }

            DB::commit();

            $finalBill = Bill::find($bill->id);
            $finalProperty = Property::find($bill->property_id);

            Log::info('=== FINAL VERIFICATION ===', [
                'bill_id' => $finalBill->id,
                'bill_status' => $finalBill->status,
                'property_id' => $finalProperty->id,
                'property_status' => $finalProperty->status,
                'transaction_status' => $transactionStatus
            ]);

            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                if ($finalBill->status === 'paid' && $finalProperty->status === 'Rented') {
                    Log::info('✅ SUCCESS: Bill paid and Property rented');
                } else {
                    Log::error('❌ FAILURE: Status mismatch', [
                        'expected_bill_status' => 'paid',
                        'actual_bill_status' => $finalBill->status,
                        'expected_property_status' => 'Rented',
                        'actual_property_status' => $finalProperty->status
                    ]);
                }
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('WEBHOOK ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getPaymentStatus($billId)
    {
        try {
            $bill = Bill::with(['property', 'renter', 'transaction'])
                ->find($billId);

            if (!$bill) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bill not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'bill' => [
                        'id' => $bill->id,
                        'status' => $bill->status,
                        'amount' => $bill->amount,
                        'due_date' => $bill->due_date,
                        'payment_date' => $bill->payment_date,
                    ],
                    'property' => [
                        'id' => $bill->property->id,
                        'name' => $bill->property->name,
                        'status' => $bill->property->status,
                    ],
                    'transaction' => $bill->transaction
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking payment status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error checking payment status'
            ], 500);
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
                'property_id' => $bill->property_id,
                'property_status' => $bill->property->status
            ]);

            DB::beginTransaction();

            $bill->update([
                'status' => 'paid',
                'payment_date' => now()
            ]);

            $propertyUpdateCount = Property::where('id', $bill->property_id)
                ->update(['status' => 'Rented']);

            Transaction::create([
                'bill_id' => $bill->id,
                'status' => 'success',
                'paid_at' => now(),
                'midtrans_response' => [
                    'transaction_status' => 'settlement',
                    'order_id' => $bill->order_id,
                    'gross_amount' => $bill->amount,
                    'payment_type' => 'bank_transfer',
                    'settlement_time' => now()->toISOString(),
                    'test_mode' => true
                ]
            ]);

            DB::commit();

            $updatedBill = $bill->fresh();
            $updatedProperty = Property::find($bill->property_id);

            Log::info('MANUAL TEST - After Settlement', [
                'bill_id' => $updatedBill->id,
                'bill_status' => $updatedBill->status,
                'property_id' => $updatedProperty->id,
                'property_status' => $updatedProperty->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Settlement simulation completed',
                'data' => [
                    'bill_id' => $updatedBill->id,
                    'bill_status' => $updatedBill->status,
                    'property_id' => $updatedProperty->id,
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
