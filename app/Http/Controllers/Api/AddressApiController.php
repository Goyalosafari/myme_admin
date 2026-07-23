<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressApiController extends Controller
{
    public function update(Request $request)
    {
        $address = Address::where('id', $request->address_id)
            ->where('user_id', $request->user_id)
            ->first();

        if (!$address) {
            return response(['message' => 'Address not found'], 404);
        }

        $address->update([
            'name'        => $request->name,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'pincode'     => $request->pincode,
            'landmark'    => $request->landmark,
            'instruction' => $request->instruction,
            'type'        => $request->type ?? $address->type,
            'status'      => $request->status ?? $address->status,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
        ]);

        return response(['message' => 'Address updated successfully'], 200);
    }

    public function destroy(Request $request)
    {
        $address = Address::where('id', $request->address_id)
            ->where('user_id', $request->user_id)
            ->first();

        if (!$address) {
            return response(['message' => 'Address not found'], 404);
        }

        $address->delete();

        return response(['message' => 'Address deleted successfully'], 200);
    }
}
