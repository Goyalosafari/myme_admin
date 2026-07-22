<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeSlot extends Model
{
<<<<<<< HEAD
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
=======
    use HasFactory, SoftDeletes;
>>>>>>> main
}
