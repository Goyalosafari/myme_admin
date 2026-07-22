<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    protected $food;

    public function __construct(Food $food)
    {
        $this->food = $food;
    }

    public function index()
    {
        $foodData = $this->food->where('type','food')->orderBy('id','desc')->paginate(20);
        $categories = Category::where('type', 'food')->get();
        return view('food',compact('foodData','categories'));
    }
    private function foodRules(bool $imageRequired): array
    {
        return [
            'title'        => 'required|string|max:255',
            'image'        => ($imageRequired ? 'required' : 'nullable') . '|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'taste'        => 'required|string|max:255',
            'price'        => 'required|numeric|min:0',
            'offer_price'  => 'required|numeric|min:0',
            'mrp'          => 'required|numeric|min:0',
            'gst'          => 'nullable|numeric|min:0|max:100',
            'veg'          => 'required|in:yes,no',
            'meal_type'    => 'required|string|max:255',
            'food_details' => 'required|string',
            'categories'   => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'ref'          => 'nullable|string',
            'offer'        => 'nullable|string',
        ];
    }

    private function foodMessages(): array
    {
        return [
            'title.required'        => 'Food name is required.',
            'title.max'             => 'Food name must not exceed 255 characters.',
            'image.required'        => 'Please upload a food image.',
            'image.image'           => 'The uploaded file must be an image.',
            'image.mimes'           => 'Image must be JPEG, PNG, JPG, GIF, or WebP.',
            'image.max'             => 'Image size must not exceed 10 MB.',
            'taste.required'        => 'Taste is required.',
            'taste.max'             => 'Taste must not exceed 255 characters.',
            'price.required'        => 'Price is required.',
            'price.numeric'         => 'Price must be a valid number.',
            'price.min'             => 'Price must be 0 or greater.',
            'offer_price.required'  => 'Offer price is required.',
            'offer_price.numeric'   => 'Offer price must be a valid number.',
            'offer_price.min'       => 'Offer price must be 0 or greater.',
            'mrp.required'          => 'MRP is required.',
            'mrp.numeric'           => 'MRP must be a valid number.',
            'mrp.min'               => 'MRP must be 0 or greater.',
            'gst.numeric'           => 'GST must be a valid number.',
            'gst.min'               => 'GST cannot be negative.',
            'gst.max'               => 'GST must not exceed 100%.',
            'veg.required'          => 'Please select veg or non-veg.',
            'veg.in'                => 'Veg must be yes or no.',
            'meal_type.required'    => 'Meal type is required.',
            'meal_type.max'         => 'Meal type must not exceed 255 characters.',
            'food_details.required' => 'Food details are required.',
            'categories.required'   => 'Please select at least one category.',
            'categories.array'      => 'Invalid category selection.',
            'categories.min'        => 'Please select at least one category.',
            'categories.*.exists'   => 'One or more selected categories are invalid.',
        ];
    }

    public function store(Request $request)
    {
        $request->validate($this->foodRules(true), $this->foodMessages());
        try{    
            $food = new Food();
            $food->title = $request->input('title');
            $food->calorie = $request->input('calorie');
            $food->cooking_time = $request->input('cooking_time');
            $food->taste = $request->input('taste');
            $food->price = $request->input('price');
            $food->offer_price = $request->input('offer_price');
            $food->mrp = $request->input('mrp');
            $food->margin = $request->input('margin');
            $food->preferences = $request->input('preferences');
            $food->meal_type = $request->input('meal_type');
            $food->food_details = $request->input('food_details');
            $food->category_id = $request->input('categories')[0] ?? null;
            $food->type = 'food';
            $food->ref = $request->input('ref');
            $food->offer = $request->input('offer');
            $food->gst = $request->input('gst');
            $food->gst_value = (floatval($request->input('gst')) * floatval($request->input('price'))) / 100;
            if($request->input('veg') !== null){
                $food->veg = $request->input('veg');
            }else{
                $food->veg = "no";
            }
    
            //handle image upload
            if($request->hasFile('image')){
                $imagePath = $request->file('image')->store('images', 'public');
                $food->image = $imagePath;
            }
            $food->save();
            $food->categories()->attach($request->input('categories'));
    
            return redirect()->route('food.index')->with('success','Food created successfully');
        }catch(\Exception $e){
            \Log::error('Error storing food', ['exception' => $e]);
            return redirect()->route('error.server_error');
        }
    }

    public function edit($id)
    {
        $food = $this->food->with('categories')->find($id);
        $selectedCategories = $food->categories->pluck('id')->toArray();

        return response()->json(['food'=>$food, 'selectedCategories' =>$selectedCategories ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate($this->foodRules(false), $this->foodMessages());
        $food = $this->food->find($id);
        $food->title = $request->input('title');
        $food->calorie = $request->input('calorie');
        $food->cooking_time = $request->input('cooking_time');
        $food->taste = $request->input('taste');
        $food->price = $request->input('price');
        $food->offer_price = $request->input('offer_price');
        $food->mrp = $request->input('mrp');
        $food->margin = $request->input('margin');
        $food->preferences = $request->input('preferences');
        $food->meal_type = $request->input('meal_type');
        $food->food_details = $request->input('food_details');
        $food->category_id = $request->input('categories')[0] ?? null;
        $food->ref = $request->input('ref');
        $food->offer = $request->input('offer');
        $food->gst = $request->input('gst');
        $food->gst_value = (floatval($request->input('gst')) * floatval($request->input('price'))) / 100;
        if($request->input('veg') !== null){
            $food->veg = $request->input('veg');
        }else{
            $food->veg = "no";
        }
        
        //handle image upload
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('images', 'public');
            $food->image = $imagePath;
        }
        $food->save();
         $food->categories()->sync($request->input('categories'));

        return redirect()->route('food.index')->with('success','Food updated successfully');
    }
    public function destroy($id)
    {
        $food = $this->food->find($id);
        $food->delete();
        return redirect()->route('food.index')->with('success','Food Deleted successfully');
    }
}
