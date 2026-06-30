<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RatingResource;
use App\Models\Rating;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class RatingsApiController extends Controller
{
    use ApiResponse;

    public function rate(Request $request)
    {
        $request->validate([
            'food_id'  => 'required|exists:foods,id',
            'user_id'  => 'required',
            'ratings'  => 'required|numeric|min:1|max:5',
            'feedback' => 'nullable|string|max:500',
        ]);

        $rating = Rating::create([
            'food_id'  => $request->food_id,
            'user_id'  => $request->user_id,
            'ratings'  => $request->ratings,
            'feedback' => $request->feedback,
        ]);

        return $this->success(new RatingResource($rating), 'Rating submitted', 201);
    }
}
