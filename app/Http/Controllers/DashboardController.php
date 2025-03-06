<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\UserCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $hasCategories = UserCategory::where('user_id', $user->id)->exists();
        if (!$hasCategories) {
            return redirect()->route('register.categories');
        }

        $selectedMonth = $request->query('month', Carbon::now()->month);

        $userIncome = $user->userIncomes()
            ->where('month', $selectedMonth)
            ->where('year', Carbon::now()->year)
            ->first();

        if (!$userIncome) {
            return redirect()->route('register.incomes');
        }

        $user_categories = UserCategory::with('category')
            ->where('user_id', $user->id)
            ->whereMonth('create_date', $selectedMonth)
            ->get();

        $amount = [];
        $limit_percentage = [];
        foreach ($user_categories as $category) {
            $calculated_amount = $userIncome->monthly_income * $category->spending_percentage / 100;
            $amount[] = $calculated_amount;

            $limit_percentage[] = $category->spending_percentage;
        }

        $categories = Category::all();

        $totalSpendPerCategory = Category::withSum(['expenses' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }], 'amount')->get();

        $totalExpenses = $totalSpendPerCategory->sum('expenses_sum_amount');
        $remainingAmount = $userIncome->monthly_income - $totalExpenses;

        $actualLimits = [];
        $actualAmounts = [];

        foreach ($totalSpendPerCategory as $category) {
            $categoryTotalExpenses = $category->expenses_sum_amount;
            $actualLimit = ($categoryTotalExpenses / $userIncome->monthly_income) * 100;
            $actualLimits[$category->id] = $actualLimit;

            $actualAmount = ($userIncome->monthly_income * $actualLimit) / 100;
            $actualAmounts[$category->id] = $actualAmount;
        }

        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $currentMonth = $months[$selectedMonth - 1];

        return view('dashboard', compact('user_categories', 'amount', 'totalSpendPerCategory', 'categories', 'remainingAmount', 'months', 'currentMonth', 'selectedMonth', 'userIncome', 'actualLimits', 'actualAmounts', 'limit_percentage'
        ));
    }
}
