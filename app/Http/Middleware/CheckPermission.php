<?php

namespace App\Http\Middleware;

use App\Enums\Role;
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
            abort(403, 'Role not assigned');
        }

        if ($role->name == Role::ADMIN->value) {
            return $next($request);
        }

        $hasPermission = $role->hasPermission($request->route()->getName(), $role->id);

        if ($hasPermission) {
            return $next($request);
        } else {
            abort(401);
        }
    }
}
