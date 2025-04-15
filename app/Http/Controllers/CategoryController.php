<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
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

    public function store(FilterRequest $request)
    {
        DB::beginTransaction();

        try {
            $now = Carbon::now();
            $userId = auth()->id();
            $newPercentage = $request->input('spending_percentage');

            $totalSpent = UserCategory::where('user_id', $userId)
                ->whereMonth('create_date', $now->month)
                ->whereYear('create_date', $now->year)
                ->sum('spending_percentage');

            if (($totalSpent + $newPercentage) > 100) {
                $remaining = 100 - $totalSpent;
                session()->flash('error', "Limit exceeded! You can only allocate up to {$remaining}% more.");
                return back()->withInput();
            }

            $category = Category::withTrashed()
                ->whereRaw('LOWER(name) = ?', [strtolower($request->name)])
                ->first();

            if ($category && $category->trashed() && $category->user_id == $userId) {
                $category->restore();
            }

            if ($category) {
                $alreadyLinked = UserCategory::where('user_id', $userId)
                    ->where('category_id', $category->id)
                    ->exists();

                if ($alreadyLinked) {
                    session()->flash('error', 'Category already exists.');
                    return back()->withInput();
                }
            }

            if (!$category) {
                $category = Category::create([
                    'user_id' => $userId,
                    'name' => $request->name,
                ]);
            }

            UserCategory::create([
                'user_id' => $userId,
                'spending_percentage' => $newPercentage,
                'category_id' => $category->id,
            ]);

            DB::commit();
            return redirect('/categories')->with('success', 'Category added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Something went wrong. Please try again later.']);
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

    public function update(FilterRequest $request, string $id)
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

            $isCategoryNameUpdated = $request->has('name') && $request->name !== $category->name;

            if (!$isCategoryNameUpdated) {
                UserCategory::where('user_id', auth()->id())
                    ->where('category_id', $category->id)
                    ->update(['spending_percentage' => $newPercentage]);

                DB::commit();
                return redirect('/categories')->with('success', 'Spending percentage updated successfully!');
            }

            $existingCategory = Category::where('name', $request->name)->first();

            if ($existingCategory) {
                $alreadyLinked = UserCategory::where('user_id', auth()->id())
                    ->where('category_id', $existingCategory->id)
                    ->exists();

                if ($alreadyLinked) {
                    session()->flash('error', 'You are already associated with this category.');
                    return back()->withInput();
                }

                if ($existingCategory->trashed()) {
                    $existingCategory->restore();
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
                return redirect('/categories')->with('success', 'You have been successfully associated with the existing category.');
            }

            $newCategory = Category::create([
                'user_id' => auth()->id(),
                'name' => $request->input('name'),
            ]);
            UserCategory::where('user_id', auth()->id())
                ->where('category_id', $category->id)
                ->delete();

            UserCategory::create([
                'user_id' => auth()->id(),
                'spending_percentage' => $newPercentage,
                'category_id' => $newCategory->id,
            ]);

            DB::commit();
            return redirect('/categories')->with('success', 'Category updated successfully!');

        } catch (Exception ) {
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
