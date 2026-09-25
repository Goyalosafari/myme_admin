<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupen extends Model
{
    use HasFactory, SoftDeletes;

    // Times this coupon has been used on placed orders. Cancelled orders don't
    // count, so a cancellation frees the usage up again.
    public function usedCount(?int $userId = null): int
    {
        return OrderBook::where('coupon_code', $this->coupen_code)
            ->where('status', '!=', 'cancel')
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->count();
    }

    public function isUsageExceeded(): bool
    {
        return $this->no_of_usage !== null
            && $this->no_of_usage !== ''
            && $this->usedCount() >= (int) $this->no_of_usage;
    }
}
