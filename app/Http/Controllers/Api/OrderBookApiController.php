<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderBookResource;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderBook;
use App\Models\Pincode;
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
            'user_id'              => 'required',
            'charge'               => 'required|numeric',
            'coupon'               => 'required|numeric',
            'wallet'               => 'required|numeric',
            'payment_status'       => 'required|string',
            'payment_mode'         => 'required|string',
            'payment_ref'          => 'nullable|string',
            'time_slot_id'         => 'required|exists:time_slots,id',
            'date'                 => 'required|date',
            'active_address'       => 'nullable|string',
            'address_type'         => 'nullable|string',
            'finyear'              => 'nullable|string',
            // Delivery-address snapshot — everything the restaurant/delivery
            // team needs is captured here at order time so it survives the
            // customer later editing or deleting the saved address it came
            // from. All optional/backward-compatible: existing clients that
            // only send active_address/address_type keep working exactly as
            // before, they just won't get pincode-verified charge or a full
            // snapshot until they start sending these too.
            'pincode'              => 'nullable|string|max:20',
            'landmark'             => 'nullable|string|max:255',
            'latitude'             => 'nullable|string|max:50',
            'longitude'            => 'nullable|string|max:50',
            'receiver_name'        => 'nullable|string|max:255',
            'receiver_phone'       => 'nullable|string|max:20',
            'delivery_instruction' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $timeslot = TimeSlot::find($request->time_slot_id);
        $orderSum = Order::where('user_id', $request->user_id)->where('status', 'cart')->sum('total');
        $user     = User::find($request->user_id);

        // Pincode is the single source of truth for delivery charge. When the
        // client tells us which pincode the order is going to, recompute the
        // fee from the backend's own pincode config rather than trusting
        // whatever the client sent — a stale app, a skipped validation step,
        // or simple tampering would otherwise silently ship a ₹0 (or wrong)
        // delivery charge straight into the final order. If no pincode is
        // given (older client) we fall back to the client-supplied charge, as
        // before.
        $charge = (float) $request->charge;
        if ($request->filled('pincode')) {
            $pincodeRecord = Pincode::where('pincode', $request->pincode)->first();
            if ($pincodeRecord) {
                $charge = (float) $pincodeRecord->delivery_fee + (float) $pincodeRecord->other_fee;
            }
        }

        $orderBook = OrderBook::create([
            'user_id'              => $request->user_id,
            'customer'             => $request->user_id,
            // These two columns are NOT NULL with a 'na' default in the DB — an
            // explicit null here (e.g. a client that omits active_address, or
            // sends '' which Laravel's ConvertEmptyStringsToNull middleware
            // turns into null) would override that default and throw a SQL
            // integrity-constraint 500, so fall back explicitly.
            'user'                 => $request->active_address ?? 'na',
            'pack_user'            => $request->address_type ?? 'na',
            'delivery_address'     => $request->active_address,
            'delivery_pincode'     => $request->pincode,
            'delivery_landmark'    => $request->landmark,
            'delivery_latitude'    => $request->latitude,
            'delivery_longitude'   => $request->longitude,
            'receiver_name'        => $request->receiver_name ?? optional($user)->name,
            'receiver_phone'       => $request->receiver_phone ?? optional($user)->mobile,
            'delivery_instruction' => $request->delivery_instruction,
            'invoice_dt'           => Carbon::today()->toDateString(),
            'finyear'              => $request->finyear ?? '2023-24',
            'status'               => 'order',
            'invoice'              => (OrderBook::max('invoice') ?? 100) + 1,
            'value'                => (float) $orderSum,
            'charge'               => $charge,
            'coupon'               => (float) $request->coupon,
            'payment_amount'       => $orderSum + $charge - $request->coupon,
            'del_dt'               => $request->date,
            'ref'                  => $request->time_slot_id,
            'ref1'                 => $timeslot->time_slot,
            'payment_status'       => $request->payment_status,
            'payment_mode'         => $request->payment_mode,
            'payment_ref'          => $request->payment_ref,
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
            new OrderBookResource(OrderBook::with('orders.food')->find($orderBook->id)),
            'Order created successfully',
            201
        );
    }

    public function orderBookList(Request $request)
    {
        return $this->success(
            OrderBookResource::collection(
                OrderBook::with('orders.food')
                    ->where('user_id', $request->user_id)
                    ->whereIn('status', ['order', 'delivered', 'cancel'])
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

        $timeslot   = TimeSlot::find($orderBook->ref);
        $now        = Carbon::now();
        $cutoffHour = optional($timeslot)->cutoff;

        $deliveryDatePassed = $orderBook->del_dt < $now->toDateString();
        $cutoffPassedToday  = $orderBook->del_dt === $now->toDateString()
            && $cutoffHour !== null
            && (int) $now->format('H') >= (int) $cutoffHour;

        if ($deliveryDatePassed || $cutoffPassedToday) {
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
        $user = User::find($request->user_id);

        Order::where('order_book_id', $request->order_book_id)->each(function ($item) use ($orderBook, $request) {
            $item->update(['status' => 'cancel']);
            Notification::create([
                'food_id'       => $item->food_id,
                'order_id'      => $item->id,
                'user_id'       => $request->user_id,
                'order_book_id' => $orderBook->id,
                'message'       => 'Product Cancelled',
                'general'       => 'no',
                'status'        => 'yes',
            ]);
        });

        // Admin alert — single summary row per cancellation, surfaced in the admin navbar bell.
        Notification::create([
            'user_id'       => $request->user_id,
            'order_book_id' => $orderBook->id,
            'message'       => ($user->name ?? 'A customer') . " cancelled Order #{$orderBook->id} (₹" . number_format($refundAmount, 2) . ' refunded to wallet)',
            'general'       => 'no',
            'status'        => 'yes',
            'is_read'       => false,
        ]);

        try {
            $message = "Dear {$user->name}, Your order #{$request->order_book_id} has been cancelled. Refund will be credited to your wallet.";
            $this->smsService->sendSms($user->mobile, $message, '1207171377044163620');
        } catch (\Exception $e) {
            \Log::error('Cancel SMS failed: ' . $e->getMessage());
        }

        return $this->success(null, 'Order cancelled successfully');
    }

    public function ordersHistory(Request $request)
    {
        // The shipped app's home-screen "active order" popup (getLastOrder() in
        // ready_to_cook/screen/original.dart) reads this response as
        // `{ "orders": [...] }` — not the standard `data` envelope — then
        // filters client-side for status == 'order' to decide whether to show
        // itself. That top-level key was always missing, so the request threw,
        // was swallowed by a catch-all, and getLastOrder() silently returned
        // null. Whatever stale "View Order" banner testers saw was therefore
        // coming from a locally cached value, never revalidated against this
        // (correctly status-filtered) endpoint. Adding the `orders` key lets
        // the app's own status filter run against real data.
        $orderBooks = OrderBook::with('orders.food')
            ->where('user_id', $request->user_id)
            ->whereIn('status', ['order', 'delivered', 'cancel'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => OrderBookResource::collection($orderBooks),
            'orders'  => OrderBookResource::collection($orderBooks),
        ]);
    }

    public function userWallet(Request $request)
    {
        $balance = Wallet::where('user_id', $request->user_id)
            ->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as balance')
            ->value('balance');

        return $this->success(['balance' => $balance]);
    }
}
