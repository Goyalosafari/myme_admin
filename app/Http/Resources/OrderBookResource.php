<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderBookResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'invoice'          => $this->invoice,
            'invoice_dt'       => $this->invoice_dt,
            'value'            => $this->value,
            'charge'           => $this->charge,
            'coupon'           => $this->coupon,
            'payment_amount'   => $this->payment_amount,
            'payment_status'   => $this->payment_status,
            'payment_mode'     => $this->payment_mode,
            'payment_ref'      => $this->payment_ref,
            'del_dt'           => $this->del_dt,
            'ref1'             => $this->ref1,
            'status'           => $this->status,
            'user_id'          => $this->user_id,
            'delivery_address' => $this->user,
            'address_type'     => $this->pack_user,
            'created_at'       => $this->created_at,
            'orders'           => OrderResource::collection($this->whenLoaded('orders')),
        ];
    }
}
