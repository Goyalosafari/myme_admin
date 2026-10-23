<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

// Scheduled hourly (app/Console/Kernel.php).
class ClearCarts extends Command
{
    protected $signature = 'cart:clear';

    protected $description = 'Clear unwanted cart items: abandoned for 24h+, or whose customer/food was deleted';

    public function handle(): int
    {
        $removed = Order::where('status', 'cart')
            ->where(function ($q) {
                $q->whereNull('user_id')
                    ->orWhereDoesntHave('user')
                    ->orWhereDoesntHave('food')
                    ->orWhere('updated_at', '<', now()->subDay());
            })
            ->delete();

        $this->info("Cleared {$removed} unwanted cart item(s).");

        return self::SUCCESS;
    }
}
