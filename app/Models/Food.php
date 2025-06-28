<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Food extends Model
{
    use HasFactory;
    protected $table = 'foods';

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function recipe()
    {
        return $this->hasMany(Recipe::class);
    }

    protected static function booted()
    {
        static::updated(function ($food) {
            // Only trigger if offer_price changed
            if ($food->isDirty('offer_price')) {
                Order::where('food_id', $food->id)
                    ->where('status', 'cart') // Optional: update only cart items
                    ->update([
                        'price' => $food->offer_price,
                        'total' => DB::raw("qty * {$food->offer_price}"),
                    ]);
            }
        });
    }

}
