<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        return $this->success(NotificationResource::collection(Notification::all()));
    }

    public function filterNotification()
    {
        return $this->success(
            NotificationResource::collection(
                Notification::where('status', 'yes')->where('general', 'yes')->get()
            )
        );
    }

    public function filterNotificationByOrder(Request $request)
    {
        return $this->success(
            NotificationResource::collection(
                Notification::where('order_id', $request->order_id)->where('general', 'no')->get()
            )
        );
    }
}
