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
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'Testing',
            'email' => 'admin@gmail.com',
            'phone' => '9841351767',
            'password' => Hash::make('123456789'),
            'role' => 'user',
        ]);

        $role = Role::where('name', 'user')->first();

        if ($role) {
            $user->roles()->attach($role->id);
        }

    }
}

