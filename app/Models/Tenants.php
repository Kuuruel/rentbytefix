<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Tenants extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'note',
        'status',
        'avatar',
        'country',
        'country',
        'user_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
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

    public function isActive()
    {
        return $this->status === 'Active';
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return $this->{$this->getRememberTokenName()};
    }

    public function setRememberToken($value)
    {
        $this->{$this->getRememberTokenName()} = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    public function getStatusLabelAttribute()
    {
        return $this->status === 'Active' ? 'Aktif' : 'Tidak Aktif';
    }

    public static function getCountByStatus()
    {
        return [
            'active' => self::active()->count(),
            'inactive' => self::inactive()->count(),
            'total' => self::count()
        ];
    }
}