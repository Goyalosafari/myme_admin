<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupen;
use Illuminate\Http\Request;

class CoupenApiController extends Controller
{
    public function index()
    {
        $coupen = Coupen::all();
        return response()->json(['coupon' => $coupen ],200 );
    }
}
