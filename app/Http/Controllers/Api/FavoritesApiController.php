<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavoriteResource;
use App\Models\Favorite;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritesApiController extends Controller
{
    use ApiResponse;

    public function addTofavorites(Request $request)
    {
        $favorite = Favorite::create([
            'food_id' => $request->food_id,
            'user_id' => Auth::id(),
        ]);

        return $this->success(new FavoriteResource($favorite), 'Added to favorites', 201);
    }

    public function favoritesList()
    {
        return $this->success(
            FavoriteResource::collection(
                Favorite::select('id', 'food_id', 'user_id')->where('user_id', Auth::id())->get()
            )
        );
    }

    public function remove($favorite_id)
    {
        $favorite = Favorite::find($favorite_id);

        if (!$favorite) {
            return $this->error('Favorite not found', 404);
        }

        $favorite->delete();

        return $this->success(null, 'Removed from favorites');
    }
}
