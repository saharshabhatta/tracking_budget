<?php

namespace App\Http\Middleware;

use App\Enums\Roles;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permissionSlug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        $user = Auth::user();
        $role = $user->getRole();

        if (!$role) {
            abort(403, 'Roles not assigned');
        }

        if ($role->name === 'super_admin') {
            return $next($request);
        }

        $permissionSlug = $request->route()->getName();
        $hasPermission = $role->hasPermission($permissionSlug, $role->id);

        if ($hasPermission) {
            return $next($request);
        }
        abort(403, 'Unauthorized');
    }
}
