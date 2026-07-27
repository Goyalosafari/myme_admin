<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class OtpApiController extends Controller
{
    // MSG91's dedicated OTP product (/api/v5/otp) turned out to be tied to a
    // DIFFERENT template store than the account's confirmed DLT-verified one
    // ("Myme OTP", under the SMS product) — sends got stuck permanently
    // "Pending" and never delivered. So we generate/store/verify the OTP
    // ourselves (like the original design) and use MSG91's plain "Send SMS"
    // Flow API purely as the SMS transport, pointed at the verified template.
    private function msg91SendSms(string $mobile, string $name, string $otp): bool
    {
        $authKey    = config('services.msg91.auth_key');
        $templateId = config('services.msg91.template_id');

        if (empty($authKey) || empty($templateId)) {
            \Log::error('MSG91 Flow SMS skipped — auth key or template id not configured.');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'accept'       => 'application/json',
                'authkey'      => $authKey,
                'content-type' => 'application/json',
            ])->post('https://control.msg91.com/api/v5/flow', [
                'template_id'      => $templateId,
                'short_url'        => '1',
                'short_url_expiry' => '3600',
                'realTimeResponse' => '1',
                'recipients'       => [[
                    'mobiles' => '91' . $mobile,
                    'VAR1'    => $name,
                    'VAR2'    => $otp,
                ]],
            ]);
        } catch (\Exception $e) {
            \Log::error('MSG91 Flow SMS request failed for ' . $mobile . ': ' . $e->getMessage());
            return false;
        }

        $result = $response->json();
        $ok     = $response->successful() && ($result['type'] ?? null) === 'success';

        if (!$ok) {
            \Log::error('MSG91 Flow SMS send failed for ' . $mobile . ': ' . $response->body());
        }

        return $ok;
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

        // Mobile/SMS-based OTP (phone verification flow) — via MSG91 Flow API
        $request->validate([
            'mobile' => 'required|digits:10',
        ]);

        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            return response(['message' => 'Mobile number not registered'], 404);
        }

        $otp = (string) rand(100000, 999999);

        if (!$this->msg91SendSms($request->mobile, $user->name ?? 'there', $otp)) {
            return response(['message' => 'Failed to send OTP. Please try again.'], 500);
        }

        $user->update([
            'otp'            => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        return response(['message' => 'OTP sent successfully'], 200);
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

    // Registration / profile-mobile-update OTP — no existing user required — via MSG91 Flow API
    public function sendRegisterOtp(Request $request)
    {
        $request->validate(['mobile' => 'required|digits:10']);

        $otp = (string) rand(100000, 999999);

        if (!$this->msg91SendSms($request->mobile, 'there', $otp)) {
            return response(['message' => 'Failed to send OTP. Please try again.'], 500);
        }

        Cache::put('reg_otp_' . $request->mobile, $otp, 600);

        return response(['message' => 'OTP sent successfully'], 200);
    }

    public function verifyRegisterOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
            'otp'    => 'required',
        ]);

        $cached = Cache::get('reg_otp_' . $request->mobile);

        if (!$cached || $cached !== $request->otp) {
            return response(['message' => 'Invalid OTP'], 422);
        }

        Cache::forget('reg_otp_' . $request->mobile);
        return response(['message' => 'OTP verified successfully'], 200);
    }

    // Called for mobile OTP verification. If the request carries an MSG91 OTP Widget
    // access-token, that takes priority over the legacy stored-OTP check below —
    // the widget already sent + verified the code on MSG91's side.
    public function verifyOtp(Request $request)
    {
        if ($request->filled('access-token') || $request->filled('access_token')) {
            return $this->verifyMobileWidgetOtp($request);
        }

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

        return $this->issueMobileLoginResponse($user, 'Mobile verified successfully');
    }

    // MSG91 OTP Widget flow: the widget sends the SMS and verifies the code
    // client-side, then hands the app a JWT access-token. We confirm that token
    // is genuine server-side before trusting it and logging the user in.
    public function verifyMobileWidgetOtp(Request $request)
    {
        $request->validate([
            'access-token' => 'required_without:access_token|string',
            'access_token'  => 'required_without:access-token|string',
        ], [
            'access-token.required_without' => 'The access-token field is required.',
            'access_token.required_without'  => 'The access-token field is required.',
        ]);

        $accessToken = $request->input('access-token', $request->input('access_token'));
        $authKey     = config('services.msg91.auth_key');

        if (empty($authKey)) {
            \Log::error('MSG91 verifyAccessToken called but MSG91_AUTH_KEY is not configured.');
            return response(['message' => 'OTP verification is not configured'], 500);
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ])->post('https://control.msg91.com/api/v5/widget/verifyAccessToken', [
            'authkey'      => $authKey,
            'access-token' => $accessToken,
        ]);

        $result = $response->json();

        if (!$response->successful() || ($result['type'] ?? null) !== 'success') {
            \Log::error('MSG91 verifyAccessToken rejected: ' . $response->body());
            return response(['message' => 'Invalid or expired OTP session'], 422);
        }

        // On success MSG91 returns the verified mobile number (with country code) in `message`.
        $verifiedMobile = preg_replace('/^91/', '', (string) ($result['message'] ?? ''));
        $mobile         = $verifiedMobile ?: $request->mobile;

        $user = User::where('mobile', $mobile)->first();
        if (!$user) {
            return response(['message' => 'Mobile number not registered', 'mobile' => $mobile], 404);
        }

        $user->update(['mobile_verified' => true]);

        return $this->issueMobileLoginResponse($user, 'Mobile verified successfully');
    }

    private function issueMobileLoginResponse(User $user, string $message)
    {
        $token  = $user->createToken('api-token')->plainTextToken;
        $wallet = Wallet::where('user_id', $user->id)
            ->selectRaw('COALESCE((SUM(debit) - SUM(credit)), 0) as balance')
            ->first();

        return response([
            'message'         => $message,
            'mobile_verified' => true,
            'token'           => $token,
            'user'            => $user,
            'wallet'          => $wallet,
        ]);
    }
}
