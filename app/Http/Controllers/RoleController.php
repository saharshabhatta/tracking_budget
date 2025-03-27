<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    public function show(Role $role)
    {
        $users = $role->users;
        $roles = Role::all();
        return view('roles.show', compact('role', 'users', 'roles'));
    }



    public function updateRole(Request $request, User $user)
    {
        $role = Role::find($request->role_id);

        if ($role) {
            $user->roles()->sync([$role->id]);
            return redirect()->back()->with('success', 'User role updated.');
        }

        return redirect()->back()->with('error', 'Role update failed.');
    }


    public function updateRoleToAdmin(Request $request, User $user)
    {
        if ($user->hasRole('user')) {
            $adminRole = Role::where('name', 'admin')->first();

            if ($adminRole) {
                $user->roles()->sync([$adminRole->id]);
                return redirect()->back()->with('success', 'User role updated to admin.');
            }
        }

        return redirect()->back()->with('error', 'Role update failed.');
    }

    public function updateRoleToUser(Request $request, User $user)
    {
        if ($user->hasRole('admin')) {
            $userRole = Role::where('name', 'user')->first();

            if ($userRole) {
                $user->roles()->sync([$userRole->id]);
                return redirect()->back()->with('success', 'User role updated to user.');
            }
        }

        return redirect()->back()->with('error', 'Role update failed.');
    }
}
