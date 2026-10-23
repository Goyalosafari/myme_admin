<?php

namespace App\Services;

use App\Models\CouponHold;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

/**
 * Cart timer: a cart lives config('cart.expiry_minutes') (CART_TIME) after its
 * first item is added; the customer can extend it. Expired carts are cleared
 * the next time any cart API touches them, and by the hourly cart:clear job.
 */
class CartService
{
    public function minutes(): int
    {
        return max(1, (int) config('cart.expiry_minutes', 30));
    }

    // Called after an item is added: starts the timer if the cart has none yet.
    public function start($userId): ?Carbon
    {
        $user = User::find($userId);
        if (!$user || !$this->hasItems($userId)) {
            return null;
        }

        if (!$user->cart_expires_at) {
            $user->forceFill(['cart_expires_at' => now()->addMinutes($this->minutes())])->save();
        }

        return $user->cart_expires_at;
    }

    // Push the expiry back by a full CART_TIME from now. Null if there's no cart.
    public function extend($userId): ?Carbon
    {
        $this->clearIfExpired($userId);

        $user = User::find($userId);
        if (!$user || !$this->hasItems($userId)) {
            return null;
        }

        $user->forceFill(['cart_expires_at' => now()->addMinutes($this->minutes())])->save();

        return $user->cart_expires_at;
    }

    // Clears the cart if its time is up. Returns true when it was cleared.
    public function clearIfExpired($userId): bool
    {
        $user = User::find($userId);

        if (!$user || !$user->cart_expires_at || $user->cart_expires_at->isFuture()) {
            return false;
        }

        $this->clear($user);

        return true;
    }

    // Remove every cart item, reset the timer and release coupons held for it.
    public function clear(User $user): int
    {
        $removed = Order::where('user_id', $user->id)->where('status', 'cart')->delete();

        CouponHold::where('user_id', $user->id)->delete();
        $user->forceFill(['cart_expires_at' => null])->save();

        return $removed;
    }

    // Cart placed as an order: stop the timer (items are no longer 'cart').
    public function finish($userId): void
    {
        User::whereKey($userId)->update(['cart_expires_at' => null]);
    }

    public function hasItems($userId): bool
    {
        return Order::where('user_id', $userId)->where('status', 'cart')->exists();
    }

    public function status($userId): array
    {
        $this->clearIfExpired($userId);

        // Carts created before the timer existed get one from now.
        $expiresAt = $this->start($userId);

        return [
            'cart_time_minutes' => $this->minutes(),
            'has_cart'          => (bool) $expiresAt,
            'expires_at'        => $expiresAt?->toIso8601String(),
            'seconds_left'      => $expiresAt ? max(0, now()->diffInSeconds($expiresAt, false)) : 0,
        ];
    }
}
