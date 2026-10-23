<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponHold extends Model
{
    protected $fillable = ['coupen_id', 'user_id', 'expires_at'];

    protected $casts = ['expires_at' => 'datetime'];

    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }
}
