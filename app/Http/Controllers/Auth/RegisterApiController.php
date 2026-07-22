<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
<<<<<<< HEAD
use Illuminate\Http\Request;
=======
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
>>>>>>> main
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
<<<<<<< HEAD
=======
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
>>>>>>> main

class RegisterApiController extends Controller
{
    public function store(Request $request)
    {
<<<<<<< HEAD
        $validator = Validator::make($request->all(), [
            'name'                    => ['required', 'string', 'max:255'],
            'email'                   => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'mobile'                  => ['nullable', 'string', 'max:255', 'unique:users'],
            'password'                => ['nullable', 'string', 'min:8'],
            'addresses'               => ['nullable', 'array'],
            'addresses.*.address'     => ['required_with:addresses', 'string', 'max:255'],
            'addresses.*.pincode'     => ['nullable', 'string', 'max:10'],
            'addresses.*.landmark'    => ['nullable', 'string', 'max:255'],
            'addresses.*.name'        => ['required_with:addresses', 'string', 'max:255'],
            'addresses.*.instruction' => ['nullable', 'string', 'max:255'],
            'addresses.*.phone'       => ['nullable', 'string', 'max:15'],
            'addresses.*.status'      => ['required_with:addresses', 'in:0,1'],
            'addresses.*.type'        => ['required_with:addresses', 'string', 'in:home,work,other'],
            'referral_code'           => ['nullable', 'string', 'max:255'],
        ]);

        // Custom validation: at least one of email or mobile is required
        $validator->after(function ($validator) use ($request) {
            if (empty($request->email) && empty($request->mobile)) {
                $validator->errors()->add('email', 'Either email or mobile is required.');
                $validator->errors()->add('mobile', 'Either email or mobile is required.');
            }
        });

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->messages() as $field => $messages) {
                $errors[$field] = $messages;
            }
            return response(['errors' => $errors], 422);
        }

        // Referral logic
        $referrer = null;
        $referrerReward = 0;
        $refereeReward = 0;
        $referredById = null;

        // Get active conversion value for referral
        $conversion = \App\Models\ConversionValue::where('status', 'active')->latest()->first();
        if ($conversion) {
            $referrerReward = $conversion->referrer_reward_points;
            $refereeReward = $conversion->referee_reward_points;
        }

        info($refereeReward);
        // If referral_code is present, try to find the user by referral_code
        if ($request->filled('referral_code')) {
            $referrer = \App\Models\User::where('referral_code', $request->referral_code)->first();
            if ($referrer) {
                $referredById = $referrer->id;
                // dd("Referrer found: " . $referrer->name);
            }
        }

        // Create the user with address fields set to null and referred_by
        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'mobile'      => $request->mobile,
            'address1'    => null,
            'address2'    => null,
            'pincode1'    => null,
            'pincode2'    => null,
            'landmark1'   => null,
            'landmark2'   => null,
            'referred_by' => $referredById,
            'reward_points' => $refereeReward, // Give referee reward points
        ]);

        // Set the new user's referral_code
        $user->referral_code = 'MYMEREF' . $user->id;
        $user->save();

        // If referrer exists, update their reward points
        if ($referrer) {
            $referrer->increment('reward_points', $referrerReward);
        }

        // Create addresses in the addresses table
        if (is_array($request->addresses)) {
            foreach ($request->addresses as $addressData) {
                $user->addresses()->create([
                    'address'     => $addressData['address'],
                    'pincode'     => $addressData['pincode'],
                    'landmark'    => $addressData['landmark'],
                    'name'        => $addressData['name'],
                    'instruction' => $addressData['instruction'],
                    'phone'       => $addressData['phone'],
                    'status'      => $addressData['status'],
                    'type'        => $addressData['type'],
                ]);
            }
        }

        // Load the user with addresses for the response
        $user->load('addresses');

        return response(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string',
        ]);

        // ✅ Check if user with this email already exists
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'Email already registered. Please login instead.',
            ], 409); // HTTP 409 Conflict
        }

        // ✅ Render OTP email using Blade template
        $html = view('email.otp_mail', ['otp' => $request->otp])->render();

        // ✅ Send mail
        Mail::send([], [], function ($message) use ($request, $html) {
            $message->to($request->email)
                ->subject('Your OTP for Myme App')
                ->html($html);
        });

        return response()->json([
            'message' => 'OTP sent successfully to ' . $request->email,
        ], 200);
    }
    public function sendLoginOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string',
        ]);

        // ✅ Check if user with this email exists
        if (! User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'Email not found. Please register first.',
            ], 404); // HTTP 404 Not Found
        }

        // ✅ Render OTP email using Blade template
        $html = view('email.otp_mail', ['otp' => $request->otp])->render();

        // ✅ Send email
        Mail::send([], [], function ($message) use ($request, $html) {
            $message->to($request->email)
                ->subject('Your Login OTP for Myme App')
                ->html($html);
        });

        return response()->json([
            'message' => 'OTP sent successfully to ' . $request->email,
        ], 200);
    }

    public function checkUserByMobile(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
        ]);

        $userExists = User::where('mobile', $request->mobile)->exists();

        if ($userExists) {
            return response()->json([
                'message' => 'User with this mobile number exists.',
            ], 200); // OK
        }

        return response()->json([
            'message' => 'User not found with this mobile number.',
        ], 404); // Not Found
    }

=======
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
>>>>>>> main
}
