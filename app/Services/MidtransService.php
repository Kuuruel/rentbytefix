<?php

// app/Services/MidtransService.php
namespace App\Services;

use App\Models\Bill;
use App\Models\Transaction;
use App\Models\Property;
use App\Models\Rental;
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

            // Cari bill berdasarkan order_id
            $bill = Bill::where('id', str_replace('BILL-', '', $orderId))->first();
            
            if (!$bill) {
                Log::error("Bill tidak ditemukan untuk order_id: {$orderId}");
                return false;
            }

            // Update atau buat record transaction
            $transaction = Transaction::updateOrCreate(
                ['bill_id' => $bill->id],
                [
                    'order_id' => $orderId,
                    'transaction_id' => $notification['transaction_id'] ?? null,
                    'gross_amount' => $notification['gross_amount'] ?? $bill->amount,
                    'payment_type' => $notification['payment_type'] ?? null,
                    'transaction_status' => $transactionStatus,
                    'fraud_status' => $fraudStatus,
                    'status_code' => $notification['status_code'] ?? null,
                    'bank' => $notification['va_numbers'][0]['bank'] ?? $notification['permata_va_number'] ?? null,
                    'va_number' => $notification['va_numbers'][0]['va_number'] ?? $notification['permata_va_number'] ?? null,
                    'transaction_time' => $notification['transaction_time'] ?? now(),
                ]
            );

            // Handle berbagai status transaksi
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $bill->update(['status' => 'challenge']);
                } else if ($fraudStatus == 'accept') {
                    $this->handlePembayaranBerhasil($bill);
                }
            } else if ($transactionStatus == 'settlement') {
                $this->handlePembayaranBerhasil($bill);
            } else if ($transactionStatus == 'pending') {
                $bill->update(['status' => 'pending']);
            } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $bill->update(['status' => 'failed']);
                // Kembalikan status properti ke Available jika pembayaran gagal
                $property = Property::find($bill->property_id);
                if ($property && $property->status === 'Pending') {
                    $property->update(['status' => 'Available']);
                }
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error webhook Midtrans: ' . $e->getMessage());
            return false;
        }
    }

    private function handlePembayaranBerhasil(Bill $bill)
    {
        // Update status bill
        $bill->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);

        // Update status properti menjadi 'Rented'
        $property = Property::find($bill->property_id);
        if ($property) {
            $property->update(['status' => 'Rented']);
            Log::info("Status properti {$property->name} berubah menjadi Rented");
        }

        // Buat record rental jika diperlukan
        $rental = Rental::updateOrCreate(
            [
                'property_id' => $bill->property_id,
                'renter_id' => $bill->renter_id,
            ],
            [
                'tenant_id' => $bill->tenant_id,
                'bill_id' => $bill->id,
                'start_date' => $bill->renter->start_date,
                'end_date' => $bill->renter->end_date,
                'amount' => $bill->amount,
                'status' => 'active',
            ]
        );

        Log::info("Pembayaran berhasil untuk Bill ID: {$bill->id}, Status properti berubah menjadi Rented");
    }

    public function createPayment(Bill $bill)
    {
        // Set server key Anda
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized', true);
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds', true);

        $params = [
            'transaction_details' => [
                'order_id' => 'BILL-' . $bill->id,
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
            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Error Midtrans: ' . $e->getMessage());
            throw $e;
        }
    }
}