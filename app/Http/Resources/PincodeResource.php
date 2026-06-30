<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PincodeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'pincode' => $this->pincode,
            'area'    => $this->area,
            'status'  => $this->status,
        ];
    }
}
