<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FoodRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'food_id',
        'order_id',
        'rating',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
