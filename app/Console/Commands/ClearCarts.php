<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Console\Command;

// Scheduled hourly (app/Console/Kernel.php).
class ClearCarts extends Command
{
    protected $signature = 'cart:clear';

    protected $description = 'Clear expired carts (CART_TIME) and leftover cart items that no longer belong to a live cart';

    public function handle(CartService $cart): int
    {
        // 1. Carts whose timer has run out.
        $expiredCarts = 0;
        $expiredItems = 0;
        User::whereNotNull('cart_expires_at')
            ->where('cart_expires_at', '<=', now())
            ->each(function (User $user) use ($cart, &$expiredCarts, &$expiredItems) {
                $expiredItems += $cart->clear($user);
                $expiredCarts++;
            });

        // 2. Unwanted leftovers: no customer, customer deleted, food deleted, or
        //    old items with no timer at all (added before the timer existed).
        $cutoff   = now()->subMinutes($cart->minutes());
        $leftover = Order::where('status', 'cart')
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('user_id')
                    ->orWhereDoesntHave('user')
                    ->orWhereDoesntHave('food')
                    ->orWhere(function ($q) use ($cutoff) {
                        $q->where('created_at', '<', $cutoff)
                            ->whereHas('user', fn ($u) => $u->whereNull('cart_expires_at'));
                    });
            })
            ->delete();

        $this->info("Cleared {$expiredCarts} expired cart(s) ({$expiredItems} item(s)) and {$leftover} leftover cart item(s).");

        return self::SUCCESS;
    }
}
