<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeslotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'time_slot' => $this->time_slot,
            'ref1'      => $this->ref1,
            'date'      => $this->date,
            'status'    => $this->status,
        ];
    }
}
