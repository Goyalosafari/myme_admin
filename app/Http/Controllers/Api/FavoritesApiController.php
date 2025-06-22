<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
Use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritesApiController extends Controller
{
    public function addTofavorites(Request $request)
    {
        $favorite = Favorite::create([
            'food_id' => $request->food_id,
            'user_id' => Auth::id()
        ]);
        return response()->json($favorite, 200);
    }

    public function favoritesList()
    {
        $favorites = Favorite::select('id','food_id')
        ->where('user_id', Auth::id())->get();
        return response()->json($favorites, 200);
    }

    public function remove($favorite_id)
    {
        Favorite::find($favorite_id)->delete();
        return response()->json('success');
    }
}

//['coupon'=>$coupen]