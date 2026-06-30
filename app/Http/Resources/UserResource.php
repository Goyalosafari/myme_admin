<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $addresses = [];

        if (!empty($this->address1)) {
            $addresses[] = [
                'type'     => 'home',
                'address'  => $this->address1,
                'pincode'  => $this->pincode1  ?? '',
                'landmark' => $this->landmark1 ?? '',
                'phone'    => $this->mobile    ?? '',
            ];
        }

        if (!empty($this->address2)) {
            $addresses[] = [
                'type'     => 'work',
                'address'  => $this->address2,
                'pincode'  => $this->pincode2  ?? '',
                'landmark' => $this->landmark2 ?? '',
                'phone'    => $this->mobile    ?? '',
            ];
        }

        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'mobile'          => $this->mobile,
            'address1'        => $this->address1,
            'address2'        => $this->address2,
            'pincode1'        => $this->pincode1,
            'pincode2'        => $this->pincode2,
            'landmark1'       => $this->landmark1,
            'landmark2'       => $this->landmark2,
            'active_addr'     => $this->active_addr,
            'status'          => $this->status,
            'mobile_verified' => $this->mobile_verified,
            'addresses'       => $addresses,
        ];
    }
}
