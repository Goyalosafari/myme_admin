<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodApiController extends Controller
{
    public function index($type)
    {
        if($type == 'food'){
        $foods = Food::with('category')->where('type', 'food')->get();
        }else if($type == 'grocery'){
            $foods = Food::with('category')->where('type', 'grocery')->get(); 
        }
        return response()->json($foods, 200);
    }

    public function foodByCategory($category_id)
    {
        $foods = Food::where('category_id', $category_id)->get();
        return response()->json($foods, 200);
    }

    public function foodDetails(Food $food)
    {
        $details = $food->load('category');
        return response()->json($details, 200);
    }

    public function foodInOffer(Food $food)
    {
        $foods = $food->where('offer','yes')->get();
        return response()->json(['data'=>$foods]);
    }
}
