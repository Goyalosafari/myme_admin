<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
//use Carbon\Carbon;
use Illuminate\Support\Carbon;

class TimeslotApiController extends Controller
{
    public function index()
    {
        $data = TimeSlot::all();
        return response()->json(['data' => $data]);
    }

  /*  public function getTimeSlotByDate(Request $request)
    {
        $date = Carbon::now();
        if($request->date == $date->format('Y-m-d'))
        { 
            $time = $date->format('H');
            $data = TimeSlot::where('ref1', '>' ,$time)->get();
        }else{
            $data = TimeSlot::get();
        }
        return response()->json(['data'=>$data]);
    }*/
    
        public function getTimeSlotByDate(Request $request)
    {
        
        $date = Carbon::now();
        if($request->date == $date->format('Y-m-d'))
        { 
            $time = $date->format('H');//dd($time);
            $data = TimeSlot::where('ref1', '>' ,$time)->get();
        }else{
            $data = TimeSlot::get();
        }
        return response()->json($data,200);
       // return response()->json(['data'=>$data]);
        
         //$location = Location::all();
       // return response()->json($location,200);
    }
    
    
    
}
