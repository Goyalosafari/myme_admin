<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use App\Traits\ApiResponse;

class RecipeApiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        return $this->success(RecipeResource::collection(Recipe::with('food')->get()));
    }

    public function recipeByFood($food_id)
    {
        return $this->success(
            new RecipeResource(Recipe::with('food')->where('food_id', $food_id)->firstOrFail())
        );
    }

    public function recipeDetails(Recipe $recipe)
    {
        return $this->success(new RecipeResource($recipe->load('food')));
    }
}
