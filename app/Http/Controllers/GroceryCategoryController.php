<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class GroceryCategoryController extends Controller
{
    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function index()
    {
        $categoryData = $this->category->where('type', 'grocery')->latest()->paginate(20);
        return view('grocery_category', compact('categoryData'));
    }

    private function rules(bool $imageRequired): array
    {
        return [
            'title'   => 'required|string|max:255',
            'image'   => ($imageRequired ? 'required' : 'nullable') . '|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'company' => 'nullable|string|max:255',
        ];
    }

    private function messages(): array
    {
        return [
            'title.required' => 'Category title is required.',
            'title.max'      => 'Title must not exceed 255 characters.',
            'image.required' => 'Please upload a category image.',
            'image.image'    => 'The file must be an image.',
            'image.mimes'    => 'Image must be JPEG, PNG, JPG, GIF, or WebP.',
            'image.max'      => 'Image size must not exceed 10 MB.',
            'company.max'    => 'Company must not exceed 255 characters.',
        ];
    }

    public function store(Request $request)
    {
        $request->validate($this->rules(true), $this->messages());

        $category = new Category();
        $category->title = $request->input('title');
        $category->company = $request->input('company');
        $category->type = 'grocery';

        if ($request->hasFile('image')) {
            $category->image = $request->file('image')->store('images', 'public');
        }
        $category->save();

        return redirect()->route('grocery_category.index')->with('success', 'Category created successfully');
    }

    public function edit($id)
    {
        return response()->json($this->category->find($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate($this->rules(false), $this->messages());

        $category = $this->category->find($id);
        $category->title = $request->input('title');
        $category->company = $request->input('company');

        if ($request->hasFile('image')) {
            $category->image = $request->file('image')->store('images', 'public');
        }
        $category->save();

        return redirect()->route('grocery_category.index')->with('success', 'Category updated successfully');
    }

    public function destroy($id)
    {
        $this->category->find($id)->delete();
        return redirect()->route('grocery_category.index')->with('success', 'Category deleted successfully');
    }
}
