<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;


class Tenants extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'note',
        'status',
        'avatar',
        'user_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'tenant_id');
    }


    public function getAvatarAttribute($value)
    {
        if ($value) {
            return $value;
        }
        return asset('assets/images/user-list/user-list1.png');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'Inactive');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', '%' . $term . '%')
              ->orWhere('email', 'like', '%' . $term . '%');
        });
    }

    public function properties()
{
    return $this->hasMany(Property::class);
}

// Di model Tenant
protected static function booted()
{
    static::created(function ($tenant) {
        User::create([
            'name' => $tenant->name,
            'email' => $tenant->email,
            'password' => Hash::make($tenant->password),
            'role' => 'tenants',
            'tenant_id' => $tenant->id
        ]);
    });
}


}