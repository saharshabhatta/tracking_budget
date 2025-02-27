<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\UserCategory;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(){
        $user=Auth::user();

        $user_categories = UserCategory::with('category')->where('user_id', $user->id)->get();

        $amount=[];

        foreach ($user_categories as $category) {
            $calculated_amount = $user->monthly_income * $category->spending_percentage / 100;
            $amount[] = $calculated_amount;
        }

        $expenses = Expense::with('category')->get();

    }
}
