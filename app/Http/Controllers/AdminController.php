<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeCategoryNameRequest;
use App\Models\Category;
use App\Models\User;
use App\Models\UserIncome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Show the dashboard with various statistics.
     */
    public function showDashboard()
    {
        $averageIncome = UserIncome::avg('monthly_income');
        $totalUsers = User::count();

        $categoriesSpending = Category::withSum('expenses', 'amount')
            ->orderByDesc('expenses_sum_amount')
            ->take(5)
            ->get()
            ->map(fn($category) => [
                'category' => $category->name,
                'total_spent' => $category->expenses_sum_amount
            ]);

        $categoriesWithMostUsers = Category::withCount('users')
            ->orderByDesc('users_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('averageIncome', 'totalUsers', 'categoriesSpending', 'categoriesWithMostUsers'));
    }

    /**
     * Show all users.
     */
    public function showUsers()
    {
        $users = User::all();
        $totalUsers = User::count();
        return view('admin.users', compact('users', 'totalUsers'));
    }

    /**
     * Display the list of categories and their user count.
     */
    public function displayCategories()
    {
        $categories = DB::table('categories')
            ->select('categories.id', 'categories.name', 'categories.user_id', 'users.first_name as user_name', DB::raw('COUNT(DISTINCT user_categories.user_id) as user_count'))
            ->leftJoin('user_categories', 'categories.id', '=', 'user_categories.category_id')
            ->leftJoin('users', 'categories.user_id', '=', 'users.id')
            ->groupBy('categories.id', 'categories.name', 'categories.user_id', 'users.first_name')
            ->get();

        return view('admin.displayCategories', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function showCreateCategory()
    {
        return view('admin.createCategory');
    }

    /**
     * Create a new category.
     */
    public function createCategory(storeCategoryNameRequest $request)
    {
        Category::create([
            'name' => $request->input('name'),
        ]);

        return redirect('/admin/categories')->with('success', 'Category added successfully!');
    }

    /**
     * Show the categories of a specific user.
     */
    public function showUserCategories($userId)
    {
        $user = User::with('categories')->findOrFail($userId);
        return view('admin.showUserCategories', compact('user'));
    }

    /**
     * Delete a category.
     */
    public function destroyCategory(string $id)
    {
        Category::destroy($id);
        return redirect()->route('admin.categories')->with('success', 'Category deleted successfully!');
    }
}
