<?php

namespace App\Http\Controllers\Api;
use App\Models\Notification;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderBook;
use App\Models\Wallet;
use App\Models\TimeSlot;
use App\Http\Controllers\Controller;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderBookApiController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

public function smstest(Request $request)
{
    // Validate that both message and mobile number are provided
    $validator = Validator::make($request->all(), [
        'mobile' => 'required|regex:/^[6-9][0-9]{9}$/|digits:10',
        'message' => 'required|string|max:160', // Ensure the message is not empty and within reasonable length
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }

    // Get mobile number and message from the request
    $mobileNumber = $request->input('mobile');
    $message = $request->input('message');

    // Template ID
    $templateID = '1207171376155379572'; // This can remain static or come from the request if you want

    try {
        // Call the SmsService to send the SMS
        $response = $this->smsService->sendSms($mobileNumber, $message, $templateID);

        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'SMS sent successfully',
            'response' => $response,  // Return the response from the SMS service
        ], 200);
    } catch (\Exception $e) {
        // Handle any errors
        return response()->json([
            'status' => 'error',
            'message' => 'SMS sending failed',
            'error' => $e->getMessage(),
        ], 500);
    }
}
    
   
   public function smsorder(Request $request)
{
    // Validate that both message and mobile number are provided
    $validator = Validator::make($request->all(), [
        'mobile' => 'required|regex:/^[6-9][0-9]{9}$/|digits:10',
        'message' => 'required|string|max:320', // Ensure the message is not empty and within reasonable length
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }

    // Get mobile number and message from the request
    $mobileNumber = $request->input('mobile');
    $message = $request->input('message');

    // Template ID
    $templateID = '1207171377328523771'; 

    try {
        // Call the SmsService to send the SMS
        $response = $this->smsService->sendSms($mobileNumber, $message, $templateID);

        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'SMS sent successfully',
            'response' => $response,  // Return the response from the SMS service
        ], 200);
    } catch (\Exception $e) {
        // Handle any errors
        return response()->json([
            'status' => 'error',
            'message' => 'SMS sending failed',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function smscancel(Request $request)
{
    // Validate that both message and mobile number are provided
    $validator = Validator::make($request->all(), [
        'mobile' => 'required|regex:/^[6-9][0-9]{9}$/|digits:10',
        'message' => 'required|string|max:320', // Ensure the message is not empty and within reasonable length
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }

    // Get mobile number and message from the request
    $mobileNumber = $request->input('mobile');
    $message = $request->input('message');

    // Template ID
    $templateID = '1207171377044163620'; 

    try {
        // Call the SmsService to send the SMS
        $response = $this->smsService->sendSms($mobileNumber, $message, $templateID);

        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'SMS sent successfully',
            'response' => $response,  // Return the response from the SMS service
        ], 200);
    } catch (\Exception $e) {
        // Handle any errors
        return response()->json([
            'status' => 'error',
            'message' => 'SMS sending failed',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public function createOrderBook(Request $request)
    {
        $requestData = $request->all();

        if (!is_array($requestData)) {
            return response(['error' => 'Invalid request data'], 400);
        }

        // Check if an order book already exists for this user with the same cart sum
        $existingOrderBook = OrderBook::where('user_id', $requestData['user_id'])
            ->where('value', function ($query) use ($requestData) {
                $query->selectRaw('SUM(total)')
                    ->from('orders')
                    ->where('user_id', $requestData['user_id'])
                    ->where('status', 'cart');
            })
            ->where('status', 'order')
            ->first();

        if ($existingOrderBook) {
            return response(['message' => 'Order book already exists', 'oderBook' => $existingOrderBook], 200);
        }

        // Set common values in $requestData
        $requestData['user_id'] = $requestData['user_id'];
        $requestData['customer'] = $requestData['user_id'];
        $requestData['user'] = $requestData['active_address'];
        $requestData['pack_user'] = $requestData['address_type'];
        $requestData['invoice_dt'] = now()->format('Y-m-d');
        $requestData['finyear'] = $requestData['finyear'] ?? "2023-24";
        $requestData['status'] = 'order';
        $requestData['invoice'] = OrderBook::max('invoice') + 1 ?? 100;

        // Calculate order sum
        $orderSum = Order::where('user_id', $requestData['user_id'])
            ->where('status', 'cart')->sum('total');
        $requestData['value'] = (float) $orderSum;
        $requestData['wallet'] = (float) $requestData['wallet'];
        $requestData['charge'] = (float) $requestData['charge'];
        $requestData['coupon'] = (float) $requestData['coupon'];
        $requestData['payment_amount'] = $orderSum + $requestData['charge'] - $requestData['coupon'];

        $timeslotData = TimeSlot::find($requestData['time_slot_id']);
        $requestData['del_dt'] = $requestData['date'];
        $requestData['ref'] = $requestData['time_slot_id'];
        $requestData['ref1'] = $timeslotData->time_slot;

        // Validate the incoming request data
        $validator = Validator::make($requestData, [
            'invoice_dt' => 'nullable|date',
            'charge' => 'required',
            'coupon' => 'required',
            'wallet' => 'required',
            'user' => 'nullable|string',
            'finyear' => 'string',
            'payment_status' => 'required',
            'payment_mode' => 'required',
            'payment_ref' => 'nullable|string',
            'del_dt' => 'required',
            'ref' => 'required',
            'ref1' => 'required',
            'user' => 'nullable|string',
            'pack_user' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors()], 400);
        }

        $orderBook = OrderBook::create($requestData);

        $updateOrder = Order::where('user_id', $requestData['user_id'])
            ->where('status', 'cart')
            ->update([
                'status' => 'order',
                'order_book_id' => $orderBook->id,
                'order_id' => $orderBook->id,
                'time_slot_id' => $requestData['time_slot_id'],
                'date' => $requestData['date']
            ]);

        // Rest of the method (wallet, notification, SMS logic) remains the same...
        // ...

        $orderBookData = OrderBook::find($orderBook->id);
        return response(['message' => 'Order created successfully', 'oderBook' => $orderBookData], 201);
    }

    /*
    public function createOrderBook(Request $request)
    {
        $requestData = $request->all();

        if(!is_array($requestData))
        {
            return response(['error'=> 'Invalid request data'],400);
        }

      //  $userData = User::find(Auth::id());       
        
        // Set common values in $requestData
        //$requestData['user_id'] = Auth::id();
        $requestData['user_id'] = $requestData['user_id'];
        //$requestData['customer'] = $userData->name;
        $requestData['customer'] = $requestData['user_id'];
        $requestData['invoice_dt'] = now()->format('Y-m-d');
        $requestData['finyear'] = $requestData['finyear'] ?? "2023-24";
        $requestData['status'] = 'order';
        //calculate invoice number
        $requestData['invoice'] = OrderBook::max('invoice') + 1 ?? 100;

        //calculate order sum
       // $orderSum  = Order::where('user_id',Auth::id())
        $orderSum  = Order::where('user_id',$request->user_id)
        ->where('status', 'cart')->sum('total');
        $requestData['value'] = (float) $orderSum;

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
        
        foreach($request->orders as $order)
        {
            $updateOrder = Order::where('id',$order)->update([
                'status'=>'order',
                'order_book_id' => $orderBook->id,
                'order_id' => $request->order_id
            ]);
        }

        $orderBookData = OrderBook::find($orderBook->id);

        return response(['message' => 'Order created successfully', 'oderBook'=>$orderBookData], 201);
    }*/

    public function orderBookList(Request $request)
    {
        $orders = OrderBook::with('orders')
        ->where('user_id', $request->user_id)
        ->where('order_id',$request->order_id)
        ->where('status', 'order')
        ->get();
        return response()->json($orders, 200);
    }
    
    
    
        public function cancelOrder(Request $request)
    {
        $date = Carbon::now();
        $orderBookData = OrderBook::find($request->order_book_id);
        $timeslotData = TimeSlot::find($orderBookData->ref);

   	if ($orderBookData->status !== 'order') {
        $status = 'you can not cancel this order';
    	}

        if($orderBookData->del_dt < $date->format('Y-m-d') ||
        ($orderBookData->del_dt == $date->format('Y-m-d') && $date->format('H') > $timeslotData->ref1))
        {
            $status = 'you can not cancel this order';
        }
        else
        {
            $walletAmtByOrderBook = Wallet::find($orderBookData->wallet_id);
            $orderSum = $orderBookData['payment_amount'] + $walletAmtByOrderBook;
            $wallet = Wallet::create([
                'user_id' =>  $request->user_id,
                'debit' => $orderSum,
                'date' => Carbon::today(),
                'description' => $request->description
            ]);

            $updateOrderBook = OrderBook::where('id', $request->order_book_id)
            ->update(['status' => 'cancel']);

            $ordersList = Order::where('order_book_id', $request->order_book_id)->get();
            foreach($ordersList as $list){
                $order_id = $list->id;
                Order::where('id',$order_id)
                ->update(['status' => 'cancel']);
                Notification::create([
                    'food_id' => $list->food_id,
                    'order_id' =>$order_id,
                    'message' => 'Product Cancelled',
                    'general' => 'no',
                    'status' => 'yes'
                ]);
            }
            $status = 'success';
            
            $userData = User::find($request->user_id);   
            $mobileNumber = $userData->mobile; 
            $templateID = '1207171377044163620';
            $message = "Dear {$userData->name},
            We've successfully cancelled your order #{$request->order_book_id}. The amount for the cancelled items has been added to your wallet balance. You can use this balance for your future purchases.
            If you have any questions or need further assistance, please feel free to contact us.
            Thank you for your understanding.
            Best regards,
            MYME BUSINESS CORPORATION PRIVATE LIMITED";
            try {
                $response = $this->smsService->sendSms($mobileNumber, $message, $templateID);
            } catch (\Exception $e) {
                \Log::error('SMS sending failed: ' . $e->getMessage());
            }
        }

        return response()->json(['status' => $status ]);
    }
    
    
   /* 
    public function cancelOrder(Request $request)
    {
             $wallet = Wallet::create([
            'user_id' =>  $request->user_id,
            'debit' => $request->debit,
            'date' => Carbon::today(),
            'description' => $request->description
        ]);
        $updateOrderBook = OrderBook::where('id', $request->order_book_id)
        ->update(['status' => 'cancel']);

        $ordersList = Order::where('order_book_id', $request->order_book_id)->get();
        foreach($ordersList as $list){
            $order_id = $list->id;
            Order::where('id',$order_id)
            ->update(['status' => 'cancel']);
            Notification::create([
                'food_id' => $list->food_id,
                'order_id' =>$order_id,
                'message' => 'Product Cancelled',
                'general' => 'no',
                'status' => 'yes'
            ]);
        }
        return response()->json(['status' => 'success']);
    }*/
    
    public function ordersHistory(Request $request)
   {
         $orders = OrderBook::where('user_id', $request->user_id)
        // ->where('status','order')
        ->orderBy('id', 'desc')->get();
        return response()->json(['orders' => $orders], 200);
    }
    
        public function userWallet(Request $request)
   {
       
        $debitSum = Wallet::where('user_id', $request->user_id)
        ->sum('debit');
        $creditSum = Wallet::where('user_id', $request->user_id)
        ->sum('credit');
        $walletSum = $debitSum- $creditSum;

            return response()->json(['sum' => $walletSum], 200);
            
   } 


public function sendOtp(Request $request)
{
    // Validate the mobile number input
    $validator = Validator::make($request->all(), [
        'mobile' => 'required|regex:/^[6-9][0-9]{9}$/|digits:10',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => 'Invalid mobile number'], 422);
    }

    $mobileNumber = $request->input('mobile');

    // Generate a random 4-digit OTP
    $otp = rand(1000, 9999);

    // Construct the SMS message
    $message = "Your OTP for MYME is {$otp}. Please enter this code to verify your mobile number. Do not share this code with anyone.";

    // SMS API URL
    $url = 'http://sms.ssdweb.in/api/sendhttp.php';

    // Parameters for the API request
    $postData = [
        'authkey' => '363198AEffi4vd673c70cdP1', // Replace with your actual authkey
        'mobiles' => $mobileNumber,
        'message' => $message,
        'sender' => 'MYME01', // Replace with your sender ID
        'route' => '4',
        'DLT_TE_ID' => '1207171376155379572',
        'country' => '91',
    ];

    try {
        // Send the POST request using HTTP client
        $response = Http::asForm()->post($url, $postData);

        // Check the response status
        if ($response->successful()) {
            // Return a success response with the OTP (for development or testing purposes)
            return response()->json([
                'status' => 'success',
                'message' => 'OTP sent successfully',
                'otp' => $otp, // Remove this in production
            ]);
        } else {
            // Return an error if the request fails
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send SMS. Please try again.',
            ], 500);
        }
    } catch (\Exception $e) {
        // Handle exceptions and return an error
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while sending the OTP',
            'error' => $e->getMessage(),
        ], 500);
    }
}

       
     /* $wallets = Wallet::where('user_id', $request->user_id)->get();

            $total = 0;
            foreach ($wallets as $wallet) {
            $total += $wallet->debit - $wallet->credit;
            }

            return response()->json(['sum' => $total], 200);*/
            
    
  /*  public function cancelOrder(Request $request)
    {
        $wallet = Wallet::create([
            'user_id' => $request->user_id,
            'debit' => $request->debit,
            'date' => Carbon::today(),
            'description' => $request->description
        ]);
        $updateOrderBook = OrderBook::where('id', $request->order_book_id)
        ->update(['status', 'cancel']);
        return response()->json(['status' => 'success']);
    }*/
}
