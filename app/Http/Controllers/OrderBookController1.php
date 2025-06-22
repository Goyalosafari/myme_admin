<?php

namespace App\Http\Controllers;
use App\Models\OrderBook;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderBookController extends Controller
{
    public function index()
    {
        $orderBook = OrderBook::get();
        return view('orderbook',compact('orderBook'));
    }

    public function orderDetails($orderBookIId)
    {
        $data = Order::with('food','user')->where('order_book_id', $orderBookIId)->get();
        return response()->json(['data'=> $data]);
    }

/*
    public function updateOrderStatus($orderId)
    {
        Order::where('id', $orderId)->update(['status'=> 'delivered']);
        return 'success';
    }
    */
    
    public function updateOrderStatus($orderId)
    {
        Order::where('id', $orderId)->update(['status'=> 'delivered']);
        $orders = Order::find($orderId);
        $orderBookId = $orders->order_book_id;

        $allDelivered = Order::where('order_book_id', $orderBookId)->where('status', 'delivered')->count() == Order::where('order_book_id', $orderBookId)->count();

        OrderBook::where('id', $orderBookId)->update(['status' => $allDelivered ? 'delivered' : 'partly_delivered']);

        return 'success';
    }
    
}
