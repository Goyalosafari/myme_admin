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
        $groceryData = $this->food->where('type', 'grocery')->latest()->paginate(20);
        $categories = Category::where('type', 'grocery')->get();
        return view('grocery_product', compact('groceryData', 'categories'));
    }

    private function rules(bool $imageRequired): array
    {
        return [
            'title'       => 'required|string|max:255',
            'image'       => ($imageRequired ? 'required' : 'nullable') . '|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'price'       => 'nullable|numeric|min:0',
            'offer_price' => 'nullable|numeric|min:0',
            'mrp'         => 'nullable|numeric|min:0',
            'gst'         => 'nullable|numeric|min:0|max:100',
            'category_id' => 'nullable|exists:categories,id',
        ];
    }

    private function messages(): array
    {
        return [
            'title.required'      => 'Product title is required.',
            'title.max'           => 'Title must not exceed 255 characters.',
            'image.required'      => 'Please upload a product image.',
            'image.image'         => 'The file must be an image.',
            'image.mimes'         => 'Image must be JPEG, PNG, JPG, GIF, or WebP.',
            'image.max'           => 'Image size must not exceed 10 MB.',
            'price.numeric'       => 'Price must be a valid number.',
            'price.min'           => 'Price cannot be negative.',
            'offer_price.numeric' => 'Offer price must be a valid number.',
            'offer_price.min'     => 'Offer price cannot be negative.',
            'mrp.numeric'         => 'MRP must be a valid number.',
            'mrp.min'             => 'MRP cannot be negative.',
            'gst.numeric'         => 'GST must be a valid number.',
            'gst.min'             => 'GST cannot be negative.',
            'gst.max'             => 'GST must not exceed 100%.',
            'category_id.exists'  => 'The selected category does not exist.',
        ];
    }

    public function store(Request $request)
    {
        $request->validate($this->rules(true), $this->messages());

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
        $food->type = 'grocery';
        $food->ref = $request->input('ref');
        $food->offer = $request->input('offer');
        $food->gst = $request->input('gst');
        $food->gst_value = (floatval($request->input('gst')) * floatval($request->input('price'))) / 100;

        if ($request->hasFile('image')) {
            $food->image = $request->file('image')->store('images', 'public');
        }
        $food->save();

        return redirect()->route('grocery_product.index')->with('success', 'Product created successfully');
    }

    public function edit($id)
    {
        return response()->json($this->food->find($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate($this->rules(false), $this->messages());

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
        $food->gst_value = (floatval($request->input('gst')) * floatval($request->input('price'))) / 100;

        if ($request->hasFile('image')) {
            $food->image = $request->file('image')->store('images', 'public');
        }
        $food->save();

        return redirect()->route('grocery_product.index')->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $this->food->find($id)->delete();
        return redirect()->route('grocery_product.index')->with('success', 'Product deleted successfully');
    }
}
