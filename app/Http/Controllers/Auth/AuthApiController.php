<?php

namespace App\Http\Controllers\Auth;

use App\Models\Wallet;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'mobile' => 'nullable|string',
            'password' => 'nullable|string',
        ]);

        // Find user by email or mobile
        $user = null;
        if ($request->filled('email')) {
            $user = \App\Models\User::where('email', $request->email)->first();
        } elseif ($request->filled('mobile')) {
            $user = \App\Models\User::where('mobile', $request->mobile)->first();
        }

        if (! $user) {
            return response(['message' => 'User not found'], 404);
        }

        // If password is present, check it
        if ($request->filled('password')) {
            if (! Hash::check($request->password, $user->password)) {
                return response(['message' => 'Invalid credentials'], 401);
            }
        }
        // If password is not present, allow login (OTP assumed checked on frontend)

        // Generate token
        $token = $user->createToken('api-token')->plainTextToken;
        $wallet = \App\Models\Wallet::where('user_id', $user->id)
            ->selectRaw('COALESCE((SUM(debit) - SUM(credit)), 0) as balance')
            ->first();

        return response([
            'token' => $token,
            'user' => $user,
            'wallet' => $wallet,
            'message' => 'Login Successful'
        ]);
    }


    public function forgot(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response(['message' => __($status)], 200)
            : response(['message' => __($status)], 400);
    }

    public function forgotPasswordOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);
        // Use existing Blade template for OTP with a custom message
        $html = view('email.otp_mail', [
            'otp' => $request->otp,
            'message' => 'You requested to reset your password on Myme App. Please use the following OTP to proceed:'
        ])->render();
        Mail::send([], [], function ($message) use ($request, $html) {
            $message->to($request->email)
                ->subject('Your OTP for Password Reset - Myme App')
                ->html($html);
        });
        return response(['message' => 'OTP sent successfully to ' . $request->email], 200);
    }
}
