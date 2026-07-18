<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'food_id' => $this->food_id,
            'title'   => $this->title,
            'image'            => 'storage/' . $this->image,
            'food'    => new FoodResource($this->whenLoaded('food')),
        ];
    }
}
