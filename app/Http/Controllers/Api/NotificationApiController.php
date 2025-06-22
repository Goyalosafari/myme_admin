<?php

namespace App\Http\Controllers\Api;
use App\Models\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    public function index(Request $request)
    {
        $data = Notification::all();
        return response()->json(['data' => $data]);
    }
    public function filterNotification(Request $request)
    {
        $data = Notification::where('status','yes')
        ->where('general', 'yes')
        ->get();
        return response()->json(['data' => $data]);

    }
    public function filterNotificationByOrder(Request $request)
    {
        $data = Notification::where('order_id', $request->order_id)
        //->where('status','yes')
        ->where('general', 'no')
        ->get();
        return response()->json(['data' => $data]);

    }
}