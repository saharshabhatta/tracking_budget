<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Http\Requests\storeExpenseRequest;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Statement;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FilterRequest $request)
    {
        $userId = auth()->id();
        $query = Expense::with('category')->where('user_id', $userId);

        if (!$request->filled('from') && !$request->filled('to')) {
            $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
            $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
            $query->whereBetween('date', [$startOfMonth, $endOfMonth]);
        } elseif ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('date', [$request->from, $request->to]);
        }

        if ($request->filled('search')) {
            $query->where(function ($subQuery) use ($request) {
                $subQuery->where('description', 'like', '%' . $request->search . '%')
                    ->orWhereHas('category', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $expenses = $query
            ->orderBy('date', 'desc')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::all();

        return view('expenses.index', compact('expenses', 'categories'));
    }



//    public function filter(Request $request)
//    {
//        $query = Expense::with('category')->where('user_id', auth()->id());
//
//        if ($request->has('from') && $request->has('to') && $request->from && $request->to) {
//            $query->whereBetween('date', [$request->from, $request->to]);
//        }
//
//        $expenses = $query->get();
//        $categories = Category::all();
//
//        return view('expenses.index', compact('expenses', 'categories'));
//    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userId = auth()->id();

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $userCategories = Category::whereHas('users', function($query) use ($userId, $currentMonth, $currentYear) {
            $query->where('user_id', $userId)
                ->whereMonth('create_date', $currentMonth)
                ->whereYear('create_date', $currentYear);
        })
            ->get();

        return view('expenses.create', compact('userCategories'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(storeExpenseRequest $request)
    {
        DB::beginTransaction();

        try {
            $expense = Expense::create([
                'user_id' => auth()->id(),
                'category_id' => $request->category_id,
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $request->date,
            ]);

            $statement = new Statement();
            $statement->statementable()->associate($expense);
            $statement->save();

            DB::commit();

            return redirect()->route('expenses.index');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create expense, please try again.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $expense = Expense::findOrFail($id);

        $user_categories=$expense->category;

        if ($expense->user_id != auth()->id()) {
            abort(401);
        }

        $categories = Category::whereHas('users', function($query) use ($expense) {
            $query->where('user_id', auth()->id())
            ->whereMonth('create_date', '=', Carbon::parse($expense->date))
            ->whereYear('create_date', '=', Carbon::parse($expense->date));
        })->get();

        $categories=$categories->merge(collect([$user_categories]));
        return view('expenses.edit', compact('expense', 'categories', 'user_categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(storeExpenseRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $expense = Expense::findOrFail($id);

            $expense->update([
                'category_id' => $request->category_id,
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $request->date,
            ]);

            DB::commit();

            return redirect()->route('expenses.index');
        } catch (Exception) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update expense, please try again.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return redirect()->route('expenses.index');
    }
}
