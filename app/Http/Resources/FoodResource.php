<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'calorie'      => $this->calorie,
            'cooking_time' => $this->cooking_time,
            'taste'        => $this->taste,
            'price'        => $this->price,
            'offer_price'  => $this->offer_price,
            'mrp'          => $this->mrp,
            'margin'       => $this->margin,
            'gst'          => $this->gst,
            'gst_value'    => $this->gst_value,
            'veg'          => $this->veg,
            'preferences'  => $this->preferences,
            'meal_type'    => $this->meal_type,
            'food_details' => $this->food_details,
            'image'        => 'storage/' . $this->image,
            'type'         => $this->type,
            'category_id'  => $this->category_id,
            'offer'        => $this->offer,
            'ref'          => $this->ref,
            'category'     => new CategoryResource($this->whenLoaded('category')),
        ];
    }
}
