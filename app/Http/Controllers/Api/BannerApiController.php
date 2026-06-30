<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use App\Traits\ApiResponse;

class BannerApiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        return $this->success(BannerResource::collection(Banner::all()));
    }

    public function Banner()
    {
        return $this->success(
            BannerResource::collection(
                Banner::where('display_location', 'home')->latest()->take(6)->get()
            )
        );
    }

    public function CategoryOne()
    {
        return $this->success(
            BannerResource::collection(
                Banner::where('display_location', 'page_1')->latest()->get()
            )
        );
    }

    public function CategoryTwo()
    {
        return $this->success(
            BannerResource::collection(
                Banner::where('display_location', 'page_2')->latest()->get()
            )
        );
    }

    public function AdOne()
    {
        return $this->success(
            BannerResource::collection(
                Banner::where('display_location', 'ad_1')->latest()->get()
            )
        );
    }

    public function AdTwo()
    {
        return $this->success(
            BannerResource::collection(
                Banner::where('display_location', 'ad_2')->latest()->get()
            )
        );
    }

    public function AdThree()
    {
        return $this->success(
            BannerResource::collection(
                Banner::where('display_location', 'ad_3')->latest()->get()
            )
        );
    }

    public function AdFour()
    {
        return $this->success(
            BannerResource::collection(
                Banner::where('display_location', 'ad_4')->latest()->get()
            )
        );
    }
}
