<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PincodeResource;
use App\Models\Pincode;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PincodeApiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        return $this->success(PincodeResource::collection(Pincode::all()));
    }

    public function pincodeValidation(Request $request)
    {
        $pincode = Pincode::where('pincode', $request->pincode)->first();

        return $this->success([
            'status' => $pincode ? 'exist' : 'not_exist',
            'data'   => $pincode ? new PincodeResource($pincode) : null,
        ]);
    }
}
