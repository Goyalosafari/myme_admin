<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeslotResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'time_slot'  => $this->time_slot,
            'cutoff'     => $this->cutoff,
            'time_start' => $this->time_start,
            'time_end'   => $this->time_end,
            'ref1'       => $this->ref1,
            'status'     => $this->status,
        ];
    }
}
