<?php

namespace App\Http\Controllers\AdminAuth;

use App\Http\Controllers\Controller;
use App\Mail\AdminOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (session('admin_logged_in')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $adminEmail    = env('ADMIN_EMAIL');
        $adminPassword = env('ADMIN_PASSWORD');

        $emailMatch    = $request->email === $adminEmail;
        $passwordMatch = Hash::check($request->password, $adminPassword)
                         || $request->password === $adminPassword;

        if (!$emailMatch || !$passwordMatch) {
            return back()
                ->withInput(['email' => $request->email])
                ->withErrors(['credentials' => 'Invalid email or password. Please try again.']);
        }

        $otp = random_int(100000, 999999);

        session([
            'admin_otp'            => $otp,
            'admin_otp_email'      => $request->email,
            'admin_otp_expires_at' => now()->addMinutes(5)->timestamp,
        ]);

        try {
            Mail::to($request->email)->send(new AdminOtpMail($otp));
            return redirect()->route('admin.otp');
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return redirect()->route('admin.otp');
            }
            session()->forget(['admin_otp', 'admin_otp_email', 'admin_otp_expires_at']);
            return back()
                ->withInput(['email' => $request->email])
                ->with('mail_error', 'OTP email could not be sent. Please fix the mail configuration.');
        }
    }

    public function showOtpForm()
    {
        if (!session('admin_otp')) {
            return redirect()->route('admin.login');
        }
        return view('auth.otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        if (!session('admin_otp') || !session('admin_otp_expires_at')) {
            return redirect()->route('admin.login')->withErrors(['email' => 'Session expired. Please try again.']);
        }

        if (now()->timestamp > session('admin_otp_expires_at')) {
            session()->forget(['admin_otp', 'admin_otp_email', 'admin_otp_expires_at']);
            return redirect()->route('admin.login')->withErrors(['email' => 'OTP expired. Please request a new one.']);
        }

        if ((string) $request->otp !== (string) session('admin_otp')) {
            return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
        }

        session()->forget(['admin_otp', 'admin_otp_email', 'admin_otp_expires_at']);
        session(['admin_logged_in' => true]);

        return redirect()->route('dashboard');
    }
}
