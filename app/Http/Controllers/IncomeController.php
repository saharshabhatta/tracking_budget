<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
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

    public function filter(FilterRequest $request)
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
            'income_type' => ['required', 'in:monthly,annual'],
            'monthly_income' => ['nullable', 'numeric', 'min:0', 'required_if:income_type,monthly'],
            'annual_income' => ['nullable', 'numeric', 'min:0', 'required_if:income_type,annual'],
        ]);

        $monthlyIncome = $request->income_type === 'monthly'
            ? $request->monthly_income
            : round($request->annual_income / 12, 2);

        $annualIncome = $request->income_type === 'annual'
            ? $request->annual_income
            : round($request->monthly_income * 12, 2);

        $currentMonth = now()->month;
        $currentYear = now()->year;

        DB::beginTransaction();
        try {
            UserIncome::create([
                'user_id' => Auth::id(),
                'month' => $currentMonth,
                'year' => $currentYear,
                'monthly_income' => $monthlyIncome,
                'annual_income' => $annualIncome
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
            'income_type' => ['required', 'in:monthly,annual'],
            'monthly_income' => ['nullable', 'numeric', 'min:0', 'required_if:income_type,monthly'],
            'annual_income' => ['nullable', 'numeric', 'min:0', 'required_if:income_type,annual'],
        ]);

        $monthlyIncome = $request->income_type === 'monthly'
            ? $request->monthly_income
            : round($request->annual_income / 12, 2);

        $annualIncome = $request->income_type === 'annual'
            ? $request->annual_income
            : round($request->monthly_income * 12, 2);

        DB::beginTransaction();

        try {
            $income=UserIncome::findOrFail($id);

            $income->update([
                'monthly_income'=>$monthlyIncome,
                'annual_income'=>$annualIncome,
            ]);
            DB::commit();
            return redirect()->route('incomes.index');
        }
        catch (Exception $e){
            DB::rollBack();
            return redirect()->route('incomes.index')->with('error','Something went wrong'. $e->getMessage());
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
