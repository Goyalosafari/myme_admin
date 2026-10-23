<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CoupenResource;
use App\Models\Coupen;
use App\Models\User;
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
                'narration'     => $c->narration,
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

    // Validates a code. When the app sends user_id this is "apply": one use is
    // reserved for that customer for COUPON_HOLD_TIME minutes, released by the
    // coupons:release-holds job if no order is placed.
    public function checkCoupon(Request $request)
    {
        $coupon = Coupen::where('coupen_code', $request->code)
            ->where('to_date', '>=', Carbon::today()->toDateString())
            ->first();

        if (!$coupon) {
            return $this->error('Coupon not found or expired', 404);
        }

        $userId = $request->filled('user_id') ? (int) $request->user_id : null;

        if ($coupon->isUsageExceeded($userId)) {
            return $this->error('This coupon has reached its usage limit', 422);
        }

        $data = (new CoupenResource($coupon))->resolve();

        if ($userId && User::whereKey($userId)->exists()) {
            $data['hold_expires_at'] = $coupon->holdFor($userId)->expires_at->toIso8601String();
        }

        return $this->success($data);
    }

    // Customer removed the coupon from the cart: give the reserved use back.
    public function releaseCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string', 'user_id' => 'required|integer']);

        $coupon = Coupen::where('coupen_code', $request->code)->first();

        if (!$coupon) {
            return $this->error('Coupon not found', 404);
        }

        $coupon->releaseFor((int) $request->user_id);

        return $this->success(null, 'Coupon released');
    }

    // Has this coupon hit its "No of Usage" limit? Counts placed orders that
    // carried the code (cancelled ones excluded) plus other customers' live
    // holds against coupens.no_of_usage.
    public function checkCouponUsage(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $coupon = Coupen::where('coupen_code', $request->code)->first();

        if (!$coupon) {
            return $this->error('Coupon not found', 404);
        }

        $userId    = $request->filled('user_id') ? (int) $request->user_id : null;
        $limit     = $coupon->usageLimit();
        $usedCount = $coupon->usedCount();
        $heldCount = $coupon->heldCount($userId);
        $exceeded  = $coupon->isUsageExceeded($userId);

        return $this->success([
            'coupon_code'     => $coupon->coupen_code,
            'no_of_usage'     => $limit,
            'used_count'      => $usedCount,
            'held_count'      => $heldCount,
            'remaining_usage' => $limit === null ? null : max($limit - $usedCount - $heldCount, 0),
            'user_used_count' => $userId ? $coupon->usedCount($userId) : null,
            'is_exceeded'     => $exceeded,
            'can_apply'       => !$exceeded,
        ], $exceeded ? 'Coupon usage limit exceeded' : 'Coupon can be applied');
    }
}
