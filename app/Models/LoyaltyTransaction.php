<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'order_book_id',
        'points',
        'type',
        'description',
    ];

    public static function balanceFor(int $userId): int
    {
        return (int) static::where('user_id', $userId)->sum('points');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
