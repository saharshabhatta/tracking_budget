<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Http\Requests\MonthRequest;
use App\Http\Requests\storeCategoryNameRequest;
use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use App\Models\UserIncome;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function showDashboard(){
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

    public function showUsers(Request $request)
    {
        $query = User::query();
        $totalUsers = User::count();

        if ($request->has('search') && $request->search != '') {
            $query->where(function ($subQuery) use ($request) {
                $subQuery->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->paginate(10)->appends($request->all());

        return view('admin.users', compact('users', 'totalUsers'));
    }

//    public function search(Request $request)
//    {
//        $query = User::query();
//        $totalUsers = User::count();
//
//        if ($request->has('search') && $request->search != '') {
//            $query->where(function ($subQuery) use ($request) {
//                $subQuery->where('first_name', 'like', '%' . $request->search . '%')
//                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
//                    ->orWhere('email', 'like', '%' . $request->search . '%')
//                    ->orWhere('phone', 'like', '%' . $request->search . '%');
//            });
//        }
//
//        $users = $query->paginate(10)->appends($request->all());
//        return view('admin.users', compact('users', 'totalUsers'));
//    }

    public function displayCategories(FilterRequest $request)
    {
        $searchTerm = $request->input('search', '');

        $categories = DB::table('categories')
            ->select(
                'categories.id',
                'categories.name',
                'categories.user_id',
                'categories.created_at',
                'users.first_name as user_name',
                DB::raw('COUNT(DISTINCT user_categories.user_id) as user_count')
            )
            ->leftJoin('user_categories', 'categories.id', '=', 'user_categories.category_id')
            ->leftJoin('users', 'categories.user_id', '=', 'users.id')
            ->whereNull('categories.deleted_at')
            ->groupBy('categories.id', 'categories.name', 'categories.user_id', 'categories.created_at', 'users.first_name');

        if ($searchTerm) {
            $categories->where(function ($query) use ($searchTerm) {
                $query->where('categories.name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('users.first_name', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->has('from') && $request->has('to') && $request->from && $request->to) {
            $from = $request->input('from') . ' 00:00:00';
            $to = $request->input('to') . ' 23:59:59';

            $categories->whereBetween('categories.created_at', [$from, $to]);
        }

        $categories = $categories->paginate(5)->withQueryString();

        return view('admin.displayCategories', compact('categories', 'searchTerm'));
    }


    public function showCreateCategory(){
        return view('admin.createCategory');
    }

    public function createCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        DB::beginTransaction();

        try {
            $category = Category::create([
                'name' => $request->input('name'),
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            if ($category && $category->trashed()) {
                $category->restore();
            }

            return redirect('/admin/categories');
        } catch (Exception $e) {
            DB::rollBack();
//            dd($e->getMessage());
            return back()->withErrors(['error' => 'Category creation failed, please try again.']);
        }
    }

    public function showUserCategories(MonthRequest $request, $userId)
    {
        $searchTerm = $request->get('search', '');
        $selectedMonth = $request->get('month', now()->format('Y-m'));

        $user = User::findOrFail($userId);

        $categories = DB::table('user_categories')
            ->join('categories', 'user_categories.category_id', '=', 'categories.id')
            ->where('user_categories.user_id', $userId)
            ->when($searchTerm, function ($query) use ($searchTerm) {
                return $query->where('categories.name', 'like', '%' . $searchTerm . '%');
            })
            ->whereMonth('user_categories.create_date', Carbon::parse($selectedMonth)->month)
            ->whereYear('user_categories.create_date', Carbon::parse($selectedMonth)->year)
            ->orderBy('user_categories.create_date', 'desc')
            ->select('categories.*', 'user_categories.spending_percentage', 'user_categories.create_date')
            ->paginate(10)
            ->withQueryString();

        return view('admin.showUserCategories', compact('categories', 'selectedMonth', 'searchTerm', 'user'));
    }

    public function editCategory($id) {
        $category = Category::findOrFail($id);

        $userCount = $category->users()->count();

        return view('admin.editCategory', compact('category', 'userCount'));
    }

    public function updateCategory(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        DB::beginTransaction();

        try {
            $category = Category::withTrashed()->findOrFail($id);

            if ($category->trashed()) {
                $category->restore();
            }

            $category->name = $validated['name'];

            $category->save();

            DB::commit();

            return redirect()->route('admin.categories')->with('success', 'Category updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.categories')->with('error', 'Something went wrong while updating the category.');
        }
    }

    public function destroyCategory(string $id) {
        try {
            $category = Category::findOrFail($id);
            $category->delete();
            return redirect()->route('admin.categories')->with('success', 'Category deleted successfully.');
        } catch (Exception $e) {
            return redirect()->route('admin.categories')->with('error', 'Something went wrong while deleting the category.');
        }
    }
}
