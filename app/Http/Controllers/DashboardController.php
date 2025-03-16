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

    $selectedMonth = (int) $request->query('month', Carbon::now()->month);

    if ($selectedMonth != Carbon::now()->month) {
        return $this->showFormForNewMonth($selectedMonth);
    }

    $userIncome = $user->userIncomes()
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

    $totalSpendPerCategory = Category::withSum(['expenses' => function ($query) use ($user, $selectedMonth) {
        $query->where('user_id', $user->id)->whereMonth('created_at', $selectedMonth);
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

    $unselectedCategories = [];
    $categories = Category::all();
    foreach ($categories as $category) {
        $userCategory = $user_categories->firstWhere('category_id', $category->id);
        if (!$userCategory) {
            $expenses = Expense::where('user_id', $user->id)
                ->where('category_id', $category->id)
                ->whereMonth('created_at', $selectedMonth)
                ->sum('amount');

            if ($expenses > 0) {
                $actualLimit = ($expenses / $userIncome->monthly_income) * 100;

                $unselectedCategories[] = [
                    'category_name' => $category->name,
                    'amount' => 0,
                    'limit' => 0,
                    'actual_limit' => number_format($actualLimit, 2),
                    'actual_amount' => $expenses,
                ];
            } else {
                $unselectedCategories[] = [
                    'category_name' => $category->name,
                    'amount' => 0,
                    'limit' => 0,
                    'actual_limit' => 0,
                    'actual_amount' => 0,
                ];
            }
        }
    }

    $months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    return view('dashboard', compact(
        'user_categories', 'amount', 'totalSpendPerCategory', 'categories',
        'remainingAmount', 'months', 'selectedMonth', 'userIncome', 'actualLimits',
        'actualAmounts', 'limit_percentage', 'unselectedCategories'
    ));
}

public function showFormForNewMonth($selectedMonth)
{
    $user = Auth::user();

    $selectedMonth = (int) $selectedMonth;

    $previousMonth = $selectedMonth - 1;
    if ($previousMonth < 1) {
        $previousMonth = 12; 
    }

    $previousUserIncome = $user->userIncomes()
        ->where('month', $previousMonth)
        ->where('year', Carbon::now()->year)
        ->first();

    $previousCategoryPercentages = UserCategory::where('user_id', $user->id)
        ->whereMonth('create_date', $previousMonth)
        ->pluck('spending_percentage', 'category_id');

    $categories = Category::all();

    return view('form_for_new_month', compact(
        'previousUserIncome', 'previousCategoryPercentages', 'selectedMonth', 'categories'
    ));
}

}
