<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoinRewardConversion extends Model
{
    protected $fillable = [
        'coin_to_reward_rate',
        'status',
    ];
}
