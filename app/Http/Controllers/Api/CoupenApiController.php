<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupen;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CoupenApiController extends Controller
{
    public function index()
    {
        $todayDate = Carbon::today()->toDateString();
        $coupen = Coupen::where('to_date', '>=' , $todayDate)->get();
        return response()->json( ['coupon'=>$coupen],200 );
    }
    
    public function filterCoupen(Request $request)
    {
        $data = Coupen::where('to_date' , '>=' , $request->date)
        ->orWhere('to_date' , null);
        if($request->total != null){
            $data->where('min_amount', '>=', $request->total);
        }
        $data = $data->get();

        $data = $data->filter(function ($item) use ($request) {
            return $item->min_amount >= $request->total;
        });

        return response()->json(['data'=> $data]);
    }




}


//['coupon'=>$coupen]