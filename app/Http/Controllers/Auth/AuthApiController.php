<?php

namespace App\Http\Controllers\Auth;

use App\Models\Wallet;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Always generate a new token, no matter how many times or where from
            $token = $user->createToken('api-token')->plainTextToken;

            $wallet = Wallet::where('user_id', $user->id)
                ->selectRaw('COALESCE((SUM(debit) - SUM(credit)), 0) as balance')
                ->first();

            return response([
                'token' => $token,
                'user' => $user,
                'wallet' => $wallet,
                'message' => 'Login Successful'
            ]);
        }

        return response(['message' => 'Invalid credentials'], 401);
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
