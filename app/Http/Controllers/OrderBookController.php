<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderBook;
use App\Models\TimeSlot;
use Carbon\Carbon;

class OrderBookController extends Controller
{
    public function index()
    {
        $orderBook = OrderBook::orderByDesc('id')->get();
        $timeSlots = TimeSlot::all();
        return view('orderbook', compact('orderBook', 'timeSlots'));
    }

    public function orderDetails($orderBookIId)
    {
        $data          = Order::with('food', 'user')->where('order_book_id', $orderBookIId)->get();
        $deliveryDate  = OrderBook::where('id', $orderBookIId)->pluck('del_dt')->first();
        $formattedDate = Carbon::parse($deliveryDate)->toDateString();
        $time_slot     = OrderBook::where('id', $orderBookIId)->pluck('ref1')->first();

        return response()->json(['data' => $data, 'deliveryDate' => $formattedDate, 'time_slot' => $time_slot]);
    }
    public function updateOrderStatus($orderBookId)
    {
        OrderBook::where('id', $orderBookId)->update(['status' => 'delivered']);
        $orders = Order::where('order_book_id', $orderBookId)->get();
        foreach ($orders as $order) {
            Order::where('id', $order->id)->update(['status' => 'delivered']);
        }
        // Award coins to the user based on the active conversion rate
        $orderBook = OrderBook::find($orderBookId);
        if ($orderBook && $orderBook->user_id) {
            $user             = \App\Models\User::find($orderBook->user_id);
            $activeConversion = \App\Models\ConversionValue::where('status', 'active')->first();
            if ($user && $activeConversion) {
                $total = $orders->sum('total');
                if ($total >= $activeConversion->minimum_applicable_amount) {
                    $coins = $total / $activeConversion->coin_conversion_rate;
                    $user->coin += $coins;
                    $user->save();
                }
            }
        }
        return 'success';
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
