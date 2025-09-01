<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bill_id',
        'midtrans_response',
        'status',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'midtrans_response' => 'array',
        'paid_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    /**
     * Get all available status options
     *
     * @return array
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SUCCESS => 'Success',
            self::STATUS_FAILED => 'Failed',
        ];
    }

    /**
     * Relationship: Transaction belongs to Bill
     *
     * @return BelongsTo
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Scope: Get successful transactions
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    /**
     * Scope: Get pending transactions
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope: Get failed transactions
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Check if transaction is successful
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    /**
     * Check if transaction is pending
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if transaction is failed
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Mark transaction as successful
     *
     * @return bool
     */
    public function markAsSuccessful(): bool
    {
        return $this->update([
            'status' => self::STATUS_SUCCESS,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark transaction as failed
     *
     * @return bool
     */
    public function markAsFailed(): bool
    {
        return $this->update([
            'status' => self::STATUS_FAILED,
        ]);
    }

    /**
     * Get formatted paid at date
     *
     * @return string|null
     */
    public function getFormattedPaidAtAttribute(): ?string
    {
        return $this->paid_at ? $this->paid_at->format('d M Y H:i:s') : null;
    }

    /**
     * Get status badge color for UI
     *
     * @return string
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_SUCCESS => 'success',
            self::STATUS_FAILED => 'danger',
            default => 'secondary',
        };
    }
}