<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Food;
use App\Models\Notification;
use App\Models\Order;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderApiController extends Controller
{
    use ApiResponse;

    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'food_id' => 'required|exists:foods,id',
            'user_id' => 'required',
            'qty'     => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $existing = Order::where([
            'food_id' => $request->food_id,
            'user_id' => $request->user_id,
            'status'  => 'cart',
        ])->first();

        if ($existing) {
            return $this->error('This item is already in the cart', 409);
        }

        $food = Food::findOrFail($request->food_id);
        $qty  = $request->qty ?? 1;

        $order = Order::create([
            'food_id'    => $request->food_id,
            'user_id'    => $request->user_id,
            'qty'        => $qty,
            'price'      => $food->offer_price,
            'total'      => $qty * $food->offer_price,
            'discount'   => $request->discount ?? 0.00,
            'finyear'    => $request->finyear ?? '2023-24',
            'date'       => Carbon::today()->toDateString(),
            'status'     => 'cart',
            'invoice_id' => 1,
            'cess'       => 0.00,
        ]);

        Notification::create([
            'food_id'  => $request->food_id,
            'order_id' => $order->id,
            'message'  => 'Product Added to cart',
            'general'  => 'no',
            'status'   => 'no',
        ]);

        return $this->success(new OrderResource($order), 'Order created successfully', 201);
    }

    public function updateOrder(Request $request)
    {
        $order = Order::find($request->order_id);

        if (!$order || $order->status !== 'cart' || $order->user_id != $request->user_id) {
            return $this->success(['status' => 'not_existing']);
        }

        if ($request->qty == 0) {
            $order->delete();
            return $this->success(['status' => 'deleted']);
        }

        $order->update([
            'qty'   => $request->qty,
            'total' => $order->price * $request->qty,
        ]);

        return $this->success(['status' => 'updated']);
    }

    public function ordersByUserId(Request $request)
    {
        return $this->success(
            OrderResource::collection(
                Order::with('food')->where('user_id', $request->user_id)->where('status', 'cart')->get()
            )
        );
    }

    public function userCartSum(Request $request)
    {
        $total = Order::where('user_id', $request->user_id)->where('status', 'cart')->sum('total');

        return $this->success(['totalSum' => $total]);
    }

    public function cartSumByUserId(Request $request)
    {
        $sum = Order::where('user_id', $request->user_id)->where('status', 'cart')->sum('total');

        return $this->success(['order_sum' => $sum]);
    }

    public function gstSumByUserId(Request $request)
    {
        $sum = Order::where('user_id', $request->user_id)->where('status', 'cart')->sum('gst_value');

        return $this->success(['gst_sum' => $sum]);
    }

    public function ordersByOrderId(Request $request)
    {
        return $this->success(
            OrderResource::collection(
                Order::with('food')->where('order_book_id', $request->order_book_id)->where('status', 'order')->get()
            )
        );
    }

    public function ordersByListUserId(Request $request)
    {
        return $this->success(
            OrderResource::collection(
                Order::with('food')->where('user_id', $request->user_id)->where('status', 'order')->get()
            )
        );
    }

    public function ordersHistoryDetails(Request $request)
    {
        return $this->success(
            OrderResource::collection(
                Order::with('food')
                    ->where('user_id', $request->user_id)
                    ->where('order_book_id', $request->order_id)
                    ->where('status', 'order')
                    ->get()
            )
        );
    }

    public function ordersByGroupUserId(Request $request)
    {
        $orders = Order::with('food')
            ->where('user_id', $request->user_id)
            ->where('status', 'order')
            ->get()
            ->groupBy('order_book_id')
            ->map(fn($group) => OrderResource::collection($group)->resolve());

        return $this->success($orders);
    }

    public function ordersByTimeslot(Request $request)
    {
        return $this->success(
            OrderResource::collection(
                Order::with('food')->where('user_id', $request->user_id)->where('status', 'cart')->get()
            )
        );
    }

    public function updateTimeSlot(Request $request, $orderId)
    {
        Order::where('id', $orderId)->update(['time_slot' => $request->time_slot]);

        return $this->success(null, 'Timeslot updated');
    }
}
