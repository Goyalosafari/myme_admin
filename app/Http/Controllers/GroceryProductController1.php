<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Food;
use Illuminate\Http\Request;

class GroceryProductController extends Controller
{
    protected $food;

    public function __construct(Food $food)
    {
        $this->food = $food;
    }

    public function index()
    {
        $groceryData = $this->food->where('type', 'grocery')->get();
        $categories = Category::where('type', 'grocery')->get();
        return view('grocery_product',compact('groceryData','categories'));
    }
    public function store(Request $request)
    {
        //validate input
        $err = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        //handle logic and store into db
        $food = new Food();
        $food->title = $request->input('title');
        $food->calorie = $request->input('calorie');
        $food->cooking_time = $request->input('cooking_time');
        $food->taste = $request->input('taste');
        $food->price = $request->input('price');
        $food->preferences = $request->input('preferences');
        $food->meal_type = $request->input('meal_type');
        $food->food_details = $request->input('food_details');
        $food->category_id = $request->input('category_id');
        $food->type = 'grocery';
        $food->ref = $request->input('ref');
        $food->offer = $request->input('offer');
        $food->gst = $request->input('gst');
        $food->gst_value = ($request->input('gst')  * $request->input('price') ) / 100 ;

        //handle image upload
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('images', 'public');
            $food->image = $imagePath;
        }
        $food->save();
        
        return redirect('/grocery/product')->with('success','Product created successfully');
    }

    public function edit($id)
    {
        $food = $this->food->find($id);
        
        return response()->json($food);
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
        $food->preferences = $request->input('preferences');
        $food->meal_type = $request->input('meal_type');
        $food->food_details = $request->input('food_details');
        $food->category_id = $request->input('category_id');
        $food->ref = $request->input('ref');
        $food->offer = $request->input('offer');
        $food->gst = $request->input('gst');
        $food->gst_value = ($request->input('gst')  * $request->input('price') ) / 100 ;

        //handle image upload
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('images', 'public');
            $food->image = $imagePath;
        }
        $food->save();

        return redirect('/grocery/product')->with('success','Product updated successfully');
    }
    public function destroy($id)
    {
        $food = $this->food->find($id);
        $food->delete();
        return redirect('/grocery/product')->with('success','Product Deleted successfully');
    }
}
