<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends Model
{
    use HasFactory, SoftDeletes;

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function food()
    {
        return $this->belongsTo(Food::class, 'food_id');
    }
}
