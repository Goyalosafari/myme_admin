<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [ 
        'mobile',
        'user',
        'name',
        'order',
        'phone_pe_order_id',
        'transactionId',
        'merchantId',
        'amount',
        'status'
    ];
}
