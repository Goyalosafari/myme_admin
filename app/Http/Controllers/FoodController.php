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
        $foodData = $this->food->where('type','food')->orderBy('id','desc')->get();
        $categories = Category::where('type', 'food')->get();
        return view('food',compact('foodData','categories'));
    }
    public function store(Request $request)
    {
        
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
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
            $food->category_id = $request->input('category_id');
            $food->type = 'food';
            $food->ref = $request->input('ref');
            $food->offer = $request->input('offer');
            $food->gst = $request->input('gst');
            $food->gst_value = ($request->input('gst')  * $request->input('price') ) / 100 ;
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
    
            return redirect('/food')->with('success','Food created successfully');
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
        //validate input
        $request->validate([
            'title' => 'required|string|max:255',
        ]);
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
        $food->category_id = $request->input('category_id');
        $food->ref = $request->input('ref');
        $food->offer = $request->input('offer');
        $food->gst = $request->input('gst');
        $food->gst_value = ($request->input('gst')  * $request->input('price') ) / 100 ;
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

        return redirect('/food')->with('success','Food updated successfully');
    }
    public function destroy($id)
    {
        $food = $this->food->find($id);
        $food->delete();
        return redirect('/food')->with('success','Food Deleted successfully');
    }
}
