<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'image'            => $this->image,
            'display_location' => $this->display_location,
            'category'         => new CategoryResource($this->whenLoaded('category')),
        ];
    }
}
