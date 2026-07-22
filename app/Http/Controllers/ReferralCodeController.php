<?php

namespace App\Http\Controllers;

use App\Models\ReferralSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReferralCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::select('id', 'name', 'mobile', 'email', 'referral_code', 'referred_by', 'created_at')
            ->latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('mobile', 'like', "%{$q}%")
                    ->orWhere('referral_code', 'like', "%{$q}%")
                    ->orWhere('referred_by', 'like', "%{$q}%");
            });
        }

        $users = $query->paginate(20)->withQueryString();
        $settings = ReferralSetting::current();

        return view('referral_codes', compact('users', 'settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'reward_amount' => 'required|numeric|min:0',
        ]);

        $settings = ReferralSetting::current();
        $settings->update($request->only('reward_amount'));

        return back()->with('success', 'Referral reward amount updated.');
    }

    public function generate($id)
    {
        $user = User::findOrFail($id);

        if (!$user->referral_code) {
            do {
                $code = 'MYME' . strtoupper(Str::random(6));
            } while (User::where('referral_code', $code)->exists());

            $user->referral_code = $code;
            $user->save();
        }

        return back()->with('success', "Referral code generated for {$user->name}.");
    }
}
