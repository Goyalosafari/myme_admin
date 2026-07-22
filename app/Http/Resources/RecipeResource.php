<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'food_id'           => $this->food_id,
            'category_id'       => $this->category_id,
            'title'             => $this->title,
            'num_of_serving'    => $this->num_of_serving,
            'description'       => $this->description,
            'ingredients'       => $this->ingredients,
            'nutritional_facts' => $this->nutritional_facts,
            'utensils'          => $this->utensils,
            'image'             => 'storage/' . $this->image,
            'food'              => new FoodResource($this->whenLoaded('food')),
        ];
    }
}
