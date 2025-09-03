<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renter extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'property_id',
        'name',
        'phone', 
        'email',
        'address',
        'unit_id',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function properties()
    {
        return $this->hasManyThrough(
            Property::class,
            Bill::class,
            'renter_id',
            'id',
            'id',
            'property_id'
        );
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getFormattedPhoneAttribute()
    {
        return $this->phone ? '+62' . ltrim($this->phone, '0') : null;
    }

    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeWithPaidRentals($query)
    {
        return $query->whereHas('bills', function ($q) {
            $q->where('status', 'paid');
        });
    }

    public function hasRentedProperty($propertyId)
    {
        return $this->bills()
                   ->where('property_id', $propertyId)
                   ->where('status', 'paid')
                   ->exists();
    }

    public function getTotalPaymentsAttribute()
    {
        return $this->bills()
                   ->where('status', 'paid')
                   ->sum('amount');
    }

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