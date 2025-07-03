<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConversionValue;
use App\Models\CoinRewardConversion;
use Illuminate\Http\Request;

class ConversionApiController extends Controller
{
    public function activeCollections(Request $request)
    {
        $conversionValues = ConversionValue::where('status', 'active')->get();
        $coinRewardConversions = CoinRewardConversion::where('status', 'active')->get();
        return response([
            'conversion_values' => $conversionValues,
            'coin_reward_conversions' => $coinRewardConversions
        ], 200);
    }
}
