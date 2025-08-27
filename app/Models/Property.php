<?php
// app/Models/Property.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'type', 
        'address',
        'price',
        'rent_type', 
        'status'
    ];

    public function setRentTypeAttribute($value)
    {
        $this->attributes['rent_type'] = strtolower($value);
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = strtolower($value);
    }

    public function getRentTypeAttribute($value)
    {
        return ucfirst($value);
    }

    public function getStatusAttribute($value)
    {
        return ucfirst($value);
    }

    protected $casts = [
        'price' => 'decimal:2'
    ];

    public function scopeAvailable($query)
    {
        return $query->where('status', 'Available');
    }

    public function scopeRented($query)
    {
        return $query->where('status', 'Rented');
    }

    public function tenant()
{
    return $this->belongsTo(Tenant::class);
}
}