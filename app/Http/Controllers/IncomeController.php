<?php

namespace App\Http\Controllers;

use App\Models\UserIncome;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $income = UserIncome::where('user_id', Auth::id())
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->orderBy('month', 'desc')
            ->paginate(10);

        return view('income.index', compact('income'));
    }

    public function filter(Request $request)
    {
        $query = UserIncome::where('user_id', Auth::id());

        if ($request->has('from') && $request->has('to') && $request->from && $request->to) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        $income = $query->orderBy('month', 'desc')->paginate(10);

        return view('income.index', compact('income'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('income.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'monthly_income' => 'required|numeric|min:0',
            'annual_income' => 'required|numeric|min:0',
        ]);

        $currentMonth = now()->month;
        $currentYear = now()->year;

        DB::beginTransaction();
        try {
            UserIncome::create([
                'user_id' => Auth::id(),
                'month' => $currentMonth,
                'year' => $currentYear,
                'monthly_income' => $request->monthly_income,
                'annual_income' => $request->annual_income
            ]);

            DB::commit();
            return redirect()->route('incomes.index');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('incomes.index')->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $income = UserIncome::findOrFail($id);
        return view('income.edit', compact('income'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'monthly_income' => 'required|numeric|min:0',
            'annual_income' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $income=UserIncome::findOrFail($id);

            $income->update([
                'monthly_income'=>$request->monthly_income,
                'annual_income'=>$request->annual_income,
            ]);
            DB::commit();
            return redirect()->route('incomes.index');
        }
        catch (Exception){
            DB::rollBack();
            return redirect()->route('incomes.index')->with('error','Something went wrong');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $income=UserIncome::findOrFail($id);
        $income->delete();
        return redirect()->route('incomes.index');
    }
}
