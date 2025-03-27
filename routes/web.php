<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckPermission;
use App\Http\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;

// Default Route
Route::get('/', function () {
    return redirect()->route('login');
});

// Routes requiring authentication and specific permissions
Route::middleware(['auth', 'permission:dashboard'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->middleware('forecast')->name('dashboard');
    Route::get('/dashboard/form-for-new-month/{month}', [DashboardController::class, 'showFormForNewMonth'])->name('dashboard.formForNewMonth');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::resource('categories', CategoryController::class)->middleware('forecast');
    Route::resource('expenses', ExpenseController::class)->middleware('forecast');
});

// Routes for registration-related processes with permissions
Route::middleware(['permission:register'])->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register/personal-info', [RegisteredUserController::class, 'storePersonalInfo'])->name('register.personal-info');
    Route::get('register/categories', [RegisteredUserController::class, 'showCategories'])->name('register.categories');
    Route::post('register/categories', [RegisteredUserController::class, 'storeCategories'])->name('register.store-categories');
    Route::get('register/incomes', [RegisteredUserController::class, 'showIncome'])->name('register.incomes');
    Route::post('register/incomes', [RegisteredUserController::class, 'storeIncome'])->name('register.store-incomes');
    Route::get('register/forecast', [RegisteredUserController::class, 'showForecast'])->name('register.forecast');
    Route::post('register/finalize', [RegisteredUserController::class, 'finalizeRegistration'])->name('register.finalize');
});

// Admin-related routes with permission middleware and admin middleware
Route::middleware([AdminMiddleware::class, 'permission:admin'])->group(function () {
    Route::get('admin/users', [AdminController::class, 'showUsers'])->name('admin.users');
    Route::get('/admin/user/{userId}/categories', [AdminController::class, 'showUserCategories']);
    Route::get('admin/categories', [AdminController::class, 'displayCategories'])->name('admin.categories');
    Route::post('admin/categories/create', [AdminController::class, 'showCreateCategory'])->name('admin.createCategory');
    Route::post('admin/categories/show', [AdminController::class, 'createCategory'])->name('admin.storeCategory');
    Route::get('admin/dashboard', [AdminController::class, 'showDashboard'])->name('admin.dashboard');
    Route::delete('/admin/category/{id}', [AdminController::class, 'destroyCategory'])->name('admin.category.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/assign-permissions', [RolePermissionController::class, 'index'])->name('admin.assign-permissions');
    Route::post('/admin/assign-permissions', [RolePermissionController::class, 'update'])->name('admin.assign-permissions.update');
});

Route::get('/roles', [RoleController::class, 'index']);
Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
Route::put('/users/{user}/make-admin', [RoleController::class, 'updateRoleToAdmin'])->name('roles.updateToAdmin');
Route::put('/users/{user}/make-user', [RoleController::class, 'updateRoleToUser'])->name('roles.updateToUser');
Route::put('/users/{user}/roles', [RoleController::class, 'updateRole'])->name('roles.update');

require __DIR__.'/auth.php';
