<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Transaction;
use App\Models\Property;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function handleNotification($notification)
    {
        try {
            DB::beginTransaction();

            $orderId = $notification['order_id'];
            $transactionStatus = $notification['transaction_status'];
            $fraudStatus = $notification['fraud_status'] ?? null;

            // Parse Bill ID dari order_id format "BILL-123"
            $billId = str_replace('BILL-', '', $orderId);
            $bill = Bill::with('property')->find($billId);
            
            if (!$bill) {
                Log::error("Bill tidak ditemukan untuk order_id: {$orderId}");
                return false;
            }

            // Update atau buat record transaction dengan struktur yang ada
            $transaction = Transaction::updateOrCreate(
                ['bill_id' => $bill->id],
                [
                    'midtrans_response' => $notification,
                    'status' => $transactionStatus,
                    'paid_at' => in_array($transactionStatus, ['settlement', 'capture']) ? now() : null
                ]
            );

            Log::info('Transaction record updated', [
                'transaction_id' => $transaction->id,
                'bill_id' => $bill->id,
                'status' => $transactionStatus
            ]);

            // Handle status transaksi
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $bill->update(['status' => 'pending']); // Masih menunggu verifikasi
                } else if ($fraudStatus == 'accept') {
                    $this->handlePembayaranBerhasil($bill);
                }
            } else if ($transactionStatus == 'settlement') {
                $this->handlePembayaranBerhasil($bill);
            } else if ($transactionStatus == 'pending') {
                $bill->update(['status' => 'pending']);
                Log::info("Bill {$bill->id} status: pending");
            } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel', 'failure'])) {
                $bill->update(['status' => 'failed']);
                // Kembalikan status properti ke Available jika pembayaran gagal
                if ($bill->property && $bill->property->status === 'Processing') {
                    $bill->property->update(['status' => 'Available']);
                    Log::info("Property {$bill->property->id} status dikembalikan ke Available");
                }
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error webhook Midtrans: ' . $e->getMessage(), [
                'notification' => $notification ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    private function handlePembayaranBerhasil(Bill $bill)
    {
        // Update status bill
        $bill->update([
            'status' => 'paid',
            'payment_date' => now()
        ]);

        // Update status properti menjadi 'Rented'
        if ($bill->property) {
            $bill->property->update(['status' => 'Rented']);
            Log::info("Status properti '{$bill->property->name}' berubah menjadi Rented", [
                'property_id' => $bill->property->id,
                'bill_id' => $bill->id
            ]);
        }

        Log::info("Pembayaran berhasil!", [
            'bill_id' => $bill->id,
            'property_status' => 'Rented',
            'bill_status' => 'paid'
        ]);
    }

    public function createPayment(Bill $bill)
    {
        // Set server key
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized', true);
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds', true);

        $params = [
            'transaction_details' => [
                // FIX: PASTIKAN CONSISTENT! Gunakan bill->id, bukan bill->order_id
                'order_id' => 'BILL-' . $bill->id,  // ← PERBAIKAN INI!
                'gross_amount' => $bill->amount,
            ],
            'customer_details' => [
                'first_name' => $bill->renter->name,
                'email' => $bill->renter->email,
                'phone' => $bill->renter->phone,
            ],
            'item_details' => [
                [
                    'id' => $bill->property_id,
                    'price' => $bill->amount,
                    'quantity' => 1,
                    'name' => 'Sewa Properti: ' . $bill->property->name,
                ]
            ],
            'callbacks' => [
                'finish' => url('/landlord/payment/success'),
                'error' => url('/landlord/payment/error'), 
                'pending' => url('/landlord/payment/pending')
            ]
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            Log::info('Snap token created successfully', [
                'bill_id' => $bill->id,
                'order_id' => 'BILL-' . $bill->id  // ← FIX LOG JUGA
            ]);
            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Error creating Midtrans payment: ' . $e->getMessage());
            throw $e;
        }
    }
}