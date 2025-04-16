<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('name', '!=', 'super_admin')->paginate(10);
        return view('roles.index', compact('roles'));
    }

    public function search(Request $request)
    {
        $query = Role::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $roles = $query->paginate(10);

        return view('roles.index', compact('roles'));
    }

    public function show(Request $request, Role $role)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            return $query->where('first_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })->paginate(10);

        return view('roles.show', compact('role', 'users'));
    }



    /**
     * Update role for the user and handle errors with transactions.
     */
    public function update(Request $request, User $user){
        DB::beginTransaction();

        try {
            $role = Role::findOrFail($request->role_id);
            $user->roles()->attach($role);

            DB::commit();
            return redirect()->route('roles.show', $role->id);
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update user role, please try again.']);
        }
    }

    /**
     * Remove role from the user and handle errors with transactions.
     */
    public function updateRemove(Request $request, User $user){
        DB::beginTransaction();

        try {
            $role = Role::findOrFail($request->role_id);
            $user->roles()->detach($role);

            DB::commit();
            return redirect()->route('roles.show', $role->id);
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to remove user role, please try again.']);
        }
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(){
        return view('roles.create');
    }

    /**
     * Store a newly created role in storage with transaction and error handling.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        DB::beginTransaction();

        try {
            Role::create([
                'name' => $validated['name'],
            ]);

            DB::commit();

            return redirect()->route('roles.index')->with('success', 'Role created successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create role, please try again.']);
        }
    }

}
