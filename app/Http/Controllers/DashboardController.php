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
}
