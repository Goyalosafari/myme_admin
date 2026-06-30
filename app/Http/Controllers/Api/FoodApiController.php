<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FoodResource;
use App\Models\Category;
use App\Models\Food;
use App\Traits\ApiResponse;

class FoodApiController extends Controller
{
    use ApiResponse;

    public function index($type)
    {
        if (!in_array($type, ['food', 'grocery'])) {
            return $this->error('Invalid food type', 422);
        }

        return $this->success(
            FoodResource::collection(Food::with('category')->where('type', $type)->get())
        );
    }

    public function foodByCategory($category_id)
    {
        return $this->success(
            FoodResource::collection(Food::where('category_id', $category_id)->get())
        );
    }

    public function foodByCategoryId($category_id)
    {
        $category = Category::findOrFail($category_id);

        return $this->success(FoodResource::collection($category->foods));
    }

    public function foodDetails(Food $food)
    {
        return $this->success(new FoodResource($food->load('category')));
    }

    public function Homefood1()
    {
        return $this->success(
            FoodResource::collection(
                Food::with('category')->where('type', 'food')->where('offer', 'yes')->get()
            )
        );
    }

    public function Homefood2()
    {
        return $this->success(
            FoodResource::collection(
                Food::with('category')->where('type', 'food')->where('ref', 'yes')->get()
            )
        );
    }

    public function Homefood3()
    {
        return $this->success(
            FoodResource::collection(
                Food::with('category')->where('type', 'grocery')->where('offer', 'yes')->get()
            )
        );
    }

    public function Homefood4()
    {
        return $this->success(
            FoodResource::collection(
                Food::with('category')->where('type', 'grocery')->where('ref', 'yes')->get()
            )
        );
    }
}
