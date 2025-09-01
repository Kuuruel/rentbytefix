<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'default_priority',
        'default_delivery_methods',
        'email_from',
        'email_footer',
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

    // Relasi dengan User (yang update settings)
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Get current settings atau buat default jika belum ada
    public static function getCurrentSettings()
    {
        $settings = self::first();

        if (!$settings) {
            $settings = self::create([
                'default_priority' => 'Normal',
                'default_delivery_methods' => ['Dashboard'],
                'email_from' => 'admin@system.com',
                'email_footer' => 'System Administrator',
                'push_enabled' => true,
                'dashboard_display_count' => 5,
                'updated_by' => auth()->id() ?? 1
            ]);
        }

        return $settings;
    }

    // Update or create settings
    public static function updateSettings($data)
    {
        $settings = self::first();
        $data['updated_by'] = auth()->id();

        if ($settings) {
            $settings->update($data);
            return $settings;
        } else {
            return self::create($data);
        }
    }

    // Get default values untuk form
    public function getDefaultsForForm()
    {
        return [
            'priority' => $this->default_priority,
            'delivery_methods' => $this->default_delivery_methods,
            'email_from' => $this->email_from,
            'email_footer' => $this->email_footer,
            'push_enabled' => $this->push_enabled,
            'display_count' => $this->dashboard_display_count
        ];
    }
}
