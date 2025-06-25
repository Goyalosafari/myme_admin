<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Services\SmsService;

use Illuminate\Http\Request;

class RegisterApiController extends Controller
{
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name'                 => ['required', 'string', 'max:255'],
            'email'                => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'             => ['required', 'string', 'min:8'],
            'mobile'               => ['nullable', 'string', 'max:255'],
            'referred_by'          => ['nullable', 'exists:users,id'], // optional but must be a valid user ID if present
            'addresses'            => ['required', 'array', 'min:1'],
            'addresses.*.address'  => ['required', 'string', 'max:255'],
            'addresses.*.pincode'  => ['nullable', 'string', 'max:10'],
            'addresses.*.landmark' => ['nullable', 'string', 'max:255'],
            'addresses.*.status'   => ['required', 'in:0,1'],
            'addresses.*.type'     => ['required', 'string', 'in:home,work,other'],
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors()->all()], 422);
        }

        // Step 1: Create user without referral_code (we’ll add it after we get the ID)
        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'mobile'        => $request->mobile ?? '0',
            'address1'      => null,
            'address2'      => null,
            'pincode1'      => null,
            'pincode2'      => null,
            'landmark1'     => null,
            'landmark2'     => null,
            'referred_by'   => $request->referred_by ?? null,
            'coin'          => 0,
            'reward_points' => 0,
        ]);

        // Step 2: Add referral code
        $user->referral_code = 'MYMEREF' . $user->id;
        $user->save();

        // Step 3: Store addresses
        foreach ($request->addresses as $addressData) {
            $user->addresses()->create([
                'address'  => $addressData['address'],
                'pincode'  => $addressData['pincode'],
                'landmark' => $addressData['landmark'],
                'status'   => $addressData['status'],
                'type'     => $addressData['type'],
            ]);
        }

        $user->load('addresses');
        $user->makeHidden(['password', 'remember_token'])
            ->makeVisible('referred_by');

        return response([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }
}
