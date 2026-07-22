<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PincodeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'pincode'      => $this->pincode,
            'place_name'   => $this->place_name,
            'district'     => $this->district,
            'state'        => $this->state,
            'delivery_fee' => $this->delivery_fee,
            'other_fee'    => $this->other_fee,
            'status'       => $this->status,
        ];
    }
}
