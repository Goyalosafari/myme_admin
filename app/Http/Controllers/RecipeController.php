<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Food;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    protected $recipe;

    public function __construct(Recipe $recipe)
    {
        $this->recipe = $recipe;
    }

    public function index()
    {
        $recipeData = $this->recipe->latest()->paginate(20);
        $categories = Category::where('type', 'food')->get();
        $foods = Food::where('type', 'food')->get();
        return view('recipe', compact('recipeData', 'categories', 'foods'));
    }

    private function rules(bool $imageRequired): array
    {
        return [
            'title'             => 'required|string|max:255',
            'image'             => ($imageRequired ? 'required' : 'nullable') . '|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'category_id'       => 'required|exists:categories,id',
            'food_id'           => 'required|exists:foods,id',
            'description'       => 'required|string',
            'ingredients'       => 'required|string',
            'nutritional_facts' => 'required|string',
            'utensils'          => 'required|string',
            'num_of_serving'    => 'nullable|string|max:100',
        ];
    }

    private function messages(): array
    {
        return [
            'title.required'              => 'Recipe title is required.',
            'title.max'                   => 'Title must not exceed 255 characters.',
            'image.required'              => 'Please upload a recipe image.',
            'image.image'                 => 'The file must be an image.',
            'image.mimes'                 => 'Image must be JPEG, PNG, JPG, GIF, or WebP.',
            'image.max'                   => 'Image size must not exceed 10 MB.',
            'category_id.required'        => 'Please select a category.',
            'category_id.exists'          => 'The selected category does not exist.',
            'food_id.required'            => 'Please select a food item.',
            'food_id.exists'              => 'The selected food item does not exist.',
            'description.required'        => 'Description is required.',
            'ingredients.required'        => 'Ingredients are required.',
            'nutritional_facts.required'  => 'Nutritional facts are required.',
            'utensils.required'           => 'Utensils are required.',
            'num_of_serving.max'          => 'Number of servings must not exceed 100 characters.',
        ];
    }

    public function store(Request $request)
    {
        $request->validate($this->rules(true), $this->messages());

        $recipe = new Recipe();
        $recipe->title = $request->input('title');
        $recipe->category_id = $request->input('category_id');
        $recipe->food_id = $request->input('food_id');
        $recipe->num_of_serving = $request->input('num_of_serving');
        $recipe->description = $request->input('description');
        $recipe->ingredients = $request->input('ingredients');
        $recipe->nutritional_facts = $request->input('nutritional_facts');
        $recipe->utensils = $request->input('utensils');

        if ($request->hasFile('image')) {
            $recipe->image = $request->file('image')->store('images', 'public');
        }
        $recipe->save();

        return redirect()->route('recipe.index')->with('success', 'Recipe created successfully');
    }

    public function edit($id)
    {
        return response()->json($this->recipe->find($id));
    }

    public function show($id)
    {
        return response()->json($this->recipe->with('category', 'food')->find($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate($this->rules(false), $this->messages());

        $recipe = $this->recipe->find($id);
        $recipe->title = $request->input('title');
        $recipe->category_id = $request->input('category_id');
        $recipe->food_id = $request->input('food_id');
        $recipe->num_of_serving = $request->input('num_of_serving');
        $recipe->description = $request->input('description');
        $recipe->ingredients = $request->input('ingredients');
        $recipe->nutritional_facts = $request->input('nutritional_facts');
        $recipe->utensils = $request->input('utensils');

        if ($request->hasFile('image')) {
            $recipe->image = $request->file('image')->store('images', 'public');
        }
        $recipe->save();

        return redirect()->route('recipe.index')->with('success', 'Recipe updated successfully');
    }

    public function destroy($id)
    {
        $this->recipe->find($id)->delete();
        return redirect()->route('recipe.index')->with('success', 'Recipe deleted successfully');
    }
}
