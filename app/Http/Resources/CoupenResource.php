<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoupenResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'code'          => $this->coupen_code,
            'title'         => $this->title,
            'no_of_usage'   => $this->no_of_usage,
            'discount_type' => $this->discount_type,
            'discount'      => $this->discount,
            'max_discount'  => $this->max_discount,
            'min_amount'    => $this->min_amount,
            'from_date'     => $this->from_date,
            'to_date'       => $this->to_date,
        ];
    }
}
