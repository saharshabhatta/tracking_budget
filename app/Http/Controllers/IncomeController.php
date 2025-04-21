<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Models\UserIncome;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FilterRequest $request)
    {
        $query = UserIncome::where('user_id', Auth::id());

        if ($request->has('from') && $request->has('to') && $request->from && $request->to) {
            $from = $request->input('from') . ' 00:00:00';
            $to = $request->input('to') . ' 23:59:59';

            $query->whereBetween('created_at', [$from, $to]);
        } else {
            $currentMonth = now()->month;
            $currentYear = now()->year;

            $query->where('year', $currentYear)
                ->where('month', $currentMonth);
        }

        $income = $query->orderBy('month', 'desc')->paginate(10);

        return view('income.index', compact('income'));
    }


//    public function filter(FilterRequest $request)
//    {
//        $query = UserIncome::where('user_id', Auth::id());
//
//        if ($request->has('from') && $request->has('to') && $request->from && $request->to) {
//            $from = $request->input('from') . ' 00:00:00';
//            $to = $request->input('to') . ' 23:59:59';
//
//            $query->whereBetween('created_at', [$from, $to]);
//        }

//        if ($request->has('from') && $request->has('to') && $request->from && $request->to) {
//            $query->whereBetween('created_at', [$request->from, $request->to]);
//        }

//        $income = $query->orderBy('month', 'desc')->paginate(10);
//
//        return view('income.index', compact('income'));
//    }

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
            'monthly_income' => ['nullable', 'numeric', 'min:0', 'required_if:income_type,monthly', 'max:9999999999999999.9999',],
            'annual_income' => ['nullable', 'numeric', 'min:0', 'required_if:income_type,annual', 'max:9999999999999999.9999',],
        ], [
            'monthly_income.max' => 'The monthly income exceeds the maximum allowable.',
            'annual_income.max' => 'The annual income exceeds the maximum allowable value .',
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
            return back()->withErrors(['error' => 'Failed, please try again.']);
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

        if (! Gate::allows('update-income', $income)) {
            abort(403);
        }

        return view('income.edit', compact('income'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'income_type' => ['required', 'in:monthly,annual'],
            'monthly_income' => ['nullable', 'numeric', 'min:0', 'required_if:income_type,monthly', 'max:9999999999999999.9999',],
            'annual_income' => ['nullable', 'numeric', 'min:0', 'required_if:income_type,annual', 'max:9999999999999999.9999',],
        ], [
            'monthly_income.max' => 'The monthly income exceeds the maximum allowable.',
            'annual_income.max' => 'The annual income exceeds the maximum allowable value .',
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
