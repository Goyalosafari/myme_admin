<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use Illuminate\Http\Request;

class TimeSlotApiController extends Controller
{
    public function index(Request $request)
    {
        $slots = TimeSlot::all();
        return response(['time_slots' => $slots], 200);
    }
}
