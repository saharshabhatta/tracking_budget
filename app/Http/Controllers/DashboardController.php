<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\UserCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $hasCategories = UserCategory::where('user_id', $user->id)->exists();
        if (!$hasCategories) {
            return redirect()->route('register.categories');
        }

        if (!$user->monthly_income || !$user->annual_income) {
            return redirect()->route('register.incomes');
        }

        $user_categories = UserCategory::with('category')->where('user_id', $user->id)->get();

        $amount = [];

        foreach ($user_categories as $category) {
            $calculated_amount = $user->monthly_income * $category->spending_percentage / 100;
            $amount[] = $calculated_amount;
        }

        $expenses = Expense::with('category')->get();
        $categories = Category::all();

        $totalSpendPerCategory = Category::withSum('expenses', 'amount')->get();
        $totalExpenses = $totalSpendPerCategory->sum('expenses_sum_amount');
        $remainingAmount = $user->monthly_income - $totalExpenses;

        return view('dashboard', compact('user_categories', 'amount', 'totalSpendPerCategory', 'expenses', 'categories', 'remainingAmount'));
    }
}
