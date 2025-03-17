<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use App\Models\Expense;
use App\Models\UserIncome;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function showDashboard(){
        $average = UserIncome::avg('monthly_income');

        $totalUsers = User::count();

        $categoriesSpending = Category::withSum('expenses', 'amount')
        ->orderByDesc('expenses_sum_amount')
        ->take(5)
        ->get()
        ->map(function($category) {
            return [
                'category' => $category->name,
                'total_spent' => $category->expenses_sum_amount
            ];
        });

        $categoriesWithMostUsers = Category::withCount('users')
            ->orderByDesc('users_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('average', 'totalUsers', 'categoriesSpending', 'categoriesWithMostUsers'));
    }

    public function showUsers(){
        $users = User::all();
        $totalUsers = User::count();
        return view('admin.users', compact('users', 'totalUsers'));
    }

    public function displayCategories(){
        $categories = Category::with(['user'])->withCount('users')->get();
        return view('admin.displayCategories', compact('categories'));
    }

    public function showCreateCategory(){
        return view('admin.createCategory');
    }

    public function createCategory(Request $request){
        $request->validate([
            'name' => 'required|unique:categories,name|max:255',
        ]);

        $category = Category::create([
            'name' => $request->input('name'),
        ]);

        return redirect('/admin/categories')->with('success', 'Category added successfully!');
    }

    public function showUserCategories($userId){
        $user = User::with('categories')->find($userId);

        return view('admin.showUserCategories', compact('user'));
    }

    public function destroyCategory(string $id)
    {
        $category=Category::destroy($id);
        return redirect('admin.displayCategories')->with('success', 'Category deleted successfully!');
    }
}
