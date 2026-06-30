<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\ApiResponse;

class CategoryApiController extends Controller
{
    use ApiResponse;

    public function index($type)
    {
        if (!in_array($type, ['food', 'grocery'])) {
            return $this->error('Invalid category type', 422);
        }

        return $this->success(
            CategoryResource::collection(Category::where('type', $type)->get())
        );
    }
}
