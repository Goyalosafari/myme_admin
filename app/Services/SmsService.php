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
        $body     = $response->body();

        if (!$response->successful()) {
            throw new \Exception('Error sending SMS: ' . $body);
        }

        // This gateway returns HTTP 200 even for rejected requests (bad auth key,
        // insufficient balance, invalid DLT template, etc.) — the real result is
        // only visible in the response body, e.g. {"msg":"...","msgType":"error"}.
        $decoded = json_decode($body, true);
        if (is_array($decoded) && ($decoded['msgType'] ?? null) === 'error') {
            throw new \Exception('SMS gateway rejected the request: ' . ($decoded['msg'] ?? $body));
        }

        return $body;
    }
}