<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->roles->count() > 1) {
            session(['available_roles' => $user->roles]);
            return redirect()->route('select.role');
        }

        $role = $user->roles->first();
        session(['active_role' => $role->name]);

        if ($role->name === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($role->name === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }

        // If it's any other role, redirect to the intended route
        return redirect()->intended(route('dashboard', absolute: false));
    }



    public function selectRole()
    {
        $roles = session('available_roles');

        return view('auth.select-role', compact('roles'));
    }

    public function chooseRole(Request $request)
    {
        $roleId = $request->input('role_id');
        $role = Role::findOrFail($roleId);

        session(['active_role' => $role->name]);

        if ($role->name === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
