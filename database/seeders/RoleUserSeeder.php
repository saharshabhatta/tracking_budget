<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('role_user')->insert([
            'user_id'    => 1,
            'role_id'    => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('role_user')->insert([
            'user_id'    => 2,
            'role_id'    => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('role_user')->insert([
            'user_id'    => 3,
            'role_id'    => 3,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
