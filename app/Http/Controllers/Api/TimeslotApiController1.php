<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use Illuminate\Http\Request;

class TimeslotApiController extends Controller
{
    public function index()
    {
        $data = TimeSlot::all();
        return response()->json(['data' => $data]);
    }
}
