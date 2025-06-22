<?php

namespace App\Http\Controllers\Api;
use App\Models\Notification;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderBook;
use App\Models\Wallet;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderBookApiController extends Controller
{
    public function createOrderBook(Request $request)
    {
        $requestData = $request->all();

        if(!is_array($requestData))
        {
            return response(['error'=> 'Invalid request data'],400);
        }

        $userData = User::find(Auth::id());       
        
        // Set common values in $requestData
        $requestData['user_id'] = Auth::id();
        $requestData['customer'] = $userData->name;
        $requestData['invoice_dt'] = now()->format('Y-m-d');
        $requestData['finyear'] = $requestData['finyear'] ?? "2023-24";
        $requestData['status'] = 'order';
        //calculate invoice number
        $requestData['invoice'] = OrderBook::max('invoice') + 1 ?? 100;

        //calculate order sum
        $orderSum  = Order::where('user_id',Auth::id())
        ->where('status', 'cart')->sum('total');
        //$requestData['value'] = (float) $orderSum;
        $requestData['value'] = $requestData['order_sum'];
        $requestData['gst_sum'] = $requestData['gst_sum'];
        $requestData['wallet'] = $requestData['wallet'];

        $requestData['charge'] = (float) $requestData['charge'];
        $requestData['coupon'] = (float) $requestData['coupon'];
        $requestData['payment_amount'] = $orderSum + $requestData['charge'] - $requestData['coupon'];

        //validate the incoming request data
        $validator = Validator::make($requestData, [
            'invoice_dt' => 'nullable|date',
            'charge' => 'required|numeric',
            'coupon' => 'nullable|numeric',
            'user' => 'nullable|string',
            'finyear' => 'string',
            'payment_status' => 'required',
            'payment_mode' => 'required',
            'payment_ref' => 'nullable|string',
        ]);
            
        $orderBook = OrderBook::create($requestData);
        
        $updateOrder = Order::where('user_id',Auth::id())
        ->where('status','cart')
        ->update([
            'status'=>'order',
            'order_book_id' => $orderBook->id,
            'order_id' => $orderBook->id
        ]);

        //add wallet amount to wallets table
        $wallet = Wallet::create([
            'user_id' => Auth::id(),
            'credit' => $requestData['wallet'],
            'date' => Carbon::today(),
            'description' => $request->description
        ]);
        $orderBookData = OrderBook::find($orderBook->id);

        $ordersList = Order::where('order_book_id', $orderBook->id)->get();
        foreach($ordersList as $list){
            $order_id = $list->id;
            Notification::where('order_id', $order_id)
            ->update([
                'food_id' => $list->food_id,
                'message' => 'Product Ordered',
                'general' => 'no',
                'status' => 'yes'
            ]);
        }
       
        return response(['message' => 'Order created successfully', 'oderBook'=>$orderBookData], 201);
    }

    public function orderBookList(Request $request)
    {
        $orders = OrderBook::with('orders')
        ->where('user_id', Auth::id())
        ->where('order_id',$request->order_id)
        ->where('status', 'order')
        ->get();
        return response()->json($orders, 200);
    }

    public function cancelOrder(Request $request)
    {
        $wallet = Wallet::create([
            'user_id' => Auth::id(),
            'debit' => $request->debit,
            'date' => Carbon::today(),
            'description' => $request->description
        ]);
        $updateOrderBook = OrderBook::where('id', $request->order_book_id)
        ->update(['status', 'cancel']);
        return response()->json(['status' => 'success']);
    }
}
