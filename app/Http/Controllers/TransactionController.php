<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Models\Expense;
use App\Models\UserIncome;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionController extends Controller
{
    public function index(FilterRequest $request)
    {
        $user = auth()->user();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $from = $request->input('from');
        $to = $request->input('to');

        $fromDateTime = $from ? Carbon::parse($from)->startOfDay() : null;
        $toDateTime = $to ? Carbon::parse($to)->endOfDay() : null;

        $incomeQuery = UserIncome::where('user_id', $user->id)
            ->orderBy('year', 'desc')
            ->orderBy('created_at', 'desc');

        $expenseQuery = Expense::where('user_id', $user->id)
            ->with('category')
            ->orderBy('date', 'desc');

        if ($fromDateTime && $toDateTime) {
            $incomeQuery->whereBetween('created_at', [$fromDateTime, $toDateTime]);
            $expenseQuery->whereBetween('date', [$fromDateTime, $toDateTime]);
        } else {
            $incomeQuery->where('year', $currentYear)
                ->where('month', $currentMonth);

            $expenseQuery->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear);
        }

        $incomes = $incomeQuery->get()->map(function ($income) {
            $income->type = 'income';
            $income->date = Carbon::createFromDate($income->year, $income->month, 1);
            return $income;
        });

        $expenses = $expenseQuery->get()->map(function ($expense) {
            $expense->type = 'expense';
            return $expense;
        });

        $transactions = $incomes->merge($expenses)->sortByDesc('date')->values();

        $perPage = 5;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $transactions->slice(($currentPage - 1) * $perPage, $perPage);

        $paginatedTransactions = new LengthAwarePaginator(
            $currentItems,
            $transactions->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        return view('transactions.index', ['transactions' => $paginatedTransactions]);
    }
}
