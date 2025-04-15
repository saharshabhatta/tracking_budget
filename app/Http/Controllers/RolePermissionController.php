<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolePermissionRequest;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::where('name', '!=', 'Super_admin')->get();
        $permissions = Permission::all();

        return view('admin.permissions', compact('roles', 'permissions'));
    }

    /**
     * Update the permissions for a given role.
     */
    public function update(Request $request)
    {
//        dd('Update function triggered');
        DB::beginTransaction();

        try {
            foreach ($request->input('role_ids', []) as $role_id) {
                $role = Role::findOrFail($role_id);

                $permissions = $request->input("permissions.{$role_id}", []);

                $role->permissions()->sync($permissions);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Permissions updated successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update permissions, please try again.']);
        }
    }


    public function search(Request $request){
        $query = Permission::query();
        $roles = Role::all();
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($subQuery) use ($request) {
                $subQuery->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $permissions = $query->get();
        return view('admin.permissions', compact('permissions', 'roles'));
    }
}
