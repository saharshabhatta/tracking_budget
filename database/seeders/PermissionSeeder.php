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

        foreach ($routes as $folder => $folderRoute) {
            foreach ($folderRoute as $route) {
                if (in_array($route['uri'], [
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
                ])) {
                    continue;
                }

                if (!$route['name']) {
                    continue;
                }


                Permission::firstOrCreate([
                    'name' => $route['name'],
                    'uri' => $route['uri'],
                    'group' => $folder,
                    'slug' => Str::slug(str_replace('.', ' ', $route['name'])),
                ]);
            }
        }
    }
}
