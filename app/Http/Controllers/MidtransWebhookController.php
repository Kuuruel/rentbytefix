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
        Log::info('Midtrans Webhook Raw Request', [
            'headers' => $request->headers->all(),
            'body' => $request->getContent(),
            'all' => $request->all(),
            'method' => $request->method(),
            'ip' => $request->ip()
        ]);

        // Get payload from both JSON and form data
        $payload = json_decode($request->getContent(), true);
        if (!$payload || !is_array($payload)) {
            $payload = $request->all();
        }

        Log::info('Midtrans Webhook Received', $payload);

        // Validate required fields
        if (!isset($payload['order_id'], $payload['status_code'], $payload['gross_amount'])) {
            Log::error('Missing required webhook data', [
                'payload' => $payload,
                'missing_fields' => array_diff(['order_id', 'status_code', 'gross_amount'], array_keys($payload))
            ]);
            return response()->json(['error' => 'Invalid webhook data'], 400);
        }

        // Verify signature
        $serverKey = config('midtrans.server_key');
        $orderId = $payload['order_id'];
        $statusCode = $payload['status_code'];
        $grossAmount = $payload['gross_amount'];
        $signatureKey = $payload['signature_key'] ?? '';
        
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($expectedSignature !== $signatureKey) {
            Log::error('Invalid signature', [
                'expected' => $expectedSignature,
                'received' => $signatureKey,
                'order_id' => $orderId,
                'status_code' => $statusCode,
                'gross_amount' => $grossAmount,
                'server_key_length' => strlen($serverKey)
            ]);
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        // Find bill - Support both old and new format
        $bill = null;
        
        // Try to find by order_id first (new format)
        $bill = Bill::where('order_id', $orderId)->first();
        
        // If not found, try extracting bill ID from order_id (old format)
        if (!$bill) {
            $billId = str_replace(['BILL-', 'ORDER-'], '', $orderId);
            if (is_numeric($billId)) {
                $bill = Bill::find($billId);
            }
        }

        if (!$bill) {
            Log::error('Bill not found', [
                'order_id' => $orderId,
                'tried_bill_id' => $billId ?? null,
                'payload' => $payload
            ]);
            return response()->json(['error' => 'Bill not found'], 404);
        }

        try {
            DB::beginTransaction();

            $transactionStatus = $payload['transaction_status'];
            $fraudStatus = $payload['fraud_status'] ?? null;
            
            Log::info('Processing webhook', [
                'bill_id' => $bill->id,
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus
            ]);

            // Map statuses
            $billStatus = $this->mapBillStatus($transactionStatus, $fraudStatus);
            $mappedTransactionStatus = $this->mapTransactionStatus($transactionStatus, $fraudStatus);

            // Update or create transaction record
            $transaction = Transaction::updateOrCreate(
                [
                    'bill_id' => $bill->id,
                    // Also try to match by order_id if exists
                    'order_id' => $orderId
                ],
                [
                    'bill_id' => $bill->id,
                    'tenant_id' => $bill->tenant_id,
                    'amount' => $bill->amount,
                    'status' => $mappedTransactionStatus,
                    'midtrans_response' => json_encode($payload),
                    'transaction_id' => $payload['transaction_id'] ?? null,
                    'payment_type' => $payload['payment_type'] ?? null,
                    'order_id' => $orderId,
                    'paid_at' => in_array($transactionStatus, ['settlement', 'capture']) && 
                                ($fraudStatus === 'accept' || $fraudStatus === null) ? now() : null,
                ]
            );

            Log::info('Transaction updated', [
                'transaction_id' => $transaction->id,
                'status' => $mappedTransactionStatus
            ]);

            // Handle successful payments
            if (in_array($transactionStatus, ['settlement']) || 
                ($transactionStatus === 'capture' && ($fraudStatus === 'accept' || $fraudStatus === null))) {
                
                $bill->update([
                    'status' => 'paid',
                    'payment_date' => now()
                ]);

                if ($bill->property) {
                    $bill->property->update(['status' => 'Rented']);
                    
                    Log::info('PROPERTY STATUS UPDATED', [
                        'property_id' => $bill->property->id,
                        'property_name' => $bill->property->name ?? 'Unknown',
                        'new_status' => 'Rented',
                        'bill_id' => $bill->id
                    ]);
                }

                Log::info('Payment successful!', [
                    'bill_id' => $bill->id,
                    'order_id' => $orderId,
                    'amount' => $bill->amount
                ]);
            } else {
                // Handle other statuses
                $bill->update(['status' => $billStatus]);
                
                if (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure']) && $bill->property) {
                    if ($bill->property->status === 'Processing') {
                        $bill->property->update(['status' => 'Available']);
                        Log::info('Property status reverted to Available', [
                            'property_id' => $bill->property->id,
                            'bill_id' => $bill->id,
                            'reason' => 'Payment ' . $transactionStatus
                        ]);
                    }
                }
            }

            DB::commit();

            Log::info('Webhook processed successfully', [
                'bill_id' => $bill->id,
                'bill_status' => $bill->fresh()->status,
                'transaction_status' => $transactionStatus,
                'property_status' => $bill->property ? $bill->property->fresh()->status : 'No property'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Webhook processing error: ' . $e->getMessage(), [
                'payload' => $payload,
                'bill_id' => $bill->id ?? null,
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Processing failed: ' . $e->getMessage()], 500);
        }
    }

    public function test(Request $request)
    {
        Log::info('Manual webhook test called', [
            'request_data' => $request->all(),
            'headers' => $request->headers->all(),
            'method' => $request->method()
        ]);
        
        return response()->json([
            'message' => 'Webhook endpoint is working',
            'timestamp' => now(),
            'environment' => app()->environment()
        ]);
    }

    private function mapBillStatus($transactionStatus, $fraudStatus = null)
    {
        switch ($transactionStatus) {
            case 'capture':
                return ($fraudStatus == 'accept' || $fraudStatus === null) ? 'paid' : 'pending';
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
                Log::warning('Unknown transaction status', ['status' => $transactionStatus]);
                return 'pending';
        }
    }

    private function mapTransactionStatus($transactionStatus, $fraudStatus = null)
    {
        switch ($transactionStatus) {
            case 'capture':
                return ($fraudStatus == 'accept' || $fraudStatus === null) ? 'success' : 'pending';
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
                Log::warning('Unknown transaction status', ['status' => $transactionStatus]);
                return 'pending';
        }
    }

    public function testSettlement(Request $request, $billId)
    {
        try {
            $bill = Bill::with('property')->find($billId);
            
            if (!$bill) {
                return response()->json(['error' => 'Bill not found'], 404);
            }

            // Use bill's order_id if exists, otherwise generate one
            $orderId = $bill->order_id ?? ('BILL-' . $bill->id);

            $webhookData = [
                'order_id' => $orderId,
                'transaction_status' => 'settlement',
                'status_code' => '200',
                'gross_amount' => (string)$bill->amount,
                'payment_type' => 'bank_transfer',
                'transaction_time' => now()->toISOString(),
                'settlement_time' => now()->toISOString(),
                'fraud_status' => 'accept',
                'transaction_id' => 'TEST-' . time()
            ];

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
                'order_id' => $orderId,
                'webhook_data' => $webhookData,
                'server_key_length' => strlen($serverKey)
            ]);

            // Create a new request with the webhook data
            $testRequest = Request::create('/webhook/midtrans', 'POST', $webhookData);
            $testRequest->headers->set('Content-Type', 'application/json');

            return $this->handle($testRequest);

        } catch (\Exception $e) {
            Log::error('Manual test error: ' . $e->getMessage(), [
                'bill_id' => $billId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}