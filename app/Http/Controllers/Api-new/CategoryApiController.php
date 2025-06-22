<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    public function index($type)
    {
        if($type == 'food'){
            $category = Category::where('type', 'food')->get();
        }else if($type == 'grocery'){
            $category = Category::where('type', 'grocery')->get();
        }
        return response()->json( $category,200 );
    }
}
