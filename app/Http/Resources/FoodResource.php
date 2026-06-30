<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'price'       => $this->price,
            'offer_price' => $this->offer_price,
            'image'       => $this->image,
            'type'        => $this->type,
            'category_id' => $this->category_id,
            'offer'       => $this->offer,
            'ref'         => $this->ref,
            'description' => $this->description,
            'category'    => new CategoryResource($this->whenLoaded('category')),
        ];
    }
}
