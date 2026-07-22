<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltySetting extends Model
{
    protected $fillable = [
        'points_per_amount',
        'min_points_to_convert',
    ];

    public static function current(): self
    {
        return static::first() ?? static::create([
            'points_per_amount'     => 100,
            'min_points_to_convert' => 50,
        ]);
    }
}
