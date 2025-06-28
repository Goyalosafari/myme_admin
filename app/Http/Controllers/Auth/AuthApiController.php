<?php

namespace App\Http\Controllers\Auth;

use App\Models\Wallet;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
}
