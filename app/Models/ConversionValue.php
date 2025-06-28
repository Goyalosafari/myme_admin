<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversionValue extends Model
{
    protected $fillable = [
        'coin_conversion_rate',
        'referrer_reward_points',
        'referee_reward_points',
        'minimum_applicable_amount', // Added this field
        'status',
    ];
}
