<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    protected $table = 'time_slots';
    protected $fillable = [
        'time_slot',
        'cutoff',
        'time_start',
        'time_end',
        'ref',
        'ref1',
        'status',
    ];
}
