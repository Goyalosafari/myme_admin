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
        $bannerData = $this->banner->latest()->paginate(20);
        $categories = $this->category->all();
        return view('banner', compact('bannerData', 'categories'));
    }

    private function rules(bool $imageRequired): array
    {
        return [
            'title'            => 'required|string|max:255',
            'image'            => ($imageRequired ? 'required' : 'nullable') . '|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'display_location' => 'required|in:home,ad_1,ad_2,ad_3,ad_4',
            'category_id'      => 'nullable|exists:categories,id',
        ];
    }

    private function messages(): array
    {
        return [
            'title.required'             => 'Banner title is required.',
            'title.max'                  => 'Title must not exceed 255 characters.',
            'image.required'             => 'Please upload a banner image.',
            'image.image'                => 'The file must be an image.',
            'image.mimes'                => 'Image must be JPEG, PNG, JPG, GIF, or WebP.',
            'image.max'                  => 'Image size must not exceed 10 MB.',
            'display_location.required'  => 'Please select a display location.',
            'display_location.in'        => 'Invalid display location selected.',
            'category_id.exists'         => 'The selected category does not exist.',
        ];
    }

    public function store(Request $request)
    {
        $request->validate($this->rules(true), $this->messages());

        $banner = new Banner();
        $banner->title = $request->input('title');
        $banner->category_id = $request->input('category_id');
        $banner->display_location = $request->input('display_location');

        if ($request->hasFile('image')) {
            $banner->image = $request->file('image')->store('images', 'public');
        }
        $banner->save();

        return redirect()->route('banner.index')->with('success', 'Banner created successfully');
    }

    public function edit($id)
    {
        return response()->json($this->banner->find($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate($this->rules(false), $this->messages());

        $banner = $this->banner->find($id);
        $banner->title = $request->input('title');
        $banner->category_id = $request->input('category_id');
        $banner->display_location = $request->input('display_location');

        if ($request->hasFile('image')) {
            $banner->image = $request->file('image')->store('images', 'public');
        }
        $banner->save();

        return redirect()->route('banner.index')->with('success', 'Banner updated successfully');
    }

    public function destroy($id)
    {
        $this->banner->find($id)->delete();
        return redirect()->route('banner.index')->with('success', 'Banner deleted successfully');
    }
}
