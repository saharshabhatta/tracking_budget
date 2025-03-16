<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\storeCategoryNameRequest;
use App\Http\Requests\storeIncomeRequest;
use App\Models\Category;
use App\Models\Statement;
use App\Models\User;
use App\Models\UserCategory;
use App\Models\UserIncome;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function storePersonalInfo(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:15'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? Role::USER,
        ]);

        event(new Registered($user));
        Auth::login($user);
        return redirect()->route('register.categories');
    }

    public function showCategories()
    {
        $categories = Category::all();
        return view('auth.register-categories', compact('categories'));
    }

    public function storeCategories(storeCategoryNameRequest $request)
    {
        $user = Auth::user();

        if ($request->new_category) {
            $newCategory = Category::create(['name' => $request->new_category]);
            $request->merge(['categories' => array_merge($request->categories, [$newCategory->id])]);
        }

        foreach ($request->categories as $categoryId) {
            UserCategory::updateOrCreate(
                ['user_id' => $user->id, 'category_id' => $categoryId],
                ['spending_percentage' => 0]
            );
        }
        return redirect()->route('register.incomes');
    }

    public function showIncome()
    {
        $user = Auth::user();

        $categories = UserCategory::where('user_id', $user->id)
            ->with('category')
            ->get();

        return view('auth.register-income', compact('categories'));
    }

    public function storeIncome(storeIncomeRequest $request)
    {
        $user = Auth::user();

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $userIncome = UserIncome::updateOrCreate(
            ['user_id' => $user->id, 'month' => $currentMonth, 'year' => $currentYear],
            ['monthly_income' => $request->monthly_income, 'annual_income' => $request->annual_income]
        );

        $statement = new Statement();
        $statement->statementable()->associate($userIncome); //ststement lai userincome sanga link garana ko lagi
        $statement->save();


        foreach ($request->category_percentages as $categoryId => $percentage) {
            UserCategory::where('user_id', $user->id)
                ->where('category_id', $categoryId)
                ->update(['spending_percentage' => $percentage]);
        }

        return redirect()->route('dashboard');
    }

    public function finalizeRegistration()
    {
        return redirect(route('dashboard'));
    }
}
