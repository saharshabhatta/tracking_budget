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

        if (!$this->hasCategories($user)) {
            return redirect()->route('register.categories');
        }

        $selectedMonth = (int) $request->query('month', Carbon::now()->month);

        $userIncome = $this->getUserIncome($user);
        if (!$userIncome) {
            return redirect()->route('register.incomes');
        }

        if (!$this->hasUserCategoryForMonth($user, $selectedMonth)) {
            return redirect()->route('dashboard.formForNewMonth', ['month' => $selectedMonth]);
        }

        $user_categories = $this->getUserCategories($user, $selectedMonth);

        $Income = $this->getUserIncomeForMonth($user, $selectedMonth);

        $amount = $this->calculateAmounts($user_categories, $Income);
        $limit_percentage = $this->getLimitPercentages($user_categories);

        $totalSpendPerCategory = $this->getTotalSpendPerCategory($user, $selectedMonth);

        $remainingAmount = $this->calculateRemainingAmount($Income, $totalSpendPerCategory);

        $actualLimits = $this->calculateActualLimits($totalSpendPerCategory, $Income);
        $actualAmounts = $this->calculateActualAmounts($actualLimits, $Income);

        $unselectedCategories = $this->getUnselectedCategories($user, $user_categories, $selectedMonth, $Income);

        $months = $this->getMonths();

        return view('dashboard', compact('user_categories', 'amount', 'totalSpendPerCategory','remainingAmount', 'months', 'selectedMonth', 'userIncome', 'actualLimits', 'actualAmounts', 'limit_percentage', 'unselectedCategories', 'Income'));
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

        $previousCategoryLimits = Category::withSum(['expenses' => function ($query) use ($user, $previousMonth) {
            $query->where('user_id', $user->id)->whereMonth('created_at', $previousMonth);
        }], 'amount')->get();

        $previousCategoryActualLimits = [];

        foreach ($previousCategoryLimits as $category) {
            $categoryTotalExpenses = $category->expenses_sum_amount ?? 0;
            $actualLimit = ($previousUserIncome && $previousUserIncome->monthly_income > 0)
                ? ($categoryTotalExpenses / $previousUserIncome->monthly_income) * 100
                : 0;

            $previousCategoryActualLimits[$category->id] = number_format($actualLimit, 2);
        }

        $categories = Category::all();

        return view('form_for_new_month', compact(
            'previousUserIncome', 'previousCategoryActualLimits', 'selectedMonth', 'categories'
        ));
    }

    public function hasCategories($user)
    {
        return UserCategory::where('user_id', $user->id)->exists();
    }

    public function getUserIncome($user)
    {
        return $user->userIncomes()->first();
    }

    public function hasUserCategoryForMonth($user, $selectedMonth)
    {
        return UserCategory::where('user_id', $user->id)
            ->whereMonth('create_date', $selectedMonth)
            ->exists();
    }

    public function getUserCategories($user, $selectedMonth)
    {
        return UserCategory::with('category')
            ->where('user_id', $user->id)
            ->whereMonth('create_date', $selectedMonth)
            ->get();
    }

    public function getUserIncomeForMonth($user, $selectedMonth)
    {
        return $user->userIncomes()
            ->where('month', $selectedMonth)
            ->where('year', now()->year)
            ->first();
    }

    public function calculateAmounts($user_categories, $Income)
    {
        $amount = [];
        foreach ($user_categories as $category) {
            $calculated_amount = $Income->monthly_income * $category->spending_percentage / 100;
            $amount[] = $calculated_amount;
        }
        return $amount;
    }

    public function getLimitPercentages($user_categories)
    {
        $limit_percentage = [];
        foreach ($user_categories as $category) {
            $limit_percentage[] = $category->spending_percentage;
        }
        return $limit_percentage;
    }

    public function getTotalSpendPerCategory($user, $selectedMonth)
    {
        return Category::withSum(['expenses' => function ($query) use ($user, $selectedMonth) {
            $query->where('user_id', $user->id)->whereMonth('created_at', $selectedMonth);
        }], 'amount')->get();
    }

    public function calculateRemainingAmount($Income, $totalSpendPerCategory)
    {
        $totalExpenses = $totalSpendPerCategory->sum('expenses_sum_amount');
        return $Income->monthly_income - $totalExpenses;
    }

    public function calculateActualLimits($totalSpendPerCategory, $Income)
    {
        $actualLimits = [];
        foreach ($totalSpendPerCategory as $category) {
            $categoryTotalExpenses = $category->expenses_sum_amount;
            $actualLimit = ($categoryTotalExpenses / $Income->monthly_income) * 100;
            $actualLimits[$category->id] = $actualLimit;
        }
        return $actualLimits;
    }

    public function calculateActualAmounts($actualLimits, $Income)
    {
        $actualAmounts = [];
        foreach ($actualLimits as $categoryId => $actualLimit) {
            $actualAmount = ($Income->monthly_income * $actualLimit) / 100;
            $actualAmounts[$categoryId] = $actualAmount;
        }
        return $actualAmounts;
    }

    public function getUnselectedCategories($user, $user_categories, $selectedMonth, $Income)
    {
        $unselectedCategories = [];
        $categories = Category::all();
        foreach ($categories as $category) {
            $userCategory = $user_categories->firstWhere('category_id', $category->id);
            if (!$userCategory) {
                $expenses = Expense::where('user_id', $user->id)
                    ->where('category_id', $category->id)
                    ->whereMonth('created_at', $selectedMonth)
                    ->sum('amount');

                $unselectedCategories[] = $this->prepareUnselectedCategory($category, $expenses, $Income);
            }
        }
        return $unselectedCategories;
    }

    public function prepareUnselectedCategory($category, $expenses, $Income)
    {
        if ($expenses > 0) {
            $actualLimit = ($expenses / $Income->monthly_income) * 100;
            return [
                'category_name' => $category->name,
                'amount' => 0,
                'limit' => 0,
                'actual_limit' => number_format($actualLimit, 2),
                'actual_amount' => $expenses,
            ];
        } else {
            return [
                'category_name' => $category->name,
                'amount' => 0,
                'limit' => 0,
                'actual_limit' => 0,
                'actual_amount' => 0,
            ];
        }
    }

    public function getMonths()
    {
        return [
            'January', 'February', 'March', 'April', 'May', 'June','July', 'August', 'September', 'October', 'November', 'December'
        ];
    }
}
