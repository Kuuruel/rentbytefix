<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MidtransSetting extends Model
{
    use HasFactory;

    protected $table = 'midtrans_settings';

    protected $fillable = [
        'merchant_id',
        'client_key',
        'server_key',
        'environment',
        'webhook_url',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getConfig()
    {
        $setting = self::first();

        if (!$setting) {
            return null;
        }

        return [
            'merchant_id' => $setting->merchant_id,
            'client_key' => $setting->client_key,
            'server_key' => $setting->server_key,
            'environment' => $setting->environment,
            'webhook_url' => $setting->webhook_url,
            'is_production' => $setting->environment === 'production',
        ];
    }
}
