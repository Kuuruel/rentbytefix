<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'bill_id',
        'order_id', 
        'amount',
        'status',
        'paid_at',
        'midtrans_response',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'midtrans_response' => 'array',
        'amount' => 'decimal:2'
    ];

    protected $dates = [
        'paid_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Relationship to Bill
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Get renter through bill relationship
     */
    public function getRenterAttribute()
    {
        return $this->bill?->renter;
    }

    /**
     * Get property through bill relationship  
     */
    public function getPropertyAttribute()
    {
        return $this->bill?->property;
    }

    /**
     * Format amount as currency
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'success' => 'bg-success-100 text-success-600',
            'pending' => 'bg-warning-100 text-warning-600', 
            'failed' => 'bg-danger-100 text-danger-600',
            default => 'bg-neutral-100 text-neutral-600'
        };
    }

    public function renter()
{
    return $this->hasOneThrough(Renter::class, Bill::class, 'id', 'id', 'bill_id', 'renter_id');
}
}