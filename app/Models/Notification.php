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

    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    
    public function reads()
    {
        return $this->hasMany(NotificationRead::class);
    }

    
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    
    public function scopeByTargetType($query, $type)
    {
        return $query->where('target_type', $type);
    }

    
    public function isReadBy($userId, $tenantId = null)
    {
        return $this->reads()
            ->where('user_id', $userId)
            ->when($tenantId, function ($query, $tenantId) {
                return $query->where('tenant_id', $tenantId);
            })
            ->exists();
    }

    
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

    
    public function getTargetAudienceAttribute()
    {
        if ($this->target_type === 'all') {
            return 'All Tenants';
        }

        if ($this->target_type === 'specific' && $this->target_tenant_ids) {
            $tenantNames = Tenants::whereIn('id', $this->target_tenant_ids)
                ->pluck('name')
                ->take(3) 
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

    
    public function getPriorityBadgeAttribute()
    {
        return match ($this->priority) {
            'Normal' => 'bg-blue-400 text-blue-600 dark:bg-blue-600/25 dark:text-blue-400',
            'Important' => 'bg-warning-100 text-warning-600 dark:bg-warning-600/25 dark:text-warning-400',
            'Critical' => 'bg-danger-100 text-danger-600 dark:bg-danger-600/25 dark:text-danger-400',
            default => 'bg-gray-100 text-gray-600 dark:bg-gray-600/25 dark:text-gray-400'
        };
    }

    
    public function getDeliveryMethodsStringAttribute()
    {
        return is_array($this->delivery_methods)
            ? implode(', ', $this->delivery_methods)
            : $this->delivery_methods;
    }

    
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

    
    public static function getUnreadCountForUser($userId, $tenantId = null)
    {
        $notifications = self::getForUser($userId, $tenantId, 100); 

        return $notifications->filter(function ($notification) use ($userId, $tenantId) {
            return !$notification->isReadBy($userId, $tenantId);
        })->count();
    }
    

    
    public static function getForTenant($tenantId, $limit = 5)
    {
        return self::active()
            ->where(function ($query) use ($tenantId) {
                
                $query->where('target_type', 'all');

                
                $query->orWhere(function ($q) use ($tenantId) {
                    $q->where('target_type', 'specific')
                        ->whereJsonContains('target_tenant_ids', (int)$tenantId);
                });
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    
    public static function getUnreadCountForTenant($tenantId)
    {
        $notifications = self::getForTenant($tenantId, 100);

        return $notifications->filter(function ($notification) use ($tenantId) {
            return !$notification->isReadByTenant($tenantId);
        })->count();
    }

    
    public function isReadByTenant($tenantId)
    {
        return $this->reads()
            ->where('tenant_id', $tenantId)
            ->whereNull('user_id') 
            ->exists();
    }

    
    public function markAsReadByTenant($tenantId)
    {
        return $this->reads()->updateOrCreate(
            [
                'user_id' => null,  
                'tenant_id' => $tenantId
            ],
            [
                'read_at' => now()
            ]
        );
    }
}
