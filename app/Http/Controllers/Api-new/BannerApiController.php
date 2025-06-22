<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;

use Illuminate\Http\Request;

class BannerApiController extends Controller
{
    public function index()
    {
        $banner = Banner::all();
        return response()->json( ['banner'=>$banner],200 );
    }
    
    public function Banner()
    {
        $banner = Banner::where('display_location','home')->orderBy('id', 'desc')->take(6)->get();
        return response()->json( ['banner'=>$banner],200 );
        
    }
    
        public function CategoryOne()
    {
        $banner = Banner::where('display_location','page_1')->orderBy('id', 'desc')->get();
        return response()->json( ['banner'=>$banner],200 );
        
    }
    
        public function CategoryTwo()
    {
        $banner = Banner::where('display_location','page_2')->orderBy('id', 'desc')->get();
        return response()->json( ['banner'=>$banner],200 );
    }
    
 
    
            public function AdOne()
    {
        $banner = Banner::where('display_location','ad_1')->orderBy('id', 'desc')->take(1)->get();
        return response()->json( ['banner'=>$banner],200 );
    }
    
                public function AdTwo()
    {
        $banner = Banner::where('display_location','ad_2')->orderBy('id', 'desc')->take(1)->get();
        return response()->json( ['banner'=>$banner],200 );
    }
    
}



    
