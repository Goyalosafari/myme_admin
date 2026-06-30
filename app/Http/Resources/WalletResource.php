<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'debit'       => $this->debit,
            'credit'      => $this->credit,
            'date'        => $this->date,
            'description' => $this->description,
            'status'      => $this->status,
        ];
    }
}
