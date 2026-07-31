<?php

namespace App\Http\Controllers;
use App\Models\Notification;
use App\Models\TimeSlot;
use App\Models\OrderBook;
use App\Models\Order;
use App\Models\LoyaltySetting;
use App\Models\LoyaltyTransaction;
use App\Models\ReferralReward;
use App\Models\ReferralSetting;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderBookController extends Controller
{
    public function index()
    {
        $orderBook = OrderBook::orderByDesc('id')->paginate(20);
        $timeSlots = TimeSlot::all();
        return view('orderbook',compact('orderBook', 'timeSlots'));
    }


    public function orderDetails($orderBookIId)
    {
        $data = Order::with('food','user')->where('order_book_id', $orderBookIId)->get();
        $orderBook = OrderBook::where('id', $orderBookIId)->first();
        $formattedDate = Carbon::parse($orderBook->del_dt)->toDateString();

        return response()->json([
            'data'           => $data,
            'deliveryDate'   => $formattedDate,
            'time_slot'      => $orderBook->ref1,
            'value'          => $orderBook->value,
            'charge'         => $orderBook->charge,
            'coupon'         => $orderBook->coupon,
            'payment_amount' => $orderBook->payment_amount,
        ]);
    }
    public function updateOrderStatus($orderBookId)
    {
        OrderBook::where('id', $orderBookId)->update(['status' => 'delivered']);
        $orders = Order::where('order_book_id', $orderBookId)->get();
        foreach($orders as $order)
        {
            Order::where('id', $order->id)->update(['status'=> 'delivered']);
        }

        $this->awardLoyaltyPoints($orderBookId);
        $this->awardReferralReward($orderBookId);

        return 'success';
    }

    private function awardReferralReward($orderBookId)
    {
        $orderBook = OrderBook::find($orderBookId);
        if (!$orderBook || !$orderBook->user_id) {
            return;
        }

        $referredUser = User::find($orderBook->user_id);
        if (!$referredUser || !$referredUser->referred_by) {
            return;
        }

        if (ReferralReward::where('referred_user_id', $referredUser->id)->exists()) {
            return;
        }

        // Only pay out on the referred user's first delivered order.
        $deliveredOrderCount = OrderBook::where('user_id', $referredUser->id)
            ->where('status', 'delivered')
            ->count();

        if ($deliveredOrderCount !== 1) {
            return;
        }

        $referrer = User::where('referral_code', $referredUser->referred_by)->first();
        if (!$referrer) {
            return;
        }

        $rewardAmount = ReferralSetting::current()->reward_amount;

        Wallet::create([
            'user_id'     => $referrer->id,
            'debit'       => $rewardAmount,
            'date'        => Carbon::today(),
            'description' => "Referral reward for referring {$referredUser->name}",
        ]);

        ReferralReward::create([
            'referrer_user_id' => $referrer->id,
            'referred_user_id' => $referredUser->id,
            'order_book_id'    => $orderBook->id,
            'amount'           => $rewardAmount,
        ]);
    }

    private function awardLoyaltyPoints($orderBookId)
    {
        $alreadyAwarded = LoyaltyTransaction::where('order_book_id', $orderBookId)
            ->where('type', 'earned')
            ->exists();

        if ($alreadyAwarded) {
            return;
        }

        $orderBook = OrderBook::find($orderBookId);
        if (!$orderBook || !$orderBook->user_id) {
            return;
        }

        $pointsPerAmount = LoyaltySetting::current()->points_per_amount;
        $points = intdiv((int) $orderBook->payment_amount, $pointsPerAmount);

        if ($points > 0) {
            LoyaltyTransaction::create([
                'user_id'       => $orderBook->user_id,
                'order_book_id' => $orderBook->id,
                'points'        => $points,
                'type'          => 'earned',
                'description'   => "Earned for order #{$orderBook->id}",
            ]);
        }
    }
    
    // update status of each item
    // public function updateOrderStatus($orderId)
    // {
    //     Order::where('id', $orderId)->update(['status'=> 'delivered']);
    //     $orders = Order::find($orderId);
    //     $orderBookId = $orders->order_book_id;

    //     $allDelivered = Order::where('order_book_id', $orderBookId)->where('status', 'delivered')->count() == Order::where('order_book_id', $orderBookId)->count();

    //     OrderBook::where('id', $orderBookId)->update(['status' => $allDelivered ? 'delivered' : 'partly_delivered']);
        
    //     Notification::create([
    //         'food_id' => $orders->food_id,
    //         'order_id' => $orderId,
    //         'message' => 'Product Delivered',
    //         'general' => 'no',
    //         'status' => 'yes'
    //     ]);
    //     return 'success';
    // }
}
