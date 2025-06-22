<?php

namespace App\Http\Controllers\Api;
use App\Models\Rating;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingsApiController extends Controller
{
    public function rate(Request $request)
    {
        $rating = Rating::create([
            'food_id' => $request->food_id,
            'user_id' => $request->user_id,
            'ratings' => $request->ratings,
            'feedback' => $request->feedback
        ]);
        return response()->json($rating, 200);
    } 
}
