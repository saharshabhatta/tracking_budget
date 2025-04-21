<?php

namespace App\Providers;

use App\Models\Expense;
use App\Models\User;
use App\Models\UserIncome;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'income' => 'App\Models\UserIncome',
            'expense' => 'App\Models\Expense',
        ]);

        Gate::define('update-income', function (User $user, UserIncome $userIncome) {
            return $user->id === $userIncome->user_id;
        });

        Gate::define('update-expense', function (User $user, Expense $expense) {
            return $user->id === $expense->user_id;
        });
    }
}
