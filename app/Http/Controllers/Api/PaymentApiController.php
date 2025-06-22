<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class PaymentApiController extends Controller
{

    public function index(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    
        $orderData = [
            'receipt'         => 'rcptid_11',
            'amount'          => 100, // amount in the smallest currency unit (e.g., paise for INR)
            'currency'        => 'INR',
            'payment_capture' => 1 // auto capture
        ];
    
        $razorpayOrder = $api->order->create($orderData);
    
        return response()->json([
            'order_id' => $razorpayOrder['id'],
            'razorpay_key' => env('RAZORPAY_KEY')
        ]);
    }

    public function storeTest(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $razorpayOrder = $api->order->create([
            'receipt' => $request->invoice,
            'amount' => $request->payment_amount * 100, // Amount in paisa
            'currency' => 'INR',
            'payment_capture' => 1 // Auto capture
        ]);
        return response()->json(['status' => 'success','razorpayOrder'=>$razorpayOrder['id']]);
    }
    public function store(Request $request)
    {
        $input = $request->all();
  
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
  
        $payment = $api->payment->fetch($input['razorpay_payment_id']);
  
        if(count($input)  && !empty($input['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount'=>$payment['amount'])); 
                // $member = new Member();
                // $member->name = auth()->user()->name;
                // $member->mob = $payment->contact;
                // $member->user_id = auth()->id();
                // $member->email = $payment->email;
                // $member->credit = $payment->amount ? number_format($payment->amount/100, 2) : 0;
                // $member->ref = $response->id;
                // $member->batch = "";
                // $member->status = $payment->captured;
                // $member->pay_status = "Completed";
                // $member->payment_ref = $response->id;
                // $member->payment_mode = $payment->method;
                // $member->package = $response->description;
                // $member->start_dt = Carbon::now();
                // $member->end_dt = Carbon::now()->addYear();
                // $member->flag = "";
                // $member->created = Carbon::now();
                // $member->bell = 0;
                // $member->user_tp = "user";
                // $member->prd = "";
                // $member->save();

                $data = [
                    'url' => route('login'),
                    'subject' => 'Payment',
                    'name'  => auth()->user()->name,
                ];
        
                // Mail::send('vendor.mail.payment.html.layout', $data, function ($message) use ($data) {
                //     $message->to(auth()->user()->email);
                //     $message->subject($data['subject']);
                // });

                //Auth::logout();

                return redirect('login')->with('message', 'Your payement has been success!');
  
            } catch (Exception $e) {
                return  $e->getMessage();
                Session::put('error',$e->getMessage());
                return redirect()->back();
            }
        }
          
        
    }
}
