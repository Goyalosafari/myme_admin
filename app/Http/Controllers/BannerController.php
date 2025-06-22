<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    protected $banner, $category;

    public function __construct(Banner $banner, Category $category)
    {
        $this->banner = $banner;
        $this->category = $category;
    }

    public function index()
    {
        $bannerData = $this->banner->all();
        $categories = $this->category->all();
        return view('banner',compact('bannerData', 'categories'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        $banner = new Banner();
        $banner->title = $request->input('title');
        $banner->category_id = $request->input('category_id');
        $banner->display_location = $request->input('display_location');

        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('images', 'public');
            $banner->image = $imagePath;
        }
        $banner->save();

        return redirect('/banner')->with('success','Banner created successfully');
    }

    public function edit($id)
    {
        $banner = $this->banner->find($id);
        
        return response()->json($banner);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'nullable|string|max:255'
        ]);
        $banner = $this->banner->find($id);
        $banner->title = $request->input('title');
        $banner->category_id = $request->input('category_id');
        $banner->display_location = $request->input('display_location');

        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('images', 'public');
            $banner->image = $imagePath;
        }
        $banner->save();

        return redirect('/banner')->with('success','Banner updated successfully');
    }
    public function destroy($id)
    {
        $banner = $this->banner->find($id);
        $banner->delete();
        return redirect('/banner')->with('success','Banner Deleted successfully');
    }
}
