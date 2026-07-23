<?php

namespace App\Http\Resources;

use App\Models\Address;
use App\Models\LoyaltyTransaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        $addresses = Address::where('user_id', $this->id)
            ->where('status', 1)
            ->get()
            ->map(function ($address) {
                return [
                    'id'          => $address->id,
                    'type'        => $address->type,
                    'address'     => $address->address ?? '',
                    'pincode'     => $address->pincode ?? '',
                    'landmark'    => $address->landmark ?? '',
                    'name'        => $address->name ?? '',
                    'instruction' => $address->instruction,
                    'phone'       => $address->phone ?? '',
                    'status'      => $address->status,
                    'latitude'    => $address->latitude,
                    'longitude'   => $address->longitude,
                ];
            })
            ->values();

        $loyaltyPoints = LoyaltyTransaction::balanceFor($this->id);

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
            'loyalty_points'  => $loyaltyPoints,
            'coin'            => $loyaltyPoints,
            'wallet_balance'  => Wallet::where('user_id', $this->id)
                ->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as balance')
                ->value('balance'),
        ];
    }
}
