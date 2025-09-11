<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenants;
use Carbon\Carbon;

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

    // 🔥 ANTI-DUPLICATE: Validation sebelum save
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bill) {
            // Generate order_id jika kosong
            if (empty($bill->order_id)) {
                $bill->order_id = 'BILL-' . uniqid();
            }
            
            // 🎯 CEK DUPLICATE SEBELUM INSERT
            $existingBill = static::where('tenant_id', $bill->tenant_id)
                ->where('renter_id', $bill->renter_id)
                ->where('property_id', $bill->property_id)
                ->where('amount', $bill->amount)
                ->whereDate('due_date', Carbon::parse($bill->due_date)->format('Y-m-d'))
                ->whereIn('status', ['pending', 'paid']) // Cek yang belum expired/failed
                ->first();

            if ($existingBill) {
                // 🚨 JANGAN INSERT DUPLICATE!
                throw new \Exception("Duplicate bill detected for tenant {$bill->tenant_id}. Existing bill ID: {$existingBill->id}");
            }
        });

        static::updating(function ($bill) {
            // Cek duplicate saat update juga (kecuali untuk record yang sama)
            $existingBill = static::where('tenant_id', $bill->tenant_id)
                ->where('renter_id', $bill->renter_id)
                ->where('property_id', $bill->property_id)
                ->where('amount', $bill->amount)
                ->whereDate('due_date', Carbon::parse($bill->due_date)->format('Y-m-d'))
                ->whereIn('status', ['pending', 'paid'])
                ->where('id', '!=', $bill->id) // Exclude current record
                ->first();

            if ($existingBill) {
                throw new \Exception("Duplicate bill detected during update for tenant {$bill->tenant_id}. Existing bill ID: {$existingBill->id}");
            }
        });
    }

    // 🎯 STATIC METHOD: Cek duplicate sebelum create (untuk controller)
    public static function checkDuplicate($tenantId, $renterId, $propertyId, $amount, $dueDate)
    {
        return static::where('tenant_id', $tenantId)
            ->where('renter_id', $renterId)
            ->where('property_id', $propertyId)
            ->where('amount', $amount)
            ->whereDate('due_date', Carbon::parse($dueDate)->format('Y-m-d'))
            ->whereIn('status', ['pending', 'paid'])
            ->exists();
    }

    // 🎯 STATIC METHOD: Create dengan validasi duplicate
    public static function createSafely(array $data)
    {
        if (static::checkDuplicate(
            $data['tenant_id'],
            $data['renter_id'] ?? null,
            $data['property_id'] ?? null,
            $data['amount'],
            $data['due_date']
        )) {
            return [
                'success' => false,
                'message' => 'Duplicate bill detected. Bill with same details already exists.',
                'bill' => null
            ];
        }

        try {
            $bill = static::create($data);
            return [
                'success' => true,
                'message' => 'Bill created successfully.',
                'bill' => $bill
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error creating bill: ' . $e->getMessage(),
                'bill' => null
            ];
        }
    }

    public function tenant()
    {
        return $this->belongsTo(Tenants::class, 'tenant_id', 'id');
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

    // 🎯 HELPER: Cari transaction yang berhasil
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
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

    public static function getTransactionsByTenant($tenantId)
    {
        return Transaction::with(['bill.renter', 'bill.property'])
            ->whereHas('bill', function ($q) use ($tenantId) {
                $q->where('tenant_id', Auth::user()->tenant_id);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // 🎯 SCOPE: Untuk debugging duplicate bills
    public function scopeDuplicates($query)
    {
        return $query->select('tenant_id', 'renter_id', 'property_id', 'amount', 'due_date')
            ->selectRaw('COUNT(*) as duplicate_count')
            ->groupBy('tenant_id', 'renter_id', 'property_id', 'amount', 'due_date')
            ->having('duplicate_count', '>', 1);
    }

    // 🎯 METHOD: Clean existing duplicates
    public static function cleanDuplicates()
    {
        $duplicates = static::select('tenant_id', 'renter_id', 'property_id', 'amount', 'due_date')
            ->selectRaw('COUNT(*) as duplicate_count, GROUP_CONCAT(id) as ids')
            ->groupBy('tenant_id', 'renter_id', 'property_id', 'amount', 'due_date')
            ->having('duplicate_count', '>', 1)
            ->get();

        $deletedCount = 0;
        foreach ($duplicates as $duplicate) {
            $ids = explode(',', $duplicate->ids);
            $keepId = array_shift($ids); // Keep first one
            
            // Delete the rest
            static::whereIn('id', $ids)->delete();
            $deletedCount += count($ids);
        }

        return $deletedCount;
    }
}