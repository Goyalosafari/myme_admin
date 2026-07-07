<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'food_id'    => $this->food_id,
            'order_id'   => $this->order_id,
            'message'    => $this->message,
            'general'    => $this->general,
            'status'     => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
