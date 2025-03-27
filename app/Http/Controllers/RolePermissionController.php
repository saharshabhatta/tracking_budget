<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolePermissionRequest;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionController extends Controller
{
    public function index(){
        $roles = Role::all();
        $permissions = Permission::all();
        return view('admin.permissions', compact('roles', 'permissions'));
    }

    public function update(RolePermissionRequest $request){

        $role = Role::findOrFail($request->role_id);

        $role->permissions()->sync($request->input('permissions',[]));

        return redirect()->back()->with('success', 'Permissions updated successfully!');
    }
}
