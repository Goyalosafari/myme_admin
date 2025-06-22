<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;
    protected $table = 'foods';

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }
    
        public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function recipe()
    {
        return $this->hasMany(Recipe::class);
    }
}
