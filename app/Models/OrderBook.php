<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderBook extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer', 'invoice', 'invoice_dt', 'prefix', 'cgst', 'igst', 'sgst',
        'wac', 'value', 'charge', 'coupon', 'status', 'order_id', 'del_md', 'user', 'ref', 'ref1', 'company',
        'del_dt', 'finyear', 'cess', 'pack_user', 'warehouse', 'print', 'src_no', 'user_id','payment_status',
        'payment_mode', 'payment_ref', 'payment_amount',
        'delivery_address', 'delivery_pincode', 'delivery_landmark', 'delivery_latitude', 'delivery_longitude',
        'receiver_name', 'receiver_phone', 'delivery_instruction',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'order_book_id');
    }

    public function userDetail()
    {
        return $this->belongsTo(User::class, 'user_id' ,'id');
    }
}
