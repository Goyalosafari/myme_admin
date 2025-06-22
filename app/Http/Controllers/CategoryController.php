<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function index()
    {
        $categoryData = $this->category->where('type','food')->get();
        return view('category',compact('categoryData'));
    }
    public function store(Request $request)
    {
        //validate input
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        //handle logic and store into db
        $category = new Category();
        $category->title = $request->input('title');
        $category->company = '';
        $category->type = 'food';

        //handle image upload
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('images', 'public');
            $category->image = $imagePath;
        }
        $category->save();
        
        return redirect('/category')->with('success','Category created successfully');
    }

    public function edit($id)
    {
        $category = $this->category->find($id);
        
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        //validate input
        $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'nullable|string|max:255'
        ]);
        $category = $this->category->find($id);
        $category->title = $request->input('title');
        $category->company = $request->input('company');
        
        //handle image upload
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('images', 'public');
            $category->image = $imagePath;
        }
        $category->save();

        return redirect('/category')->with('success','Category updated successfully');
    }
    public function destroy($id)
    {
        $category = $this->category->find($id);
        $category->delete();
        return redirect('/category')->with('success','Category Deleted successfully');
    }
}
