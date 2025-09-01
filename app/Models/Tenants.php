<?php
// app/Models/Tenants.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenants extends Model
{
    use HasFactory;

    /**
     * Field yang bisa diisi secara mass assignment
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'note',
        'status',
        'avatar',
        'country',
        'user_id'
    ];

    /**
     * Field yang disembunyikan dalam serialization
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data untuk field tertentu
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relasi dengan model User
     * Setiap tenant dimiliki oleh satu user
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Accessor untuk field avatar
     * Jika tidak ada avatar, return default image
     */
    public function getAvatarAttribute($value)
    {
        if ($value) {
            return $value;
        }
        return asset('assets/images/user-list/user-list1.png');
    }

    /**
     * Scope untuk filter tenant yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope untuk filter tenant yang tidak aktif
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'Inactive');
    }

    /**
     * Scope untuk pencarian berdasarkan nama atau email
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', '%' . $term . '%')
                ->orWhere('email', 'like', '%' . $term . '%');
        });
    }

    /**
     * Scope untuk filter berdasarkan negara
     */
    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Accessor untuk mendapatkan status dalam bahasa Indonesia
     */
    public function getStatusLabelAttribute()
    {
        return $this->status === 'Active' ? 'Aktif' : 'Tidak Aktif';
    }

    /**
     * Mendapatkan jumlah tenant berdasarkan status
     */
    public static function getCountByStatus()
    {
        return [
            'active' => self::active()->count(),
            'inactive' => self::inactive()->count(),
            'total' => self::count()
        ];
    }
}