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

    // NOTE: the shipped Flutter app's coupon screen (lib/view/coupon/coupon.dart)
    // parses this response as `{ "coupon": [ {..., "coupen_code": ... } ] }` —
    // NOT the standard `{ "success": true, "data": [...] }` envelope every other
    // endpoint uses, and not the "code" key CoupenResource emits. That mismatch
    // is why backend coupons never appeared on the frontend: `json['coupon']`
    // was always null, so the screen fell straight to "No data available".
    // Matching the app's actual contract here (rather than changing the already
    // shipped app) is the fix.
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $coupons = Coupen::where('to_date', '>=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('from_date')->orWhere('from_date', '<=', $today);
            })
            ->get()
            ->map(fn (Coupen $c) => [
                'id'            => $c->id,
                'title'         => $c->title,
                'coupen_code'   => $c->coupen_code,
                'no_of_usage'   => $c->no_of_usage,
                'discount_type' => $c->discount_type,
                'discount'      => $c->discount,
                'max_discount'  => $c->max_discount,
                'min_amount'    => $c->min_amount,
                'from_date'     => $c->from_date,
                'to_date'       => $c->to_date,
                'status'        => $c->status,
                'order'         => $c->order,
                'parent_id'     => $c->parent_id,
                'created_at'    => $c->created_at,
                'updated_at'    => $c->updated_at,
            ])
            ->values();

        return response()->json(['success' => true, 'coupon' => $coupons]);
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
        $coupon = Coupen::where('coupen_code', $request->code)
            ->where('to_date', '>=', Carbon::today()->toDateString())
            ->first();

        if (!$coupon) {
            return $this->error('Coupon not found or expired', 404);
        }

        return $this->success(new CoupenResource($coupon));
    }
}
