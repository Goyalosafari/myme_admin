<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    public function food()
    {
        return $this->hasMany(Food::class);
    }
     public function foods()
    {
        return $this->belongsToMany(Food::class);
    }
}
