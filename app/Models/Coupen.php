<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupen extends Model
{
    use HasFactory, SoftDeletes;

    public function holds()
    {
        return $this->hasMany(CouponHold::class);
    }

    // Times this coupon has been used on placed orders. Cancelled orders don't
    // count, so a cancellation frees the usage up again.
    public function usedCount(?int $userId = null): int
    {
        return OrderBook::where('coupon_code', $this->coupen_code)
            ->where('status', '!=', 'cancel')
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->count();
    }

    // Uses reserved by customers who applied it but haven't ordered yet.
    public function heldCount(?int $exceptUserId = null): int
    {
        return $this->holds()->active()
            ->when($exceptUserId, fn ($q) => $q->where('user_id', '!=', $exceptUserId))
            ->count();
    }

    public function usageLimit(): ?int
    {
        return $this->no_of_usage !== null && $this->no_of_usage !== '' ? (int) $this->no_of_usage : null;
    }

    // Full once orders + other customers' live holds reach the limit. The
    // asking customer's own hold is excluded so it never blocks them.
    public function isUsageExceeded(?int $forUserId = null): bool
    {
        $limit = $this->usageLimit();

        return $limit !== null && $this->usedCount() + $this->heldCount($forUserId) >= $limit;
    }

    // Reserve (or refresh) one use for this customer for COUPON_HOLD_TIME.
    public function holdFor(int $userId): CouponHold
    {
        return CouponHold::updateOrCreate(
            ['coupen_id' => $this->id, 'user_id' => $userId],
            ['expires_at' => now()->addMinutes(max(1, (int) config('cart.coupon_hold_minutes', 30)))]
        );
    }

    public function releaseFor(int $userId): void
    {
        $this->holds()->where('user_id', $userId)->delete();
    }
}
