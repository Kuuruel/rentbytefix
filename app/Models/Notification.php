<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'priority',
        'delivery_methods',
        'target_type',
        'target_tenant_ids',
        'is_archived',
        'created_by'
    ];

    protected $casts = [
        'delivery_methods' => 'array',
        'target_tenant_ids' => 'array',
        'is_archived' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi dengan User (yang buat notification)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi dengan notification reads
    public function reads()
    {
        return $this->hasMany(NotificationRead::class);
    }

    // Scope untuk notification aktif (tidak diarsip)
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    // Scope untuk notification yang diarsip
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    // Scope untuk filter berdasarkan priority
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Scope untuk filter berdasarkan target type
    public function scopeByTargetType($query, $type)
    {
        return $query->where('target_type', $type);
    }

    // Cek apakah notification sudah dibaca oleh user tertentu
    public function isReadBy($userId, $tenantId = null)
    {
        return $this->reads()
            ->where('user_id', $userId)
            ->when($tenantId, function ($query, $tenantId) {
                return $query->where('tenant_id', $tenantId);
            })
            ->exists();
    }

    // Mark notification sebagai dibaca
    public function markAsReadBy($userId, $tenantId = null)
    {
        return $this->reads()->updateOrCreate(
            [
                'user_id' => $userId,
                'tenant_id' => $tenantId
            ],
            [
                'read_at' => now()
            ]
        );
    }

    // Cek apakah notification berlaku untuk tenant tertentu
    public function isForTenant($tenantId)
    {
        if ($this->target_type === 'all') {
            return true;
        }

        if ($this->target_type === 'specific' && $this->target_tenant_ids) {
            return in_array($tenantId, $this->target_tenant_ids);
        }

        return false;
    }

    // Get target audience sebagai string
    public function getTargetAudienceAttribute()
    {
        if ($this->target_type === 'all') {
            return 'All Tenants';
        }

        if ($this->target_type === 'specific' && $this->target_tenant_ids) {
            $tenantNames = Tenants::whereIn('id', $this->target_tenant_ids)
                ->pluck('name')
                ->take(3) // Ambil 3 nama pertama
                ->toArray();

            $count = count($this->target_tenant_ids);
            $namesString = implode(', ', $tenantNames);

            if ($count > 3) {
                return $namesString . ' +' . ($count - 3) . ' more';
            }

            return $namesString;
        }

        return 'Unknown';
    }

    // Get priority badge class
    public function getPriorityBadgeAttribute()
    {
        return match ($this->priority) {
            'Normal' => 'bg-blue-400 text-blue-600 dark:bg-blue-600/25 dark:text-blue-400',
            'Important' => 'bg-warning-100 text-warning-600 dark:bg-warning-600/25 dark:text-warning-400',
            'Critical' => 'bg-danger-100 text-danger-600 dark:bg-danger-600/25 dark:text-danger-400',
            default => 'bg-gray-100 text-gray-600 dark:bg-gray-600/25 dark:text-gray-400'
        };
    }

    // Get delivery methods sebagai string
    public function getDeliveryMethodsStringAttribute()
    {
        return is_array($this->delivery_methods)
            ? implode(', ', $this->delivery_methods)
            : $this->delivery_methods;
    }

    // Get notifications untuk user tertentu
    public static function getForUser($userId, $tenantId = null, $limit = 5)
    {
        return self::active()
            ->where(function ($query) use ($tenantId) {
                $query->where('target_type', 'all');

                if ($tenantId) {
                    $query->orWhere(function ($q) use ($tenantId) {
                        $q->where('target_type', 'specific')
                            ->whereJsonContains('target_tenant_ids', $tenantId);
                    });
                }
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    // Get unread count untuk user
    public static function getUnreadCountForUser($userId, $tenantId = null)
    {
        $notifications = self::getForUser($userId, $tenantId, 100); // Get more for counting

        return $notifications->filter(function ($notification) use ($userId, $tenantId) {
            return !$notification->isReadBy($userId, $tenantId);
        })->count();
    }
}
