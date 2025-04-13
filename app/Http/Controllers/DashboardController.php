<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\UserCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        if (!$this->hasCategoriesForCurrentMonth($user, $currentMonth, $currentYear)) {
            return redirect()->route('register.categories');
        }

        $selectedMonthYear = $request->query('month');
        $selectedMonth = $selectedMonthYear ? (int) substr($selectedMonthYear, 5, 2) : Carbon::now()->month;
        $selectedYear = $selectedMonthYear ? (int) substr($selectedMonthYear, 0, 4) : Carbon::now()->year;

        $userIncome = $this->getUserIncomeForMonth($user, $selectedMonth, $selectedYear);
        [$user_categories, $isForecast] = $this->getUserCategories($user, $selectedMonth, $selectedYear);

        $amount = $this->calculateAmounts($user_categories, $userIncome);
        $limit_percentage = $this->getLimitPercentages($user_categories);

        if ($isForecast) {
            $actualLimits = [];
            $actualAmounts = [];
            foreach ($user_categories as $cat) {
                $categoryId = $cat->category->id ?? $cat->category_id;
                $actualLimits[$categoryId] = 0;
                $actualAmounts[$categoryId] = 0;
            }
            $totalSpendPerCategory = collect();
        } else {
            $totalSpendPerCategory = $this->getTotalSpendPerCategory($user, $selectedMonth, $selectedYear);
            $actualLimits = $this->calculateActualLimits($totalSpendPerCategory, $userIncome);
            $actualAmounts = $this->calculateActualAmounts($actualLimits, $userIncome);
        }

        $remainingAmount = $this->calculateRemainingAmount($userIncome, $totalSpendPerCategory);
        $months = $this->getMonths();

        return view('dashboard', compact(
            'user_categories',
            'amount',
            'totalSpendPerCategory',
            'remainingAmount',
            'months',
            'selectedMonth',
            'userIncome',
            'actualLimits',
            'actualAmounts',
            'limit_percentage',
            'isForecast',
            'selectedYear'
        ));
    }


    public function getUserIncomeForMonth($user, $selectedMonth, $selectedYear)
    {
        $income = $user->userIncomes()
            ->where('month', $selectedMonth)
            ->where('year', $selectedYear)
            ->sum('monthly_income');

        if (!$income) {
            if ($selectedMonth == 1) {
                $selectedMonth = 12;
                $selectedYear -= 1;
            } else {
                $selectedMonth -= 1;
            }

            $income = $user->userIncomes()
                ->where('month', $selectedMonth)
                ->where('year', $selectedYear)
                ->sum('monthly_income');
        }

        return $income;
    }

    public function getUserCategories($user, $selectedMonth, $selectedYear)
    {
        $categories = UserCategory::with('category')
            ->where('user_id', $user->id)
            ->whereMonth('create_date', $selectedMonth)
            ->whereYear('create_date', $selectedYear)
            ->get();

        if ($categories->isEmpty()) {
            $previousMonth = $selectedMonth == 1 ? 12 : $selectedMonth - 1;
            $previousYear = $selectedMonth == 1 ? $selectedYear - 1 : $selectedYear;

            $previousCategories = UserCategory::with('category')
                ->where('user_id', $user->id)
                ->whereMonth('create_date', $previousMonth)
                ->whereYear('create_date', $previousYear)
                ->get();

            $previousExpenses = $this->getTotalSpendPerCategory($user, $previousMonth, $previousYear);
            $previousIncome = $this->getUserIncomeForMonth($user, $previousMonth, $previousYear);
            $actualLimits = $this->calculateActualLimits($previousExpenses, $previousIncome);

            $forecastedCategories = $previousCategories->map(function ($cat) use ($actualLimits, $previousIncome) {
                $previousLimitPercentage = $cat->spending_percentage;
                $previousLimitAmount = ($previousIncome * $previousLimitPercentage) / 100;

                $actualSpentAmount = $actualLimits[$cat->category_id] ?? 0;
                $actualSpentAmount = ($actualSpentAmount * $previousIncome) / 100;

                $averageAmount = ($previousLimitAmount + $actualSpentAmount) / 2;

                $newPercentage = $previousIncome > 0 ? ($averageAmount / $previousIncome) * 100 : 0;

                $newCat = clone $cat;
                $newCat->spending_percentage = $newPercentage;

                return $newCat;
            });
            return [$forecastedCategories, true];
        }

        return [$categories, false];
    }

    public function getTotalSpendPerCategory($user, $selectedMonth, $selectedYear)
    {
        return Category::withSum(['expenses' => function ($query) use ($user, $selectedMonth, $selectedYear) {
            $query->where('user_id', $user->id)
                ->whereMonth('created_at', $selectedMonth)
                ->whereYear('created_at', $selectedYear);
        }], 'amount')
            ->whereHas('users', function ($query) use ($user, $selectedMonth, $selectedYear) {
                $query->where('user_id', $user->id)
                    ->whereMonth('create_date', $selectedMonth)
                    ->whereYear('create_date', $selectedYear);
            })
            ->get();
    }

    public function calculateAmounts($user_categories, $income)
    {
        $amount = [];
        foreach ($user_categories as $category) {
            $calculated_amount = $income * $category->spending_percentage / 100;
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

    public function calculateRemainingAmount($income, $totalSpendPerCategory)
    {
        $totalExpenses = $totalSpendPerCategory->sum('expenses_sum_amount');
        return $income - $totalExpenses;
    }

    public function calculateActualLimits($totalSpendPerCategory, $income)
    {
        $actualLimits = [];
        foreach ($totalSpendPerCategory as $category) {
            $categoryTotalExpenses = $category->expenses_sum_amount ?? 0;
            $actualLimit = ($income > 0)
                ? ($categoryTotalExpenses / $income) * 100
                : 0;
            $actualLimits[$category->id] = $actualLimit;
        }
        return $actualLimits;
    }

    public function calculateActualAmounts($actualLimits, $income)
    {
        $actualAmounts = [];
        foreach ($actualLimits as $categoryId => $actualLimit) {
            $actualAmount = ($income * $actualLimit) / 100;
            $actualAmounts[$categoryId] = $actualAmount;
        }
        return $actualAmounts;
    }

    public function hasCategoriesForCurrentMonth($user, $currentMonth, $currentYear)
    {
        return UserCategory::where('user_id', $user->id)
            ->whereMonth('create_date', $currentMonth)
            ->whereYear('create_date', $currentYear)
            ->exists();
    }

    public function getMonths()
    {
        return [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
    }


}
