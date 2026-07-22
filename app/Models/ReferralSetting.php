<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralSetting extends Model
{
    protected $fillable = [
        'reward_amount',
    ];

    public static function current(): self
    {
        return static::first() ?? static::create(['reward_amount' => 100.00]);
    }
}
