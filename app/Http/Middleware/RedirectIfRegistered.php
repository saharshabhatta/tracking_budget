<?php

namespace App\Http\Middleware;

use App\Models\UserCategory;
use App\Models\UserIncome;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfRegistered
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->hasRole('admin')) {
                abort(403, 'Access denied for admin users.');
            }

            $hasIncome = UserIncome::where('user_id', $user->id)->exists();
            $hasCategory = UserCategory::where('user_id', $user->id)->exists();

            if ($hasIncome && $hasCategory) {
                return redirect()->route('dashboard');
            }
        }
        return $next($request);
    }

}
