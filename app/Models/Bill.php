<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'renter_id', 
        'property_id',
        'amount',
        'due_date',
        'payment_link',
        'status'
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relasi ke User/Tenant (pemilik properti)
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    // Relasi ke Renter (penyewa)
    public function renter()
    {
        return $this->belongsTo(Renter::class, 'renter_id');
    }

    // Relasi ke Property
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    // Relasi ke Transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Accessor untuk nama renter (dari relasi)
    public function getRenterNameAttribute()
    {
        return $this->renter ? $this->renter->name : null;
    }

    // Accessor untuk email renter
    public function getRenterEmailAttribute()
    {
        return $this->renter ? $this->renter->email : null;
    }

    // Accessor untuk phone renter  
    public function getRenterPhoneAttribute()
    {
        return $this->renter ? $this->renter->phone : null;
    }

    // Scope untuk filter by status
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope untuk filter by tenant
    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    // Method untuk format amount
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    // Method untuk cek apakah sudah expired
    public function getIsExpiredAttribute()
    {
        return $this->due_date < now() && $this->status !== 'paid';
    }

    // Method untuk latest transaction

    public function latestTransaction()
    {
        return $this->hasOne(Transaction::class)->latestOfMany();
    }

    public function successfulTransaction()
    {
        return $this->hasOne(Transaction::class)->where('status', Transaction::STATUS_SUCCESS);
    }
}