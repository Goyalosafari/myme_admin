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
                'name'     => $request->name,
                'mobile'   => $request->mobile,
                'email'    => 'mobile_' . $request->mobile . '@myme.local',
                'password' => Hash::make(Str::random(32)),
            ]);
        } else {
            // Email + OTP + password registration
            $validator = Validator::make($request->all(), [
                'name'     => ['required', 'string', 'max:255'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],
                'otp'      => ['required', 'digits:4'],
            ]);

            if ($validator->fails()) {
                return response(['error' => $validator->errors()->all()], 422);
            }

            // Verify email OTP set by /send-otp
            $cached = Cache::get('email_otp_' . $request->email);
            if (!$cached || $cached !== $request->otp) {
                return response(['error' => ['Invalid or expired OTP']], 422);
            }
            Cache::forget('email_otp_' . $request->email);

            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'mobile'    => $request->mobile ?? null,
                'password'  => Hash::make($request->password),
                'address1'  => $request->address1 ?? null,
                'address2'  => $request->address2 ?? null,
                'pincode1'  => $request->pincode1 ?? null,
                'pincode2'  => $request->pincode2 ?? null,
                'landmark1' => $request->landmark1 ?? null,
                'landmark2' => $request->landmark2 ?? null,
            ]);
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
