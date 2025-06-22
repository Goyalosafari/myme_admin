<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerApiController extends Controller
{
    public function bannerListByPage($page_name)
    {
        $banner = Banner::with(['category' => function($query){
            $query->select('id','title');
        }])
        ->where('display_location', $page_name)->orderBy('id', 'desc')->take(1)->get();
        return response()->json( ['banner'=>$banner],200 );
    }

    public function index()
    {
        $banner = Banner::with(['category' => function($query){
            $query->select('id','title');
        }])->get();
        return response()->json( ['banner'=>$banner],200 );
    }
    
    public function Banner()
    {
        $banner = Banner::with(['category' => function($query){
            $query->select('id','title');
        }])
        ->where('display_location','home')->orderBy('banners.id', 'desc')->take(6)->get();
        return response()->json( ['banner'=>$banner],200 );
        
    }
    
        public function CategoryOne()
    {
        $banner = Banner::with(['category' => function($query){
            $query->select('id','title');
        }])
        ->where('display_location','page_1')->orderBy('id', 'desc')->get();
        return response()->json( ['banner'=>$banner],200 );
        
    }
    
        public function CategoryTwo()
    {
        $banner = Banner::with(['category' => function($query){
            $query->select('id','title');
        }])
        ->where('display_location','page_2')->orderBy('id', 'desc')->take(1)->get();
        return response()->json( ['banner'=>$banner],200 );
    }
    
        public function CategoryThree()
    {
        $banner = Banner::with(['category' => function($query){
            $query->select('id','title');
        }])
        ->where('display_location','page_3')->orderBy('id', 'desc')->take(1)->get();
        return response()->json( ['banner'=>$banner],200 );
    }
    
        public function CategoryFour()
    {
        $banner = Banner::with(['category' => function($query){
            $query->select('id','title');
        }])
        ->where('display_location','page_4')->orderBy('id', 'desc')->take(1)->get();
        return response()->json( ['banner'=>$banner],200 );
    }
    
            public function AdOne()
    {
        $banner = Banner::with(['category' => function($query){
            $query->select('id','title');
        }])
        ->where('display_location','ad_1')->orderBy('id', 'desc')->take(1)->get();
        return response()->json( ['banner'=>$banner],200 );
    }
}
