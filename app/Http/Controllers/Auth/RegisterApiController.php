<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RegisterApiController extends Controller
{
    private function generateReferralCode(): string
    {
        do {
            $code = 'MYME' . strtoupper(Str::random(6));
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }

    public function store(Request $request)
    {
        // Mobile-only registration (no email/password)
        if ($request->filled('mobile') && !$request->filled('email')) {
            $validator = Validator::make($request->all(), [
                'name'   => ['required', 'string', 'max:255'],
                'mobile' => ['required', 'digits:10', 'unique:users,mobile'],
            ]);

            if ($validator->fails()) {
                return response(['error' => $validator->errors()->all()], 422);
            }

            $user = User::create([
                'name'          => $request->name,
                'mobile'        => $request->mobile,
                'email'         => null,
                'password'      => Hash::make(Str::random(32)),
                'referral_code' => $this->generateReferralCode(),
                'referred_by'   => $request->referral_code ?? null,
            ]);
        } else {
            // Email + OTP + password registration — mobile is required too, so an
            // account started via mobile-only signup can be matched and completed
            // here rather than creating a duplicate user for the same phone.
            $validator = Validator::make($request->all(), [
                'name'     => ['required', 'string', 'max:255'],
                'email'    => ['required', 'string', 'email', 'max:255'],
                'mobile'   => ['required', 'digits:10'],
                'password' => ['required', 'string', 'min:8']               
            ]);

            if ($validator->fails()) {
                return response(['error' => $validator->errors()->all()], 422);
            }

            // OTP is generated and verified client-side by the app before it ever
            // calls this endpoint — nothing to check server-side, just clear the
            // cache entry /send-otp left behind.
            Cache::forget('email_otp_' . $request->email);

            $existingByPhone = User::where('mobile', $request->mobile)->first();
            $existingByEmail = User::where('email', $request->email)->first();

            if ($existingByEmail && (!$existingByPhone || $existingByEmail->id !== $existingByPhone->id)) {
                return response(['error' => ['Email already registered. Please login.']], 409);
            }

            if ($existingByPhone && $existingByPhone->isDeactivated()) {
                return response(['error' => ['This account has been deactivated. Please contact support.']], 403);
            }

            $attributes = [
                'name'      => $request->name,
                'email'     => $request->email,
                'mobile'    => $request->mobile,
                'password'  => Hash::make($request->password),
                'address1'  => $request->address1 ?? null,
                'address2'  => $request->address2 ?? null,
                'pincode1'  => $request->pincode1 ?? null,
                'pincode2'  => $request->pincode2 ?? null,
                'landmark1' => $request->landmark1 ?? null,
                'landmark2' => $request->landmark2 ?? null,
            ];

            if ($existingByPhone) {
                // This phone number already has an account (e.g. an earlier
                // mobile-only signup) — attach the email/password to that same
                // record instead of creating a duplicate user for one person.
                $existingByPhone->update($attributes);
                $user = $existingByPhone;
            } else {
                $user = User::create($attributes + [
                    'referral_code' => $this->generateReferralCode(),
                    'referred_by'   => $request->referral_code ?? null,
                ]);
            }
        }

        // Auto-login — return token so Flutter doesn't need a separate login step
        $token  = $user->createToken('api-token')->plainTextToken;
        $wallet = Wallet::where('user_id', $user->id)
            ->selectRaw('COALESCE((SUM(debit) - SUM(credit)), 0) as balance')
            ->first();

        return response([
            'token'   => $token,
            'user'    => new UserResource($user),
            'wallet'  => $wallet,
            'message' => 'User registered successfully',
        ], 201);
    }
}
