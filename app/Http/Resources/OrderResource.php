<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'food_id'       => $this->food_id,
            'user_id'       => $this->user_id,
            'qty'           => $this->qty,
            'price'         => $this->price,
            'total'         => $this->total,
            'discount'      => $this->discount,
            'gst_value'     => $this->gst_value,
            'status'        => $this->status,
            'order_book_id' => $this->order_book_id,
            'date'          => $this->date,
            'food'          => new FoodResource($this->whenLoaded('food')),
        ];
    }
}
