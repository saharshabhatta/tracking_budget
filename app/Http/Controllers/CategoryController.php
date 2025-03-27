<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeCategoryRequest;
use App\Models\Category;
use App\Models\UserCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(storeCategoryRequest $request)
    {
        $category = Category::withTrashed()->where('name', $request->name)->first();

        if ($category) {
            $category->restore();
        } else {
            $category = Category::create([
                'user_id' => auth()->id(),
                'name' => $request->input('name'),
            ]);
        }

        UserCategory::create([
            'user_id' => auth()->id(),
            'spending_percentage' => $request->input('spending_percentage'),
            'category_id' => $category->id,
        ]);

        return redirect('/categories')->with('success', 'Category created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);

        if ($category->user_id != auth()->id()) {
            abort(403);
        }

        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
//        $category = Category::findOrFail($id);
//        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(storeCategoryRequest $request, string $id)
    {
//        $category = Category::findOrFail($id);
//        $category->update([
//            'name' => $request->input('name'),
//        ]);
//
//        return redirect('/categories')->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Category::destroy($id);
        return redirect('/categories')->with('success', 'Category deleted successfully!');
    }
}
