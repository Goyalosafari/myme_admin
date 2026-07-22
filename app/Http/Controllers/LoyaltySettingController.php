<?php

namespace App\Http\Controllers;

use App\Models\LoyaltySetting;
use Illuminate\Http\Request;

class LoyaltySettingController extends Controller
{
    public function index()
    {
        $settings = LoyaltySetting::current();
        return view('loyalty_settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'points_per_amount'     => 'required|integer|min:1',
            'min_points_to_convert' => 'required|integer|min:0',
        ]);

        $settings = LoyaltySetting::current();
        $settings->update($request->only('points_per_amount', 'min_points_to_convert'));

        return back()->with('success', 'Loyalty point settings updated.');
    }
}
