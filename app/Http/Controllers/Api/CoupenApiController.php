<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CoupenResource;
use App\Models\Coupen;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CoupenApiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        return $this->success(
            CoupenResource::collection(
                Coupen::where('to_date', '>=', Carbon::today()->toDateString())->get()
            )
        );
    }

    public function filterCoupen(Request $request)
    {
        $query = Coupen::where(function ($q) use ($request) {
            $q->where('to_date', '>=', $request->date)->orWhereNull('to_date');
        });

        if ($request->total) {
            $query->where('min_amount', '<=', $request->total);
        }

        return $this->success(CoupenResource::collection($query->get()));
    }

    public function checkCoupon(Request $request)
    {
        $coupon = Coupen::where('code', $request->code)
            ->where('to_date', '>=', Carbon::today()->toDateString())
            ->first();

        if (!$coupon) {
            return $this->error('Coupon not found or expired', 404);
        }

        return $this->success(new CoupenResource($coupon));
    }
}
