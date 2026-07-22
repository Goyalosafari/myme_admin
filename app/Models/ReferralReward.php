<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralReward extends Model
{
    protected $fillable = [
        'referrer_user_id',
        'referred_user_id',
        'order_book_id',
        'amount',
    ];
}
