<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@gmail.com',
            'phone' => '9800000000',
            'password' => \Hash::make('superadmin')
        ]);

        $superAdminRole = Role::where('name', 'super_admin')->first();

        if ($superAdminRole) {
            $superAdmin->roles()->attach($superAdminRole->id);
        }

        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@gmail.com',
            'phone' => '9841351767',
            'password' => \Hash::make('admin')
        ]);

        $adminRole = Role::where('name', 'admin')->first();

        if ($adminRole) {
            $admin->roles()->attach($adminRole->id);
        }

        $user = User::create([
            'first_name' => 'User',
            'last_name' => 'User',
            'email' => 'user@gmail.com',
            'phone' => '9812345678',
            'password' => \Hash::make('user')
        ]);

        $userRole = Role::where('name', 'user')->first();

        if ($userRole) {
            $user->roles()->attach($userRole->id);
        }
    }
}
