<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rating extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable= [
        'food_id', 'order_id', 'user_id', 'ratings', 'feedback'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
