<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderBookResource;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderBook;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\Wallet;
use App\Services\SmsService;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderBookApiController extends Controller
{
    use ApiResponse;

    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function smstest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile'  => 'required|digits:10|regex:/^[6-9][0-9]{9}$/',
            'message' => 'required|string|max:160',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $response = $this->smsService->sendSms($request->mobile, $request->message, '1207171376155379572');
            return $this->success(['response' => $response], 'SMS sent successfully');
        } catch (\Exception $e) {
            return $this->error('SMS sending failed: ' . $e->getMessage(), 500);
        }
    }

    public function smsorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile'  => 'required|digits:10|regex:/^[6-9][0-9]{9}$/',
            'message' => 'required|string|max:320',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $response = $this->smsService->sendSms($request->mobile, $request->message, '1207171377328523771');
            return $this->success(['response' => $response], 'SMS sent successfully');
        } catch (\Exception $e) {
            return $this->error('SMS sending failed: ' . $e->getMessage(), 500);
        }
    }

    public function smscancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile'  => 'required|digits:10|regex:/^[6-9][0-9]{9}$/',
            'message' => 'required|string|max:320',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $response = $this->smsService->sendSms($request->mobile, $request->message, '1207171377044163620');
            return $this->success(['response' => $response], 'SMS sent successfully');
        } catch (\Exception $e) {
            return $this->error('SMS sending failed: ' . $e->getMessage(), 500);
        }
    }

    public function createOrderBook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'        => 'required',
            'charge'         => 'required|numeric',
            'coupon'         => 'required|numeric',
            'wallet'         => 'required|numeric',
            'payment_status' => 'required|string',
            'payment_mode'   => 'required|string',
            'payment_ref'    => 'nullable|string',
            'time_slot_id'   => 'required|exists:time_slots,id',
            'date'           => 'required|date',
            'active_address' => 'nullable|string',
            'address_type'   => 'nullable|string',
            'finyear'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $timeslot = TimeSlot::find($request->time_slot_id);
        $orderSum = Order::where('user_id', $request->user_id)->where('status', 'cart')->sum('total');

        $orderBook = OrderBook::create([
            'user_id'        => $request->user_id,
            'customer'       => $request->user_id,
            'user'           => $request->active_address,
            'pack_user'      => $request->address_type,
            'invoice_dt'     => Carbon::today()->toDateString(),
            'finyear'        => $request->finyear ?? '2023-24',
            'status'         => 'order',
            'invoice'        => (OrderBook::max('invoice') ?? 100) + 1,
            'value'          => (float) $orderSum,
            'charge'         => (float) $request->charge,
            'coupon'         => (float) $request->coupon,
            'payment_amount' => $orderSum + $request->charge - $request->coupon,
            'del_dt'         => $request->date,
            'ref'            => $request->time_slot_id,
            'ref1'           => $timeslot->time_slot,
            'payment_status' => $request->payment_status,
            'payment_mode'   => $request->payment_mode,
            'payment_ref'    => $request->payment_ref,
        ]);

        Order::where('user_id', $request->user_id)->where('status', 'cart')->update([
            'status'        => 'order',
            'order_book_id' => $orderBook->id,
            'order_id'      => $orderBook->id,
            'time_slot_id'  => $request->time_slot_id,
            'date'          => $request->date,
        ]);

        // Deduct wallet balance
        $walletBalance = Wallet::where('user_id', $request->user_id)
            ->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as balance')
            ->value('balance');

        $creditAmount = min((float) $request->wallet, (float) $walletBalance);
        if ($creditAmount > 0) {
            Wallet::create([
                'user_id'     => $request->user_id,
                'credit'      => $creditAmount,
                'date'        => Carbon::today(),
                'description' => $request->description,
            ]);
        }

        // Update notifications
        Order::where('order_book_id', $orderBook->id)->each(function ($item) {
            Notification::where('order_id', $item->id)->update([
                'food_id' => $item->food_id,
                'message' => 'Product Ordered',
                'general' => 'no',
                'status'  => 'yes',
            ]);
        });

        // Send order confirmation SMS
        try {
            $user    = User::find($request->user_id);
            $message = "Thank you for your order with MYME! Your order #{$orderBook->id} has been placed. Delivery: {$orderBook->ref1} on {$orderBook->del_dt}.";
            $this->smsService->sendSms($user->mobile, $message, '1207171377328523771');
        } catch (\Exception $e) {
            \Log::error('Order SMS failed: ' . $e->getMessage());
        }

        return $this->success(
            new OrderBookResource(OrderBook::with('orders')->find($orderBook->id)),
            'Order created successfully',
            201
        );
    }

    public function orderBookList(Request $request)
    {
        return $this->success(
            OrderBookResource::collection(
                OrderBook::with('orders')
                    ->where('user_id', $request->user_id)
                    ->where('status', 'order')
                    ->get()
            )
        );
    }

    public function cancelOrder(Request $request)
    {
        $orderBook = OrderBook::find($request->order_book_id);

        if (!$orderBook) {
            return $this->error('Order not found', 404);
        }

        if ($orderBook->status !== 'order') {
            return $this->error('This order cannot be cancelled', 422);
        }

        $timeslot = TimeSlot::find($orderBook->ref);
        $now      = Carbon::now();

        if ($orderBook->del_dt < $now->toDateString() ||
            ($orderBook->del_dt === $now->toDateString() && $now->format('H') > optional($timeslot)->ref1)) {
            return $this->error('Cancellation window has passed for this order', 422);
        }

        $walletEntry  = Wallet::find($orderBook->wallet_id);
        $refundAmount = $orderBook->payment_amount + ($walletEntry ? $walletEntry->credit : 0);

        Wallet::create([
            'user_id'     => $request->user_id,
            'debit'       => $refundAmount,
            'date'        => Carbon::today(),
            'description' => $request->description,
        ]);

        $orderBook->update(['status' => 'cancel']);

        Order::where('order_book_id', $request->order_book_id)->each(function ($item) {
            $item->update(['status' => 'cancel']);
            Notification::create([
                'food_id'  => $item->food_id,
                'order_id' => $item->id,
                'message'  => 'Product Cancelled',
                'general'  => 'no',
                'status'   => 'yes',
            ]);
        });

        try {
            $user    = User::find($request->user_id);
            $message = "Dear {$user->name}, Your order #{$request->order_book_id} has been cancelled. Refund will be credited to your wallet.";
            $this->smsService->sendSms($user->mobile, $message, '1207171377044163620');
        } catch (\Exception $e) {
            \Log::error('Cancel SMS failed: ' . $e->getMessage());
        }

        return $this->success(null, 'Order cancelled successfully');
    }

    public function ordersHistory(Request $request)
    {
        return $this->success(
            OrderBookResource::collection(
                OrderBook::where('user_id', $request->user_id)->where('status', 'order')->latest()->get()
            )
        );
    }

    public function userWallet(Request $request)
    {
        $balance = Wallet::where('user_id', $request->user_id)
            ->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as balance')
            ->value('balance');

        return $this->success(['balance' => $balance]);
    }
}
