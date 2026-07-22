<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TimeslotResource;
use App\Models\TimeSlot;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimeslotApiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        return $this->success(TimeslotResource::collection(TimeSlot::all()));
    }

    public function getTimeSlotByDate(Request $request)
    {
        $now = Carbon::now();

        if ($request->date === $now->toDateString()) {
            $slots = TimeSlot::where('ref1', '>', $now->format('H'))->get();
        } else {
            $slots = TimeSlot::all();
        }
<<<<<<< HEAD
        return response()->json(['data'=>$data]);
    }*/
    
        public function getTimeSlotByDate(Request $request)
    {
        
        $date = Carbon::now();
        if($request->date == $date->format('Y-m-d'))
        { 
            $time = $date->format('H');//dd($time);
            $data = TimeSlot::where('ref1', '>' ,$time)->where('status','y')->get();
        }else{
            $data = TimeSlot::where('status','y')->get();
        }
        return response()->json($data,200);
       // return response()->json(['data'=>$data]);
        
         //$location = Location::all();
       // return response()->json($location,200);
=======

        return $this->success(TimeslotResource::collection($slots));
>>>>>>> main
    }
}
