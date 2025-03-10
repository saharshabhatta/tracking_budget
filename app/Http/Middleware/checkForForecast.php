<?php

namespace App\Http\Middleware;

use App\Models\UserCategory;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class checkForForecast
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        $hasCategories = UserCategory::where('user_id', Auth::id())->exists();
        if (!$hasCategories) {
            return redirect()->route('register.categories');
        }

        $selectedMonth = $request->query('month', Carbon::now()->month);

        $userIncome = $user->userIncomes()
            ->where('month', $selectedMonth)
            ->where('year', Carbon::now()->year)
            ->first();

        if (!$userIncome) {
            return redirect()->route('register.incomes');
        }

        return $next($request);
    }
}
