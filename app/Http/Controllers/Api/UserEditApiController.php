<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserEditApiController extends Controller
{
    // 1. Edit user name
    public function updateName(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $user = Auth::user();
        $user->name = $request->name;
        $user->save();
        return response(['message' => 'Name updated successfully', 'user' => $user], 200);
    }

    // 2. Edit user email (requires OTP verification)
    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
        ]);
        // Here you should verify the OTP (implement OTP logic as needed)
        // For now, assume OTP is valid
        $user = Auth::user();
        $user->email = $request->email;
        $user->save();
        return response(['message' => 'Email updated successfully', 'user' => $user], 200);
    }

    // 3. Edit user password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();
        return response(['message' => 'Password updated successfully'], 200);
    }

    // 4. Send OTP to new email before editing
    public function sendEmailOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);
        // Use existing Blade template for OTP with a custom message
        $html = view('email.otp_mail', [
            'otp' => $request->otp,
            'message' => 'You requested to change your email on Myme App. Please use the following OTP to proceed:'
        ])->render();
        Mail::send([], [], function ($message) use ($request, $html) {
            $message->to($request->email)
                ->subject('Your OTP for Myme App')
                ->html($html);
        });
        return response(['message' => 'OTP sent successfully to ' . $request->email], 200);
    }

    // Update user mobile number by user_id
    public function updateMobile(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'mobile' => 'required|string|max:255|unique:users,mobile',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->messages() as $field => $messages) {
                $errors[$field] = $messages;
            }
            return response(['errors' => $errors], 422);
        }
        $user = \App\Models\User::findOrFail($request->user_id);
        $user->mobile = $request->mobile;
        $user->save();
        return response(['message' => 'Mobile number updated successfully', 'user' => $user], 200);
    }
}
