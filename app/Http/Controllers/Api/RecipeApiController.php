<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Recipe;

use Illuminate\Http\Request;

class RecipeApiController extends Controller
{
    public function index()
    {
        $recipies = Recipe::with('food')->get();
        return response()->json(['recipies'=>$recipies], 200);
    }
    public function recipeByFood($food_id)
    {
        $recipies = Recipe::where('food_id',$food_id)->first();
        return response()->json(['recipies'=>$recipies], 200);
    }
    public function recipeDetails(Recipe $recipe)
    {
        $details = $recipe->load('food');
        return response()->json(['details'=>$details], 200);
    }
}

//['recipies'=>$recipies]
