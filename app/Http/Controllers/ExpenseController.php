<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeExpenseRequest;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Statement;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Expense::with('category')->where('user_id', auth()->id());

        $expenses = $query->get();
        $categories = Category::all();

        return view('expenses.index', compact('expenses', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('expenses.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(storeExpenseRequest $request)
    {
        $validated = $request->validated();

        $validated['user_id'] = auth()->id();

        $expense = Expense::create($validated);

        $statement = new Statement();
        $statement->statementable()->associate($expense);

        $statement->save();

        return redirect()->route('expenses.index');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $expense = Expense::with('category')->findOrFail($id);
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $expense = Expense::findOrFail($id);

        if($expense->user_id != auth()->id()){
            abort(401);
        }

        $categories = Category::all();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(storeExpenseRequest $request, $id)
    {
        $validated = $request->validated();

        $validated['user_id'] = auth()->id();

        $expense = Expense::findOrFail($id);
        $expense->update($validated);

        return redirect()->route('expenses.index');
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
