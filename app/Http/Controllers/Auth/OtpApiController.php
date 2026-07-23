<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class OtpApiController extends Controller
{
    protected $sms;

    public function __construct(SmsService $sms)
    {
        $this->sms = $sms;
    }

    // Called by Flutter register screen: sends email OTP for new user verification
    public function sendOtp(Request $request)
    {
        // Email-based OTP (registration flow)
        if ($request->filled('email')) {
            $request->validate(['email' => 'required|email']);

            if (User::where('email', $request->email)->exists()) {
                return response(['message' => 'Email already registered. Please login.'], 409);
            }

            $otp = (string) rand(1000, 9999);
            Cache::put('email_otp_' . $request->email, $otp, 1200); // 20 minutes

            try {
                Mail::raw(
                    "Your MYME registration OTP is: {$otp}\n\nValid for 20 minutes.",
                    function ($msg) use ($request) {
                        $msg->to($request->email)
                            ->subject('MYME – Email Verification OTP');
                    }
                );
            } catch (\Exception $e) {
                // Mail not configured — OTP still cached, dev can retrieve from cache
            }

            return response(['message' => 'OTP sent to ' . $request->email], 200);
        }

        // Mobile/SMS-based OTP (phone verification flow)
        $request->validate([
            'mobile' => 'required|digits:10',
        ]);

        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            return response(['message' => 'Mobile number not registered'], 404);
        }

        $otp     = rand(100000, 999999);
        $expires = Carbon::now()->addMinutes(10);

        $user->update([
            'otp'            => (string) $otp,
            'otp_expires_at' => $expires,
        ]);

        $message    = "Your MYME verification OTP is {$otp}. Valid for 10 minutes.";
        $templateId = '1707173462931959706';

        try {
            $this->sms->sendSms($request->mobile, $message, $templateId);
        } catch (\Exception $e) {
            \Log::error('Login OTP SMS failed for ' . $request->mobile . ': ' . $e->getMessage());
        }

        return response(['message' => 'OTP sent successfully', 'otp' => (string) $otp], 200);
    }

    // Shared by /send-login-otp, /forgot-password-otp, /user/send-email-otp.
    // The app generates the OTP client-side and asks us to relay it by email —
    // verification happens client-side too (no server-side OTP is stored here).
    private function relayEmailOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required',
        ]);

        if (!User::where('email', $request->email)->exists()) {
            return response(['message' => 'Email not found. Please register first'], 404);
        }

        try {
            Mail::raw(
                "Your MYME OTP is: {$request->otp}\n\nValid for 10 minutes.",
                function ($msg) use ($request) {
                    $msg->to($request->email)->subject('MYME – Verification OTP');
                }
            );
        } catch (\Exception $e) {
            \Log::error('Email OTP relay failed for ' . $request->email . ': ' . $e->getMessage());
        }

        return response(['message' => 'OTP sent to ' . $request->email], 200);
    }

    public function sendLoginOtp(Request $request)
    {
        return $this->relayEmailOtp($request);
    }

    public function forgotPasswordOtp(Request $request)
    {
        return $this->relayEmailOtp($request);
    }

    public function sendEmailOtp(Request $request)
    {
        return $this->relayEmailOtp($request);
    }

    // Registration / profile-mobile-update OTP — no existing user required
    public function sendRegisterOtp(Request $request)
    {
        $request->validate(['mobile' => 'required|digits:10']);

        $otp        = (string) rand(100000, 999999);
        $message    = "Your MYME OTP is {$otp}. Valid for 10 minutes.";
        $templateId = '1707173462931959706';

        Cache::put('reg_otp_' . $request->mobile, $otp, 600);

        try {
            $this->sms->sendSms($request->mobile, $message, $templateId);
        } catch (\Exception $e) {
            \Log::error('Registration OTP SMS failed for ' . $request->mobile . ': ' . $e->getMessage());
        }

        return response(['message' => 'OTP sent successfully', 'otp' => $otp], 200);
    }

    public function verifyRegisterOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
            'otp'    => 'required|digits:6',
        ]);

        $cached = Cache::get('reg_otp_' . $request->mobile);

        if (!$cached || $cached !== $request->otp) {
            return response(['message' => 'Invalid OTP'], 422);
        }

        Cache::forget('reg_otp_' . $request->mobile);
        return response(['message' => 'OTP verified successfully'], 200);
    }

    // Called for mobile OTP verification
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
            'otp'    => 'required|digits:6',
        ]);

        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            return response(['message' => 'Mobile number not found'], 404);
        }

        if ($user->otp !== $request->otp) {
            return response(['message' => 'Invalid OTP'], 422);
        }

        if (Carbon::now()->gt($user->otp_expires_at)) {
            return response(['message' => 'OTP expired'], 422);
        }

        $user->update([
            'otp'             => null,
            'otp_expires_at'  => null,
            'mobile_verified' => true,
        ]);

        // Auto-login after OTP verification — return Sanctum token
        $token  = $user->createToken('api-token')->plainTextToken;
        $wallet = Wallet::where('user_id', $user->id)
            ->selectRaw('COALESCE((SUM(debit) - SUM(credit)), 0) as balance')
            ->first();

        return response([
            'message'         => 'Mobile verified successfully',
            'mobile_verified' => true,
            'token'           => $token,
            'user'            => $user,
            'wallet'          => $wallet,
        ]);
    }
}
