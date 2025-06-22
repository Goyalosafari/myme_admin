<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'food_id', 'qty', 'price', 'discount',
        'final_price', 'total', 'dt_from', 'dt_to', 'user_id', 'order_id', 'order_book_id',
        'finyear', 'cgst', 'igst', 'sgst', 'gst_value', 'cess', 'net_price', 'invoice_id',
        'time_slot', 'status'
    ];
    public function food()
    {
        return $this->belongsTo(Food::class,'food_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id' ,'id');
    }

    public function time_slot()
    {
        return $this->belongsTo(TimeSlot::class, 'id');
    }
}
