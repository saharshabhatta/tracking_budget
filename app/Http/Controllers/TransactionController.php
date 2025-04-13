<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\UserIncome;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $incomes = UserIncome::where('user_id', $user->id)
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->orderBy('year', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($income) {
                $income->type = 'income';
                $income->date = Carbon::createFromDate($income->year, $income->month, 1);
                return $income;
            });

        $expenses = Expense::where('user_id', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->with('category')
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($expense) {
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
