<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoupenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'code'       => $this->code,
            'title'      => $this->title,
            'discount'   => $this->discount,
            'min_amount' => $this->min_amount,
            'from_date'  => $this->from_date,
            'to_date'    => $this->to_date,
        ];
    }
}
