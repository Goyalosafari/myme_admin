<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsService
{
    protected $authKey;
    protected $senderId;
    protected $route;
    protected $url;

    public function __construct()
    {
        $this->authKey = '363198AEffi4vd673c70cdP1';
        $this->senderId = 'MYME01';
        $this->route = '4';
        $this->url = 'http://sms.ssdweb.in/api/sendhttp.php';
    }

    public function sendSms($mobileNumber, $message, $templateID)
    {
        $message = urlencode($message);
        $postData = [
            'authkey' => $this->authKey,
            'mobiles' => $mobileNumber,
            'message' => $message,
            'sender' => $this->senderId,
            'route' => $this->route,
            'DLT_TE_ID' => $templateID,
            'country' => '91' 
        ];
        
        $response = Http::asForm()->post($this->url, $postData);
        
        if ($response->successful()) {
            return $response->body();
        } else {
            throw new \Exception('Error sending SMS: ' . $response->body());
        }
    }
}