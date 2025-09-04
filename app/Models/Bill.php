<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'renter_id', 
        'order_id',
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

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function renter()
    {
        return $this->belongsTo(Renter::class, 'renter_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getRenterNameAttribute()
    {
        return $this->renter ? $this->renter->name : null;
    }

    public function getRenterEmailAttribute()
    {
        return $this->renter ? $this->renter->email : null;
    }
 
    public function getRenterPhoneAttribute()
    {
        return $this->renter ? $this->renter->phone : null;
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getIsExpiredAttribute()
    {
        return $this->due_date < now() && $this->status !== 'paid';
    }

    public function latestTransaction()
    {
        return $this->hasOne(Transaction::class)->latestOfMany();
    }

    public function successfulTransaction()
    {
        return $this->hasOne(Transaction::class)->where('status', Transaction::STATUS_SUCCESS);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bill) {
            if (empty($bill->order_id)) {
                $bill->order_id = 'BILL-' . uniqid();
            }
        });
    }

    public static function getTransactionsByTenant($tenantId)
    {
        return Transaction::with(['bill.renter', 'bill.property'])
            ->whereHas('bill', function ($q) use ($tenantId) {
                $q->where('tenant_id', Auth::user()->tenant_id);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }
}