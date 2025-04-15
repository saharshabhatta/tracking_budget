<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = Carbon::now();

        $adminPermissions = range(29, 50);

        foreach ($adminPermissions as $permissionId) {
            DB::table('permission_role')->updateOrInsert(
                ['role_id' => 2, 'permission_id' => $permissionId],
                ['created_at' => $timestamp, 'updated_at' => $timestamp]
            );
        }

        // User permissions (example: permissions 11 to 18 based on your screenshot)
        $userPermissions = range(1, 27);

        foreach ($userPermissions as $permissionId) {
            DB::table('permission_role')->updateOrInsert(
                ['role_id' => 3, 'permission_id' => $permissionId],
                ['created_at' => $timestamp, 'updated_at' => $timestamp]
            );
        }
    }
}
