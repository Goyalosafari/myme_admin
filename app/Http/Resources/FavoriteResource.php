<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'food_id' => $this->food_id,
            'user_id' => $this->user_id,
        ];
    }
}
