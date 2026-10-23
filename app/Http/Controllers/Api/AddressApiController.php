<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressApiController extends Controller
{
    private const FIELDS = ['name', 'phone', 'address', 'pincode', 'landmark', 'instruction', 'type', 'status', 'latitude', 'longitude'];

    // Save an address from the app's map screen. With an address id it edits
    // that address; without one it adds a new address for the user (the app
    // uses the same screen for both, and "add" used to 404 here).
    public function update(Request $request)
    {
        // The app's address objects carry the id as "id" (UserResource), so accept both.
        $addressId = $request->input('address_id', $request->input('id'));

        $validator = Validator::make($request->all(), [
            'user_id'     => 'required|exists:users,id',
            'name'        => 'nullable|string|max:255',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string|max:1000',
            'pincode'     => 'nullable|string|max:20',
            'landmark'    => 'nullable|string|max:1000',
            'instruction' => 'nullable|string|max:1000',
            'type'        => 'nullable|string|max:50',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()->first(), 'errors' => $validator->errors()], 422);
        }

        $data = $this->addressData($request);

        if ($addressId) {
            $address = Address::where('id', $addressId)->where('user_id', $request->user_id)->first();

            if (!$address) {
                return response(['message' => 'Address not found'], 404);
            }

            // Only touch what was sent, so a partial edit doesn't wipe the rest.
            $address->update($data);

            return response(['message' => 'Address updated successfully', 'address' => $address->fresh()], 200);
        }

        $address = Address::create($data + [
            'user_id' => $request->user_id,
            'type'    => 'home',
            'status'  => 1,
        ]);

        // 200 (not 201): the app treats any non-200 from this screen as "Failed to update address".
        return response(['message' => 'Address added successfully', 'address' => $address->fresh()], 200);
    }

    public function destroy(Request $request)
    {
        $address = Address::where('id', $request->input('address_id', $request->input('id')))
            ->where('user_id', $request->user_id)
            ->first();

        if (!$address) {
            return response(['message' => 'Address not found'], 404);
        }

        $address->delete();

        return response(['message' => 'Address deleted successfully'], 200);
    }

    private function addressData(Request $request): array
    {
        $data = [];
        foreach (self::FIELDS as $field) {
            if ($request->exists($field)) {
                $data[$field] = $request->input($field);
            }
        }

        if (array_key_exists('type', $data)) {
            $data['type'] = $data['type'] ? strtolower($data['type']) : 'home';
        }

        // Apps send true/"true"/"1"/1 — the column is a tinyint.
        // Null/blank means "not sent" — never hide the address by accident.
        if (array_key_exists('status', $data)) {
            if ($data['status'] === null || $data['status'] === '') {
                unset($data['status']);
            } else {
                $data['status'] = filter_var($data['status'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === false ? 0 : 1;
            }
        }

        return $data;
    }
}
