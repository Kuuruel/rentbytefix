<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Log incoming request untuk debugging
        Log::info('Midtrans Webhook Raw Request', [
            'headers' => $request->headers->all(),
            'body' => $request->getContent(),
            'all' => $request->all()
        ]);

        // Ambil raw payload
        $payload = json_decode($request->getContent(), true);
        if (!$payload || !is_array($payload)) {
            $payload = $request->all();
        }

        Log::info('Midtrans Webhook Received', $payload);

        // Validasi data yang dibutuhkan
        if (!isset($payload['order_id'], $payload['status_code'], $payload['gross_amount'])) {
            Log::error('Missing required webhook data', $payload);
            return response()->json(['error' => 'Invalid webhook data'], 400);
        }

        // Validate signature - FIX: Pastikan semua data ada
        $serverKey = config('midtrans.server_key');
        $orderId = $payload['order_id'];
        $statusCode = $payload['status_code'];
        $grossAmount = $payload['gross_amount'];
        
        // FIX: Signature validation yang benar
        $signatureKey = $payload['signature_key'] ?? '';
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($expectedSignature !== $signatureKey) {
            Log::error('Invalid signature', [
                'expected' => $expectedSignature,
                'received' => $signatureKey,
                'payload' => $payload
            ]);
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        // Cari bill by order_id - FIX: Handle format yang berbeda
        $billId = str_replace(['BILL-', 'ORDER-'], '', $payload['order_id']);
        $bill = Bill::find($billId);

        if (!$bill) {
            Log::error('Bill not found', [
                'order_id' => $payload['order_id'],
                'extracted_bill_id' => $billId,
                'payload' => $payload
            ]);
            return response()->json(['error' => 'Bill not found'], 404);
        }

        try {
            DB::beginTransaction();

            // Map statuses
            $transactionStatus = $payload['transaction_status'];
            $fraudStatus = $payload['fraud_status'] ?? null;
            
            $billStatus = $this->mapBillStatus($transactionStatus, $fraudStatus);
            $mappedTransactionStatus = $this->mapTransactionStatus($transactionStatus, $fraudStatus);

            // Update bill status
            $bill->update(['status' => $billStatus]);

            // Create / update transaction
            Transaction::updateOrCreate(
                ['bill_id' => $bill->id],
                [
                    'bill_id' => $bill->id,
                    'status' => $mappedTransactionStatus,
                    'midtrans_response' => json_encode($payload),
                    'transaction_id' => $payload['transaction_id'] ?? null,
                    'payment_type' => $payload['payment_type'] ?? null,
                    'paid_at' => in_array($transactionStatus, ['settlement', 'capture']) && 
                                ($fraudStatus === 'accept' || $fraudStatus === null) ? now() : null,
                ]
            );

            // Handle successful payment
            if (in_array($transactionStatus, ['settlement']) || 
                ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
                
                $bill->update([
                    'status' => 'paid',
                    'payment_date' => now()
                ]);

                // Update property status
                if ($bill->property) {
                    $bill->property->update(['status' => 'Rented']);
                    
                    Log::info('PROPERTY STATUS UPDATED', [
                        'property_id' => $bill->property->id,
                        'new_status' => 'Rented'
                    ]);
                }
            }

            DB::commit();

            Log::info('Webhook processed successfully', [
                'bill_id' => $bill->id,
                'bill_status' => $bill->status,
                'transaction_status' => $transactionStatus
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Webhook processing error: ' . $e->getMessage(), [
                'payload' => $payload,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    // Test endpoint untuk manual testing
    public function test(Request $request)
    {
        Log::info('Manual webhook test called', $request->all());
        return response()->json(['message' => 'Webhook endpoint is working']);
    }

    // Mapping untuk Bills table
    private function mapBillStatus($transactionStatus, $fraudStatus = null)
    {
        switch ($transactionStatus) {
            case 'capture':
                return ($fraudStatus == 'accept') ? 'paid' : 'pending';
            case 'settlement':
                return 'paid';
            case 'pending':
                return 'pending';
            case 'deny':
            case 'cancel':
            case 'expire':
            case 'failure':
                return 'failed';
            default:
                return 'pending';
        }
    }

    // Mapping untuk Transactions table
    private function mapTransactionStatus($transactionStatus, $fraudStatus = null)
    {
        switch ($transactionStatus) {
            case 'capture':
                return ($fraudStatus == 'accept') ? 'success' : 'pending';
            case 'settlement':
                return 'success';
            case 'pending':
                return 'pending';
            case 'deny':
            case 'cancel':
            case 'expire':
            case 'failure':
                return 'failed';
            default:
                return 'pending';
        }
    }

    /**
     * Manual test endpoint untuk settlement
     */
    public function testSettlement(Request $request, $billId)
    {
        try {
            $bill = Bill::with('property')->find($billId);
            
            if (!$bill) {
                return response()->json(['error' => 'Bill not found'], 404);
            }

            // Simulate Midtrans webhook data
            $webhookData = [
                'order_id' => 'BILL-' . $bill->id,
                'transaction_status' => 'settlement',
                'status_code' => '200',
                'gross_amount' => (string)$bill->amount,
                'payment_type' => 'bank_transfer',
                'transaction_time' => now()->toISOString(),
                'settlement_time' => now()->toISOString(),
                'fraud_status' => 'accept',
                'transaction_id' => 'TEST-' . time()
            ];

            // Add signature
            $serverKey = config('midtrans.server_key');
            $hashed = hash('sha512', 
                $webhookData['order_id'] . 
                $webhookData['status_code'] . 
                $webhookData['gross_amount'] . 
                $serverKey
            );
            $webhookData['signature_key'] = $hashed;

            Log::info('MANUAL TEST SETTLEMENT', [
                'bill_id' => $billId,
                'webhook_data' => $webhookData
            ]);

            // Create request dan process
            $testRequest = new Request();
            $testRequest->replace($webhookData);

            return $this->handle($testRequest);

        } catch (\Exception $e) {
            Log::error('Manual test error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}