<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeCategoryRequest;
use App\Models\Category;
use App\Models\UserCategory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $selectedMonth = $request->query('month', Carbon::now()->format('Y-m'));

        $categories = DB::table('user_categories')
            ->join('categories', 'user_categories.category_id', '=', 'categories.id')
            ->where('user_categories.user_id', auth()->id())
            ->when($search, function ($query) use ($search) {
                return $query->where('categories.name', 'like', '%' . $search . '%');
            })
            ->whereMonth('user_categories.create_date', Carbon::parse($selectedMonth)->month)
            ->whereYear('user_categories.create_date', Carbon::parse($selectedMonth)->year)
            ->orderBy('user_categories.create_date', 'desc')
            ->select('categories.*', 'user_categories.spending_percentage', 'user_categories.create_date')
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', compact('categories', 'selectedMonth'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $now = Carbon::now();

        $totalSpent = UserCategory::where('user_id', auth()->id())
            ->whereMonth('create_date', $now->month)
            ->whereYear('create_date', $now->year)
            ->sum('spending_percentage');

        return view('categories.create', compact('totalSpent'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(storeCategoryRequest $request)
    {
        DB::beginTransaction();

        try {
            $now = Carbon::now();

            $totalSpent = UserCategory::where('user_id', auth()->id())
                ->whereMonth('create_date', $now->month)
                ->whereYear('create_date', $now->year)
                ->sum('spending_percentage');

            $newPercentage = $request->input('spending_percentage');

            if (($totalSpent + $newPercentage) > 100) {
                $remaining = 100 - $totalSpent;
                session()->flash('error', "Limit exceeded! You can only allocate up to {$remaining}% more.");
                return back()->withInput();
            }

            $category = Category::where('name', $request->name)->first();

            if ($category && $category->user_id != auth()->id()) {
                UserCategory::create([
                    'user_id' => auth()->id(),
                    'spending_percentage' => $newPercentage,
                    'category_id' => $category->id,
                ]);
                DB::commit();
                return redirect('/categories')->with('success', 'You have been successfully associated with the existing category.');
            }

            if ($category && $category->trashed()) {
                $category->restore();
            }

            if (!$category) {
                $category = Category::create([
                    'user_id' => auth()->id(),
                    'name' => $request->input('name'),
                ]);
            }

            UserCategory::create([
                'user_id' => auth()->id(),
                'spending_percentage' => $newPercentage,
                'category_id' => $category->id,
            ]);

            DB::commit();
            return redirect('/categories')->with('success', 'Category added successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withError('Something went wrong. Please try again later.');
        }
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
        $category = Category::findOrFail($id);

        $user_category = UserCategory::where('user_id', auth()->id())
            ->where('category_id', $category->id)
            ->first();

        $now = Carbon::now();

        $totalSpent = UserCategory::where('user_id', auth()->id())
            ->where('category_id', '!=', $category->id)
            ->whereMonth('create_date', $now->month)
            ->whereYear('create_date', $now->year)
            ->sum('spending_percentage');

        return view('categories.edit', compact('category', 'user_category', 'totalSpent'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(storeCategoryRequest $request, string $id)
    {
        DB::beginTransaction();

        try {
            $now = Carbon::now();

            $category = Category::findOrFail($id);

            $totalSpent = UserCategory::where('user_id', auth()->id())
                ->where('category_id', '!=', $category->id)
                ->whereMonth('create_date', $now->month)
                ->whereYear('create_date', $now->year)
                ->sum('spending_percentage');

            $newPercentage = $request->input('spending_percentage');

            if (($totalSpent + $newPercentage) > 100) {
                $remaining = 100 - $totalSpent;
                session()->flash('error', "Limit exceeded! You can only allocate up to {$remaining}% more.");
                return back()->withInput();
            }

            $existingCategory = Category::where('name', $request->name)->first();

            if ($existingCategory && $existingCategory->user_id != auth()->id()) {
                UserCategory::where('user_id', auth()->id())
                    ->where('category_id', $category->id)
                    ->delete();

                UserCategory::create([
                    'user_id' => auth()->id(),
                    'spending_percentage' => $newPercentage,
                    'category_id' => $existingCategory->id,
                ]);

                DB::commit();
                return redirect('/categories')->with('success', 'You have been successfully associated with the existing category.');
            }

            if ($existingCategory && $existingCategory->trashed()) {
                $existingCategory->restore();
            }

            if (!$existingCategory) {
                $existingCategory = Category::create([
                    'user_id' => auth()->id(),
                    'name' => $request->input('name'),
                ]);
            }

            UserCategory::where('user_id', auth()->id())
                ->where('category_id', $category->id)
                ->delete();

            UserCategory::create([
                'user_id' => auth()->id(),
                'spending_percentage' => $newPercentage,
                'category_id' => $existingCategory->id,
            ]);

            DB::commit();
            return redirect('/categories')->with('success', 'Category updated successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withError('Something went wrong. Please try again later.');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        $category->users()->detach(auth()->id());

        return redirect('/categories');
    }
}
