<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable= [
        'food_id', 'user_id', 'ratings', 'feedback'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
