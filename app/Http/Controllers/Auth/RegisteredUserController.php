<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\storeCategoryNameRequest;
use App\Http\Requests\storeIncomeRequest;
use App\Models\Category;
use App\Models\Role;
use App\Models\Statement;
use App\Models\User;
use App\Models\UserCategory;
use App\Models\UserIncome;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $roles = Role::all();
        return view('auth.register', compact('roles'));
    }

    public function storePersonalInfo(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'numeric', 'digits:10'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            $defaultRole = Role::where('name', 'user')->first();
            if ($defaultRole) {
                $user->roles()->attach($defaultRole->id);
            }

            event(new Registered($user));

            Auth::login($user);

            DB::commit();
            return redirect()->route('register.categories');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Registration failed, please try again.']);
        }
    }


    public function showCategories()
    {
        if (!Auth::check()) {
            return redirect()->route('register')->withErrors(['error' => 'Please register first.']);
        }

        $categories = Category::whereHas('user', function ($query) {
            $query->whereHas('roles', function ($query) {
                $query->where('name', Roles::ADMIN);
            });
        })
        ->get();
        return view('auth.register-categories', compact('categories'));
    }

    public function storeCategories(storeCategoryNameRequest $request)
    {
        $user = Auth::user();

        DB::beginTransaction();

        try {
            if ($request->new_category) {
                $existingCategory = Category::whereRaw('LOWER(name) = ?', [strtolower($request->new_category)])->first();

                if ($existingCategory) {
                    $categoryIdToLink = $existingCategory->id;
                } else {
                    $newCategory = Category::create([
                        'name' => $request->new_category,
                        'user_id' => $user->id,
                    ]);
                    $categoryIdToLink = $newCategory->id;
                }

                $request->merge([
                    'categories' => array_merge($request->categories ?? [], [$categoryIdToLink])
                ]);
            }

            foreach ($request->categories as $categoryId) {
                UserCategory::firstOrCreate([
                    'user_id' => $user->id,
                    'category_id' => $categoryId,
                ], [
                    'spending_percentage' => 0
                ]);
            }

            DB::commit();
            return redirect()->route('register.incomes');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to store categories, please try again.']);
        }
    }

    public function showIncome()
    {
        $user = Auth::user();

        if (!Auth::check()) {
            return redirect()->route('register')->withErrors(['error' => 'Please register first.']);
        }

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

        DB::beginTransaction();

        try {
            $totalPercentage = array_sum($request->category_percentages);

            if ($totalPercentage > 100) {
                return back()->withErrors(['error' => 'The total category percentages cannot exceed 100%.']);
            }

            $monthlyIncome = $request->income_type === 'monthly'
                ? $request->monthly_income
                : round($request->annual_income / 12, 2);

            $annualIncome = $request->income_type === 'annual'
                ? $request->annual_income
                : round($request->monthly_income * 12, 2);

            $userIncome = UserIncome::create([
                'user_id' => $user->id,
                'month' => $currentMonth,
                'year' => $currentYear,
                'monthly_income' => $monthlyIncome,
                'annual_income' => $annualIncome,
            ]);

            $statement = new Statement();
            $statement->statementable()->associate($userIncome);
            $statement->save();

            foreach ($request->category_percentages as $categoryId => $percentage) {
                UserCategory::where('user_id', $user->id)
                    ->where('category_id', $categoryId)
                    ->update(['spending_percentage' => $percentage]);
            }

            DB::commit();
            return redirect()->route('dashboard');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to store income data, please try again.']);
        }
    }

    public function finalizeRegistration()
    {
        return redirect(route('dashboard'));
    }
}
