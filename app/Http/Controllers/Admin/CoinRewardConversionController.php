<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoinRewardConversion;
use Illuminate\Http\Request;

class CoinRewardConversionController extends Controller
{
    public function index()
    {
        $coinRewardConversions = CoinRewardConversion::all();
        return view('admin.coin_reward_conversions.index', compact('coinRewardConversions'));
    }

    public function create()
    {
        return view('admin.coin_reward_conversions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'coin_to_reward_rate' => 'required|numeric|min:0',
        ]);
        $data = $request->only(['coin_to_reward_rate']);
        $data['status'] = 'inactive';
        CoinRewardConversion::create($data);
        return redirect()->route('admin.coin-reward-conversions.index')->with('success', 'Coin to reward conversion value added successfully.');
    }

    public function edit($id)
    {
        $coinRewardConversion = CoinRewardConversion::findOrFail($id);
        return view('admin.coin_reward_conversions.edit', compact('coinRewardConversion'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'coin_to_reward_rate' => 'required|numeric|min:0',
        ]);
        $coinRewardConversion = CoinRewardConversion::findOrFail($id);
        $coinRewardConversion->update($request->only(['coin_to_reward_rate', 'status']));
        return redirect()->route('admin.coin-reward-conversions.index')->with('success', 'Coin to reward conversion value updated successfully.');
    }

    public function activate($id)
    {
        CoinRewardConversion::where('id', '!=', $id)->update(['status' => 'inactive']);
        $coinRewardConversion = CoinRewardConversion::findOrFail($id);
        $coinRewardConversion->status = 'active';
        $coinRewardConversion->save();
        return redirect()->route('admin.coin-reward-conversions.index')->with('success', 'Coin to reward conversion value activated.');
    }
}
