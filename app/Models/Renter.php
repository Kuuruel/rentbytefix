<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renter extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',  // Milik tenant mana (pemilik properti)
        'property_id',
        'name',
        'phone', 
        'email',
        'address',
        'unit_id',      // Dari struktur database
        'start_date',   // Dari struktur database
        'end_date'      // Dari struktur database
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relasi ke Bills (satu renter bisa punya banyak bills)
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    // Relasi ke User/Tenant (pemilik properti)
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    // Relasi ke Properties (melalui bills)
    public function properties()
    {
        return $this->hasManyThrough(
            Property::class,
            Bill::class,
            'renter_id',     // Foreign key on bills table
            'id',            // Foreign key on properties table  
            'id',            // Local key on renters table
            'property_id'    // Local key on bills table
        );
    }

    // Accessor untuk format nama
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    // Accessor untuk format phone
    public function getFormattedPhoneAttribute()
    {
        return $this->phone ? '+62' . ltrim($this->phone, '0') : null;
    }

    // Scope untuk filter by tenant
    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    // Scope untuk renter yang pernah bayar
    public function scopeWithPaidRentals($query)
    {
        return $query->whereHas('bills', function ($q) {
            $q->where('status', 'paid');
        });
    }

    // Method untuk cek apakah renter ini pernah sewa properti tertentu
    public function hasRentedProperty($propertyId)
    {
        return $this->bills()
                   ->where('property_id', $propertyId)
                   ->where('status', 'paid')
                   ->exists();
    }

    // Method untuk total pembayaran dari renter ini
    public function getTotalPaymentsAttribute()
    {
        return $this->bills()
                   ->where('status', 'paid')
                   ->sum('amount');
    }

    // Method untuk rental history
    public function rentalHistory()
    {
        return $this->bills()
                   ->with(['property'])
                   ->where('status', 'paid')
                   ->orderBy('created_at', 'desc');
    }

    public function property()
{
    return $this->belongsTo(Property::class);
}


}