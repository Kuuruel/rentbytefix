<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MidtransDraft extends Model
{
    use HasFactory;

    protected $table = 'midtrans_drafts';

    protected $fillable = [
        'user_id',
        'draft_data',
    ];

    protected $casts = [
        'draft_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accessor untuk mendapatkan data draft sebagai array
    public function getDraftDataAttribute($value)
    {
        return json_decode($value, true);
    }

    // Mutator untuk menyimpan data draft sebagai JSON
    public function setDraftDataAttribute($value)
    {
        $this->attributes['draft_data'] = is_array($value) ? json_encode($value) : $value;
    }
}
