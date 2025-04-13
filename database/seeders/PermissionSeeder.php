<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $routes = collect(Route::getRoutes())
            ->map(function ($route) {
                $folder = explode('/', $route->uri())[0];
                return [
                    'uri' => $route->uri(),
                    'name' => $route->getName(),
                    'folder' => $folder,
                ];
            })
            ->groupBy('folder');

        // List of routes to skip
        $excludeRoutes = [
            'login',
            'forgot-password',
            'reset-password',
            'verify-email',
            'verify-email/{id}/{hash}',
            'storage/{path}',
            'up',
            '/',
            'register',
            'reset-password/{token}',
            'confirm-password',
            'dashboard',
            'logout',
            'register.categories',
            'register.store-categories',
            'register.incomes',
            'register.store-incomes',
            'register.forecast',
            'register.finalize',
            'select.role',
            'choose.role',
            'password.request',
            'password.email',
            'password.reset',
            'password.store',
            'verification.notice',
            'verification.verify',
            'verification.send',
            'password.confirm',
            'password.update',
        ];

        foreach ($routes as $folder => $folderRoute) {
            foreach ($folderRoute as $route) {
                if (collect($excludeRoutes)->contains(fn($exclude) => Str::is($exclude, $route['name']))) {
                    continue;
                }

                if (!$route['name']) {
                    continue;
                }

                $permissionName = $this->formatPermissionName($route['name']);

                Permission::firstOrCreate([
                    'name' => $route['name'],
                    'uri' => $route['uri'],
                    'group' => $folder,
                    'slug' => $permissionName,
                ]);
            }
        }
    }


    /**
     * Format the permission name for better readability.
     */
    private function formatPermissionName($name)
    {
        return ucwords(str_replace('.', ' ', $name));
    }
}
