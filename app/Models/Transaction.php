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

    // 🔥 PLATFORM FEE CONSTANTS
    const PLATFORM_FEE_PERCENTAGE = 5; // 5%
    const PAYMENT_GATEWAY_FEE = 2500; // Rp 2.500

    protected $fillable = [
        'bill_id',
        'tenant_id',
        'property_id',
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

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function getRenterAttribute()
    {
        return $this->bill?->renter;
    }

    public function getPropertyAttribute()
    {
        return $this->bill?->property;
    }

    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
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

    // 🔥 NEW: Platform Revenue Methods

    /**
     * Hitung platform fee untuk transaksi ini
     */
    public function getPlatformFeeAttribute()
    {
        if ($this->status !== self::STATUS_SUCCESS) {
            return 0;
        }

        $percentageFee = ($this->amount * self::PLATFORM_FEE_PERCENTAGE) / 100;
        $flatFee = self::PAYMENT_GATEWAY_FEE;

        return $percentageFee + $flatFee;
    }

    /**
     * Hitung platform fee dengan format rupiah
     */
    public function getFormattedPlatformFeeAttribute()
    {
        return 'Rp ' . number_format($this->platform_fee, 0, ',', '.');
    }

    /**
     * Static method: Total platform revenue untuk periode tertentu
     */
    public static function getPlatformRevenue($startDate = null, $endDate = null)
    {
        $query = static::where('status', self::STATUS_SUCCESS);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $transactions = $query->get();
        $totalRevenue = 0;

        foreach ($transactions as $transaction) {
            $totalRevenue += $transaction->platform_fee;
        }

        return $totalRevenue;
    }

    /**
     * Static method: Platform revenue summary untuk dashboard
     */
    public static function getRevenueSummary()
    {
        $last30Days = static::getPlatformRevenue(now()->subDays(30), now());
        $last60_30Days = static::getPlatformRevenue(now()->subDays(60), now()->subDays(30));
        $thisMonth = static::getPlatformRevenue(now()->startOfMonth(), now());
        $lastMonth = static::getPlatformRevenue(
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        );

        return [
            'last_30_days' => [
                'amount' => $last30Days,
                'formatted' => 'Rp ' . number_format($last30Days, 0, ',', '.')
            ],
            'previous_30_days' => [
                'amount' => $last60_30Days,
                'formatted' => 'Rp ' . number_format($last60_30Days, 0, ',', '.')
            ],
            'this_month' => [
                'amount' => $thisMonth,
                'formatted' => 'Rp ' . number_format($thisMonth, 0, ',', '.')
            ],
            'last_month' => [
                'amount' => $lastMonth,
                'formatted' => 'Rp ' . number_format($lastMonth, 0, ',', '.')
            ],
            'growth_30_days' => $last60_30Days > 0
                ? (($last30Days - $last60_30Days) / $last60_30Days) * 100
                : ($last30Days > 0 ? 100 : 0),
            'growth_monthly' => $lastMonth > 0
                ? (($thisMonth - $lastMonth) / $lastMonth) * 100
                : ($thisMonth > 0 ? 100 : 0),
        ];
    }

    /**
     * Static method: Platform revenue per bulan untuk chart
     */
    public static function getMonthlyPlatformRevenue($year = null)
    {
        $year = $year ?? date('Y');

        $transactions = static::selectRaw('
                MONTH(created_at) as month, 
                SUM(amount) as total_transaction_value,
                COUNT(*) as transaction_count
            ')
            ->where('status', self::STATUS_SUCCESS)
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyRevenue = [];

        foreach ($transactions as $data) {
            $totalValue = $data->total_transaction_value;
            $transactionCount = $data->transaction_count;

            $percentageFee = ($totalValue * self::PLATFORM_FEE_PERCENTAGE) / 100;
            $flatFees = $transactionCount * self::PAYMENT_GATEWAY_FEE;
            $platformRevenue = $percentageFee + $flatFees;

            $monthlyRevenue[] = [
                'month' => $data->month,
                'month_name' => date('F', mktime(0, 0, 0, $data->month, 1)),
                'transaction_count' => $transactionCount,
                'total_transaction_value' => (float) $totalValue,
                'platform_revenue' => (float) $platformRevenue,
                'formatted_revenue' => 'Rp ' . number_format($platformRevenue, 0, ',', '.'),
                'revenue_percentage' => $totalValue > 0 ? ($platformRevenue / $totalValue) * 100 : 0
            ];
        }

        return $monthlyRevenue;
    }

    /**
     * Scope: Only successful transactions
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    /**
     * Scope: Last N days
     */
    public function scopeLastDays($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
