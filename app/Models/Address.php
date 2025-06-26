<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['user_id', 'address', 'pincode', 'landmark', 'name', 'instruction', 'phone', 'status', 'type'];
}