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
        $recipeData = $this->recipe->all();
        $categories = Category::where('type', 'food')->get();
        $foods = Food::where('type', 'food')->get();
        return view('recipe',compact('recipeData','categories','foods'));
    }
    public function store(Request $request)
    {
        //validate input
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required|string|max:255',
            'ingredients' => 'required|string|max:255',
            'nutritional_facts' => 'required|string|max:255',
            'utensils' => 'required|string|max:255',
            'category_id' => 'required',
            'food_id' => 'required'

        ]);
        
        //handle logic and store into db
        $recipe = new Recipe();
        $recipe->title = $request->input('title');
        $recipe->category_id = $request->input('category_id');
        $recipe->food_id = $request->input('food_id');
        $recipe->num_of_serving = $request->input('num_of_serving');
        $recipe->description = $request->input('description');
        $recipe->ingredients = $request->input('ingredients');
        $recipe->nutritional_facts = $request->input('nutritional_facts');
        $recipe->utensils = $request->input('utensils');

        //handle image upload
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('images', 'public');
            $recipe->image = $imagePath;
        }
        $recipe->save();

        return redirect('/recipe')->with('success','Recipe created successfully');
    }

    public function edit($id)
    {
        $recipe = $this->recipe->find($id);
        
        return response()->json($recipe);
    }

    public function update(Request $request, $id)
    {
        //validate input
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'ingredients' => 'required|string|max:255',
            'nutritional_facts' => 'required|string|max:255',
            'utensils' => 'required|string|max:255',
            'category_id' => 'required',
            'food_id' => 'required'
        ]);
        $recipe = $this->recipe->find($id);
        $recipe->title = $request->input('title');
        $recipe->category_id = $request->input('category_id');
        $recipe->food_id = $request->input('food_id');
        $recipe->num_of_serving = $request->input('num_of_serving');
        $recipe->description = $request->input('description');
        $recipe->ingredients = $request->input('ingredients');
        $recipe->nutritional_facts = $request->input('nutritional_facts');
        $recipe->utensils = $request->input('utensils');
        
        //handle image upload
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('images', 'public');
            $recipe->image = $imagePath;
        }
        $recipe->save();

        return redirect('/recipe')->with('success','Recipe updated successfully');
    }
    public function destroy($id)
    {
        $recipe = $this->recipe->find($id);
        $recipe->delete();
        return redirect('/recipe')->with('success','Recipe Deleted successfully');
    }
}
