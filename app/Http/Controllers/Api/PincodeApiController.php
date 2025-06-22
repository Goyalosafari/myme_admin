<?php

namespace App\Http\Controllers\Api;
use App\Models\Pincode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PincodeApiController extends Controller
{
    public function index()
    {
        $data = Pincode::all();
        return response()->json(['data' => $data]);
    }
    public function pincodeValidation(Request $request)
    {
        $data = Pincode::where('pincode', '=', $request->pincode)
        ->first();
        if($data){
            $status = 'exist';
        }else{
            $status = 'not_exist';
        }
        return response()->json(['status' => $status, 'data' => $data]);
    }
}