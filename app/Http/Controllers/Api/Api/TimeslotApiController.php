<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TimeslotApiController extends Controller
{
    public function index()
    {
        $data = TimeSlot::all();
        return response()->json(['data' => $data]);
    }

    public function getTimeSlotByDate(Request $request)
    {
        if($request->date == Carbon::now()->format('Y-m-d'))
        { 
            $data = TimeSlot::where('ref1', '>' ,$request->ref1)->get();
        }else{
            $data = TimeSlot::get();
        }
        return response()->json(['data'=>$data]);
    }
}
