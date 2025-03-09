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

//        foreach ($categories as $category) {
//            $comments = $category->comments;
//            dd($comments->toArray());
//        }

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

        $category = Category::create([
            'name' => $request->input('name'),
        ]);

        $user_category=UserCategory::create([
            'user_id'=>auth()->id(),
            'spending_percentage' => $request->input('spending_percentage'),
            'category_id'=>$category->id,
        ]);

        return redirect('/categories')->with('success', 'Category created successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);

        if($category->user_id != auth()->id()){
            abort(403);
        }

        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'spending_percentage'=>'required|numeric|min:0|max:100',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->input('name'),
        ]);

        return redirect('/categories')->with('success', 'Category updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category=Category::destroy($id);
    }
}
