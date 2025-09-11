<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'default_priority',
        'default_delivery_methods',
        'push_enabled',
        'dashboard_display_count',
        'updated_by'
    ];

    protected $casts = [
        'default_delivery_methods' => 'array',
        'push_enabled' => 'boolean',
        'dashboard_display_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    
    public static function getCurrentSettings()
    {
        $settings = self::first();

        if (!$settings) {
            $settings = self::create([
                'default_priority' => 'Normal',
                'default_delivery_methods' => ['Dashboard'],
                'push_enabled' => true,
                'dashboard_display_count' => 5,
                'updated_by' => Auth::id() ?? 1
            ]);
        }

        return $settings;
    }

    
    public static function updateSettings($data)
    {
        $settings = self::first();
        $data['updated_by'] = Auth::id() ?? 1;

        if ($settings) {
            $settings->update($data);
            return $settings->fresh(); 
        } else {
            return self::create($data);
        }
    }

    
    public function getDefaultsForForm()
    {
        return [
            'default_priority' => $this->default_priority,
            'default_delivery_methods' => $this->default_delivery_methods,
            'push_enabled' => $this->push_enabled,
            'display_count' => $this->dashboard_display_count
        ];
    }
}