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
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getServerKeyAttribute($value)
    {
        return $value;
    }

    public function setServerKeyAttribute($value)
    {
        $this->attributes['server_key'] = $value;
    }

    public function isProduction()
    {
        return $this->environment === 'production';
    }

    public function isSandbox()
    {
        return $this->environment === 'sandbox';
    }

    public function getBaseUrl()
    {
        return $this->environment === 'production'
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }
}
