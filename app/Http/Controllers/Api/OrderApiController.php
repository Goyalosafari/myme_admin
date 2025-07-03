<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\FoodRating;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

        if (! is_array($requestData)) {
            return response(['error' => 'Invalid request data'], 400);
        }

        // $requestData['dt_from'] = $requestData['dt_from'] ? \DateTime::createFromFormat('d/m/Y',$requestData['dt_from'])->format('Y-m-d') : (new \DateTime())->format('Y-m-d');
        // $requestData['dt_to'] = $requestData['dt_to'] ? \DateTime::createFromFormat('d/m/Y',$requestData['dt_to'])->format('Y-m-d') : (new \DateTime())->format('Y-m-d');

        // Set default values if they are not provided
        $requestData['user_id']  = $requestData['user_id'];
        $requestData['qty']      = $requestData['qty'] ?? 1;
        $requestData['discount'] = $requestData['discount'] ?? 0.00;
        $requestData['finyear']  = $requestData['finyear'] ?? "2023-24";

        // Retrieve food data and calculate total price
        $foodData                  = Food::find($requestData['food_id']);
        $requestData['price']      = $foodData->offer_price;
        $requestData['total']      = $requestData['qty'] * $requestData['price'];
        $requestData['date']       = (new \DateTime())->format('Y-m-d');
        $requestData['status']     = 'cart';
        $requestData['invoice_id'] = 1;
        $requestData['cess']       = 0.00;

        // Validate the incoming request data
        $validator = Validator::make($requestData, [
            'food_id' => 'required',
            'qty'     => 'integer',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response(['error' => $validator->errors()], 400);
        }

        // Check if the food with the given food_id is already in the cart for the current user
        $existingOrder = Order::where([
            'food_id' => $requestData['food_id'],
            'user_id' => $requestData['user_id'],
            'status'  => 'cart',
        ])->first();

        if ($existingOrder) {
            return response(['error' => 'This food item is already in the cart'], 400);
        }

        // Create a new order using the validated data
        $order = Order::create($requestData);
        $data  = Order::find($order->id);

        $notification = Notification::create([
            'food_id'  => $requestData['food_id'],
            'order_id' => $order->id,
            'message'  => 'Product Added to cart',
            'general'  => 'no',
            'status'   => 'no',
        ]);
        return response(['message' => 'Order created successfully', 'order' => $data], 201);
    }

    /*  public function ordersByUserId(Request $request)
    {
        $orders = Order::with('food')->where('user_id', $request->user_id)
        ->where('status','cart')->get();

        $order_sum = Order::where('user_id', $request->user_id)
        ->where('status','cart')->sum('total');

        $gst_sum = Order::where('user_id', $request->user_id)
        ->where('status','cart')->sum('gst_value');

        return response()->json(['orders' => $orders, 'order_sum'=>$order_sum, 'gst_sum'=>$gst_sum], 200);
    }*/

    public function cartSumByUserId(Request $request)
    {

        $order_sum = Order::where('user_id', $request->user_id)
            ->where('status', 'cart')->sum('total');

        return response()->json(['order_sum' => $order_sum], 200);
    }

    public function gstSumByUserId(Request $request)
    {

        $gst_sum = Order::where('user_id', $request->user_id)
            ->where('status', 'cart')->sum('gst_value');

        return response()->json(['gst_sum' => $gst_sum], 200);
    }

    public function ordersByUserId(Request $request)
    {

        $orders = Order::with('food')->where('user_id', $request->user_id)
            ->where('status', 'cart')->get();
        return response()->json(['orders' => $orders], 200);
    }

    public function userCartSum(Request $request)
    {

        $orders = Order::with('food')->where('user_id', $request->user_id)
            ->where('status', 'cart')->get();
        $totalSum = $orders->sum('total');
        return response()->json(['totalSum' => $totalSum], 200);
    }

    public function ordersByOrderId(Request $request)
    {

        $orders = Order::with('food')->where('order_book_id', $request->user_id)
            ->where('status', 'order')->get();
        return response()->json(['orders' => $orders], 200);
    }

    ///ordersByListUserId

    public function ordersByListUserId(Request $request)
    {
        $orders = Order::with('food')->where('user_id', $request->user_id)
            ->where('status', 'order')->get();
        return response()->json(['orders' => $orders], 200);
    }

    public function ordersHistoryDetails(Request $request)
    {
        $orders = Order::with('food')->where('user_id', $request->user_id)
            ->where('order_book_id', $request->order_id)
            // ->where('status', 'order')
            ->get();
        return response()->json(['orders' => $orders], 200);
    }

    /*  {
         $orders = Order::with('food','time_slot')->where('user_id', $request->user_id)
        ->where('status','order')->get()
        ->groupBy('order_book_id');
        return response()->json(['orders' => $orders], 200);
    }

    public function ordersByUserId(Request $request)
    {

        $orders = Order::with('food', ‘time_slot’ )->where('user_id', $request->user_id)
        ->where('status','cart')->get();
        return response()->json(['orders' => $orders], 200);
    }

    */

    public function time_slot()
    {
        return $this->belongsTo(TimeSlot::class, 'id');
    }

    public function updateTimeslot(Request $request, $orderId)
    {
        $data = Order::where('id', $orderId)
            ->update([
                'time_slot' => $request->time_slot,
            ]);
        return response()->json('success', 200);
    }

    public function ordersByGroupUserId(Request $request)
    {
        $orders = Order::with('food', 'time_slot')->where('user_id', $request->user_id)
            ->where('status', 'order')->get()
            ->groupBy('order_book_id');
        return response()->json(['orders' => $orders], 200);
    }

    public function updateOrder(Request $request)
    {
        $order = Order::find($request->order_id);
        if ($order->status == 'cart' && $order->user_id == $request->user_id) {
            if ($request->qty == 0) {
                $order->delete();
                $status = 'deleted';
                // $notification = Notification::create([
                //     'food_id' => $order->food_id,
                //     'order_id' => $request->order_id,
                //     'message' => 'Product Removed from cart',
                //     'general' => 'no',
                //     'status' => 'no'
                // ]);
            } else {
                $order->update([
                    'qty'   => $request->qty,
                    'total' => $order->price * $request->qty,
                ]);
                $status = 'updated';
                /*$notification = Notification::create([
                    'food_id' => $order->food_id,
                    'order_id' => $request->order_id,
                    'message' => 'Product modified to cart',
                    'general' => 'no',
                    'status' => 'no'
                ]);*/
            }
        } else {
            $status = 'not_existing';
        }
        return response()->json(['status' => $status]);

    }

    public function updateOrder123(Request $request)
    {
        $order = Order::find($request->order_id);
        if ($order->status == 'cart' && $order->user_id == $request->user_id) {
            if ($request->qty == 0) {
                $order->delete();
                $status = 'deleted';
                // $notification = Notification::create([
                //     'food_id' => $order->food_id,
                //     'order_id' => $request->order_id,
                //     'message' => 'Product Removed from cart',
                //     'general' => 'no',
                //     'status' => 'no'
                // ]);
            } else {
                $order->update(['qty' => $request->qty]);
                $status = 'updated';
                /*$notification = Notification::create([
                    'food_id' => $order->food_id,
                    'order_id' => $request->order_id,
                    'message' => 'Product modified to cart',
                    'general' => 'no',
                    'status' => 'no'
                ]);*/
            }
        } else {
            $status = 'not_existing';
        }
        return response()->json(['status' => $status]);
    }

    public function ordersByTimeslot(Request $request)
    {

        $orders = Order::with('food', ‘time_slot’)->where('user_id', $request->user_id)
            ->where('status', 'cart')->get();
        return response()->json(['orders' => $orders], 200);
    }

    public function testSmsSending()
    {
        // Static test values
        $userData = (object) [
            'mobile' => '7559899911', // Replace with a test mobile number
        ];

        $orderBook = (object) [
            'id'     => 101,          // Replace with a test order ID
            'ref1'   => '3-5 days',   // Replace with estimated delivery time
            'del_dt' => '2024-11-20', // Replace with a test delivery date
        ];

        $mobileNumber = $userData->mobile;
        $message      = "Thank you for your order with MYME BUSINESS CORPORATION PRIVATE LIMITED ! Your order #{$orderBook->id} has been successfully placed. You will receive a confirmation email shortly. Estimated delivery time: {$orderBook->ref1} and date : {$orderBook->del_dt}.";
        $templateID   = '1207171377328523771';

        try {
            // Assuming $this->smsService->sendSms() is implemented
            $response = $this->smsService->sendSms($mobileNumber, $message, $templateID);

            // Log or display the response for verification
            \Log::info('SMS Response: ' . json_encode($response));
            echo "SMS sent successfully! Response: " . json_encode($response);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('SMS sending failed: ' . $e->getMessage());
            echo "SMS sending failed: " . $e->getMessage();
        }
    }

    public function AddFoodRating(Request $request)
    {
        $request->validate([
            'user_id'  => 'required|exists:users,id',
            'food_id'  => 'required|exists:foods,id',
            'order_id' => 'nullable|integer',
            'rating'   => 'required|integer|min:1|max:5',
        ]);

        // Check if the user has already rated this food
        $existing = FoodRating::where('user_id', $request->user_id)
            ->where('food_id', $request->food_id)
            ->when($request->order_id, function ($q) use ($request) {
                return $q->where('order_id', $request->order_id);
            })
            ->exists();

        if ($existing) {
            return response()->json([
                'message' => 'You have already rated this food.',
            ], 409);
        }

        // Store rating
        FoodRating::create([
            'user_id'  => $request->user_id,
            'food_id'  => $request->food_id,
            'order_id' => $request->order_id,
            'rating'   => $request->rating,
        ]);

        // Update foods table
        $ratingColumn = 'rating_' . $request->rating;

        Food::where('id', $request->food_id)
            ->update([
                $ratingColumn => DB::raw("COALESCE($ratingColumn, 0) + 1"),
            ]);

        return response()->json(['message' => 'Rating submitted successfully.']);
    }

    public function hasRatedFood(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'food_id' => 'required|exists:foods,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $hasRated = FoodRating::where('user_id', $request->user_id)
            ->where('food_id', $request->food_id)
            ->exists();

        return response()->json(['hasRated' => $hasRated]);
    }

}
