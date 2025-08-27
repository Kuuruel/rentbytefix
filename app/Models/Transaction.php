<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'midtrans_response',
        'status',
        'paid_at'
    ];

    protected $casts = [
        'midtrans_response' => 'json',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SETTLEMENT = 'settlement';
    const STATUS_SUCCESS = 'success';
    const STATUS_CAPTURE = 'capture';
    const STATUS_DENY = 'deny';
    const STATUS_CANCEL = 'cancel';
    const STATUS_EXPIRE = 'expire';
    const STATUS_FAILURE = 'failure';

    // Relasi ke Bill
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    // Accessor untuk Midtrans Order ID
    public function getOrderIdAttribute()
    {
        if ($this->midtrans_response && isset($this->midtrans_response['order_id'])) {
            return $this->midtrans_response['order_id'];
        }
        return null;
    }

    // Accessor untuk Transaction ID dari Midtrans
    public function getTransactionIdAttribute()
    {
        if ($this->midtrans_response && isset($this->midtrans_response['transaction_id'])) {
            return $this->midtrans_response['transaction_id'];
        }
        return null;
    }

    // Accessor untuk Payment Type
    public function getPaymentTypeAttribute()
    {
        if ($this->midtrans_response && isset($this->midtrans_response['payment_type'])) {
            return $this->midtrans_response['payment_type'];
        }
        return null;
    }

    // Accessor untuk Gross Amount
    public function getGrossAmountAttribute()
    {
        if ($this->midtrans_response && isset($this->midtrans_response['gross_amount'])) {
            return (float) $this->midtrans_response['gross_amount'];
        }
        return 0;
    }

    // Accessor untuk Bank (jika transfer bank)
    public function getBankAttribute()
    {
        if ($this->midtrans_response && isset($this->midtrans_response['va_numbers'])) {
            return $this->midtrans_response['va_numbers'][0]['bank'] ?? null;
        }
        if ($this->midtrans_response && isset($this->midtrans_response['permata_va_number'])) {
            return 'permata';
        }
        if ($this->midtrans_response && isset($this->midtrans_response['bca_va_number'])) {
            return 'bca';
        }
        return null;
    }

    // Accessor untuk VA Number
    public function getVaNumberAttribute()
    {
        if ($this->midtrans_response && isset($this->midtrans_response['va_numbers'])) {
            return $this->midtrans_response['va_numbers'][0]['va_number'] ?? null;
        }
        if ($this->midtrans_response && isset($this->midtrans_response['permata_va_number'])) {
            return $this->midtrans_response['permata_va_number'];
        }
        if ($this->midtrans_response && isset($this->midtrans_response['bca_va_number'])) {
            return $this->midtrans_response['bca_va_number'];
        }
        return null;
    }

    // Scope untuk filter by status
    public function scopePaid($query)
    {
        return $query->whereIn('status', [
            self::STATUS_SETTLEMENT,
            self::STATUS_SUCCESS,
            self::STATUS_CAPTURE
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeFailed($query)
    {
        return $query->whereIn('status', [
            self::STATUS_DENY,
            self::STATUS_CANCEL,
            self::STATUS_EXPIRE,
            self::STATUS_FAILURE
        ]);
    }

    // Scope untuk filter by bill
    public function scopeByBill($query, $billId)
    {
        return $query->where('bill_id', $billId);
    }

    // Method untuk cek apakah transaksi sukses
    public function isSuccess()
    {
        return in_array($this->status, [
            self::STATUS_SETTLEMENT,
            self::STATUS_SUCCESS,
            self::STATUS_CAPTURE
        ]);
    }

    // Method untuk cek apakah transaksi gagal
    public function isFailed()
    {
        return in_array($this->status, [
            self::STATUS_DENY,
            self::STATUS_CANCEL,
            self::STATUS_EXPIRE,
            self::STATUS_FAILURE
        ]);
    }

    // Method untuk cek apakah transaksi pending
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    // Method untuk format status
    public function getStatusLabelAttribute()
    {
        $labels = [
            self::STATUS_PENDING => 'Menunggu Pembayaran',
            self::STATUS_SETTLEMENT => 'Berhasil',
            self::STATUS_SUCCESS => 'Berhasil',
            self::STATUS_CAPTURE => 'Berhasil',
            self::STATUS_DENY => 'Ditolak',
            self::STATUS_CANCEL => 'Dibatalkan',
            self::STATUS_EXPIRE => 'Kadaluarsa',
            self::STATUS_FAILURE => 'Gagal'
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    // Method untuk format gross amount
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->gross_amount, 0, ',', '.');
    }

    // Static method untuk create dari Midtrans notification
    public static function createFromNotification($notification, $billId)
    {
        return self::create([
            'bill_id' => $billId,
            'midtrans_response' => $notification,
            'status' => $notification->transaction_status ?? self::STATUS_PENDING,
            'paid_at' => in_array($notification->transaction_status ?? '', [
                self::STATUS_SETTLEMENT,
                self::STATUS_SUCCESS,
                self::STATUS_CAPTURE
            ]) ? now() : null
        ]);
    }

    // Static method untuk update dari Midtrans notification
    public function updateFromNotification($notification)
    {
        $this->update([
            'midtrans_response' => $notification,
            'status' => $notification->transaction_status ?? $this->status,
            'paid_at' => in_array($notification->transaction_status ?? '', [
                self::STATUS_SETTLEMENT,
                self::STATUS_SUCCESS,
                self::STATUS_CAPTURE
            ]) ? ($this->paid_at ?? now()) : null
        ]);

        return $this;
    }
}