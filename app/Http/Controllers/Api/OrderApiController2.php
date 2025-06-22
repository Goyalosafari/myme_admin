<?php

namespace App\Http\Controllers\Api;

use App\Models\Food;
use App\Models\Notification;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class OrderApiController extends Controller
{
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function addToCart(Request $request)
    {
       
        // Ensure that the request data is an array
        $requestData = $request->all();

        if (!is_array($requestData)) {
           return response(['error' => 'Invalid request data'], 400);
        }
       
        $requestData['dt_from'] = $requestData['dt_from'] ? $requestData['dt_from'] : Carbon::today();
        $requestData['dt_to'] = $requestData['dt_to'] ? $requestData['dt_to'] : Carbon::today();

        $requestData['user_id'] = $requestData['user_id'];
        $requestData['qty'] = $requestData['qty'] ?? 1;
        $requestData['discount'] = $requestData['discount'] ?? 0.00;
        $requestData['finyear'] = $requestData['finyear'] ?? "2023-24";

        // Retrieve food data and calculate total price
        $foodData = Food::find($requestData['food_id']);
        $requestData['price'] = $foodData->price;
        $requestData['total'] = $requestData['qty'] * $requestData['price'];
        $requestData['date'] = Carbon::today();
        $requestData['status'] = 'cart';
        $requestData['invoice_id'] = 1;
        $requestData['cess'] = 0.00;
        $requestData['gst_value'] = $foodData['gst_value'] * $requestData['qty'];

        // Validate the incoming request data
        $validator = Validator::make($requestData, [
           'food_id' => 'required',
           'qty' => 'integer',
           'time_slot_id' => 'required',
        ]);

       // Check if validation fails
       if ($validator->fails()) {
           return response(['error' => $validator->errors()], 400);
       }

       // Create a new order using the validated data
       $order = Order::create($requestData);
       $data = Order::find($order->id);

       $notification = Notification::create([
        'food_id' => $requestData['food_id'],
        'order_id' => $order->id,
        'message' => 'Product Added to cart',
        'general' => 'no',
        'status' => 'no'
       ]);
       return response(['message' => 'Order created successfully', 'order' => $data], 201);
    }

    public function ordersByUserId()
    {
        $orders = Order::with('food','time_slot')->where('user_id', $requestData['user_id'])
        ->where('status','cart')->get();

        $order_sum = Order::where('user_id', $requestData['user_id'])
        ->where('status','cart')->sum('total');
        
        $gst_sum = Order::where('user_id', $requestData['user_id'])
        ->where('status','cart')->sum('gst_value');

        return response()->json(['orders' => $orders, 'order_sum'=>$order_sum, 'gst_sum'=>$gst_sum], 200);
    }
    public function updateTimeslot(Request $request, $orderId)
    {
        $data = Order::where('id', $orderId)
        ->update([
            'time_slot' => $request->time_slot
        ]);
        return response()->json('success', 200);
    }

    public function updateOrder(Request $request)
    {
        $order = Order::find($request->order_id);
        if($order->status == 'cart' && $order->user_id == Auth::id())
        {
            if($request->qty == 0){
                $order->delete();
                $status = 'deleted';
                // $notification = Notification::create([
                //     'food_id' => $order->food_id,
                //     'order_id' => $request->order_id,
                //     'message' => 'Product Removed from cart',
                //     'general' => 'no',
                //     'status' => 'no'
                // ]);
            }else{
                $order->update(['qty' =>$request->qty]);
                $status = 'updated';
                $notification = Notification::create([
                    'food_id' => $order->food_id,
                    'order_id' => $request->order_id,
                    'message' => 'Product modified to cart',
                    'general' => 'no',
                    'status' => 'no'
                ]);
            }
        }else{
            $status = 'not_existing';
        }
        return response()->json(['status'=>$status]);
    }
}
