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

    // Accessor untuk server key (untuk keamanan)
    public function getServerKeyAttribute($value)
    {
        return $value;
    }

    // Mutator untuk server key (enkripsi sederhana jika diperlukan)
    public function setServerKeyAttribute($value)
    {
        $this->attributes['server_key'] = $value;
    }

    // Method untuk cek apakah environment production
    public function isProduction()
    {
        return $this->environment === 'production';
    }

    // Method untuk cek apakah environment sandbox
    public function isSandbox()
    {
        return $this->environment === 'sandbox';
    }

    // Method untuk mendapatkan base URL berdasarkan environment
    public function getBaseUrl()
    {
        return $this->environment === 'production'
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }
}
