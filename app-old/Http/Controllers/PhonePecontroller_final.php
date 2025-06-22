<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;

class PhonePecontroller extends Controller
{
    public function phonePe()
    {
        $data = array (
          'merchantId' => 'M1DTTAOY5F1E',
          'merchantTransactionId' => uniqid(),
          'merchantUserId' => 'MUID123',
          'amount' => 1000,
          'redirectUrl' => route('response'),
          'redirectMode' => 'POST',
          'callbackUrl' => route('response'),
          'mobileNumber' => '9999999999',
          'paymentInstrument' => 
          array (
            'type' => 'PAY_PAGE',
          ),
        );

        $encode = base64_encode(json_encode($data));

        $saltKey = '6a0c40bc-a687-4fe6-ab04-b552f00cacb0';
        $saltIndex = 1;

        $string = $encode.'/pg/v1/pay'.$saltKey;
        $sha256 = hash('sha256',$string);

        $finalXHeader = $sha256.'###'.$saltIndex;

        //https://api-preprod.phonepe.com/apis/pg-sandbox
        //https://api-preprod.phonepe.com/apis/merchant-simulator

        $response = Curl::to('https://api.phonepe.com/apis/hermes/pg/v1/pay')
                ->withHeader('Content-Type:application/json')
                ->withHeader('X-VERIFY:'.$finalXHeader)
                ->withData(json_encode(['request' => $encode]))
                ->post();
 //dd($response);
        $rData = json_decode($response);

        return redirect()->to($rData->data->instrumentResponse->redirectInfo->url);

    }

    public function response(Request $request)
    {
        $input = $request->all();

        $saltKey = '6a0c40bc-a687-4fe6-ab04-b552f00cacb0';
        $saltIndex = 1;

        $finalXHeader = hash('sha256','/pg/v1/status/'.$input['merchantId'].'/'.$input['transactionId'].$saltKey).'###'.$saltIndex;

        $response = Curl::to('https://api.phonepe.com/apis/hermes/pg/v1/status/'.$input['merchantId'].'/'.$input['transactionId'])
                ->withHeader('Content-Type:application/json')
                ->withHeader('accept:application/json')
                ->withHeader('X-VERIFY:'.$finalXHeader)
                ->withHeader('X-MERCHANT-ID:'.$input['transactionId'])
                ->get();

        dd(json_decode($response));
    }


    public function refundProcess($tra_id)
    {
        $payload = [
           'merchantId' => 'M1DTTAOY5F1E',
           'merchantUserId' => 'MUID123',
           'merchantTransactionId' => ($tra_id),
           'originalTransactionId' => strrev($tra_id),
           'amount' => 5000,
           'callbackUrl' => route('response'),
        ];

        $encode = base64_encode(json_encode($payload));

        $saltKey = '6a0c40bc-a687-4fe6-ab04-b552f00cacb0';
        $saltIndex = 1;

        $string = $encode.'/pg/v1/refund'.$saltKey;
        $sha256 = hash('sha256',$string);

        $finalXHeader = $sha256.'###'.$saltIndex;

        $response = Curl::to('https://api.phonepe.com/apis/hermes/pg/v1/refund')
                ->withHeader('Content-Type:application/json')
                ->withHeader('X-VERIFY:'.$finalXHeader)
                ->withData(json_encode(['request' => $encode]))
                ->post();

        $rData = json_decode($response);



        $finalXHeader1 = hash('sha256','/pg/v1/status/'.'MERCHANTUAT'.'/'.$tra_id.$saltKey).'###'.$saltIndex;

        $responsestatus = Curl::to('https://api.phonepe.com/apis/hermes/pg/v1/status/'.'MERCHANTUAT'.'/'.$tra_id)
                ->withHeader('Content-Type:application/json')
                ->withHeader('accept:application/json')
                ->withHeader('X-VERIFY:'.$finalXHeader1)
                ->withHeader('X-MERCHANT-ID:'.$tra_id)
                ->get();

        dd(json_decode($response),json_decode($responsestatus));
        // dd($rData);
    }
}
