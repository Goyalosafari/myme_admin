<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConversionValue;
use Illuminate\Http\Request;

class ConversionValueController extends Controller
{
    public function index()
    {
        // dd(auth()->user());
        $conversionValues = ConversionValue::all();
        return view('admin.conversion_values.index', compact('conversionValues'));
    }

    public function create()
    {
        return view('admin.conversion_values.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'coin_conversion_rate' => 'required|numeric|min:0',
            'referrer_reward_points' => 'required|integer|min:0',
            'referee_reward_points' => 'required|integer|min:0',
            'minimum_applicable_amount' => 'required|numeric|min:0',
        ]);
        $data = $request->only(['coin_conversion_rate', 'referrer_reward_points', 'referee_reward_points', 'minimum_applicable_amount']);
        $data['status'] = 'inactive'; // Always set new row as inactive
        $conversion = ConversionValue::create($data);
        return redirect()->route('admin.conversion-values.index')->with('success', 'Conversion value added successfully.');
    }

    public function edit($id)
    {
        $conversionValue = ConversionValue::findOrFail($id);
        return view('admin.conversion_values.edit', compact('conversionValue'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'coin_conversion_rate' => 'required|numeric|min:0',
            'referrer_reward_points' => 'required|integer|min:0',
            'referee_reward_points' => 'required|integer|min:0',
            'minimum_applicable_amount' => 'required|numeric|min:0',
        ]);
        $conversionValue = ConversionValue::findOrFail($id);
        $conversionValue->update($request->only(['coin_conversion_rate', 'referrer_reward_points', 'referee_reward_points', 'minimum_applicable_amount', 'status']));
        return redirect()->route('admin.conversion-values.index')->with('success', 'Conversion value updated successfully.');
    }

    public function activate($id)
    {
        ConversionValue::where('id', '!=', $id)->update(['status' => 'inactive']);
        $conversionValue = ConversionValue::findOrFail($id);
        $conversionValue->status = 'active';
        $conversionValue->save();
        return redirect()->route('admin.conversion-values.index')->with('success', 'Conversion value activated.');
    }
}
