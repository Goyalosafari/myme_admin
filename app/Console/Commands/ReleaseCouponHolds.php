<?php

namespace App\Console\Commands;

use App\Models\CouponHold;
use Illuminate\Console\Command;

// Scheduled every 30 minutes (app/Console/Kernel.php).
class ReleaseCouponHolds extends Command
{
    protected $signature = 'coupons:release-holds';

    protected $description = 'Release coupon uses that were applied in a cart but never turned into an order';

    public function handle(): int
    {
        $released = CouponHold::where('expires_at', '<=', now())->delete();

        $this->info("Released {$released} expired coupon hold(s).");

        return self::SUCCESS;
    }
}
