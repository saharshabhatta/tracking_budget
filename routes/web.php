<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'forecast'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth','permission:user', 'forecast'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('categories', CategoryController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::post('expenses.filter', [ExpenseController::class, 'filter'])->name('expenses.filter');

    Route::resource('incomes', IncomeController::class);
    Route::post('incomes.filter', [IncomeController::class, 'filter'])->name('incomes.filter');

    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
});

Route::middleware(['registered'])->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register/personal-info', [RegisteredUserController::class, 'storePersonalInfo'])->name('register.personal-info');
    Route::get('register/categories', [RegisteredUserController::class, 'showCategories'])->name('register.categories');
    Route::post('register/categories', [RegisteredUserController::class, 'storeCategories'])->name('register.store-categories');
    Route::get('register/incomes', [RegisteredUserController::class, 'showIncome'])->name('register.incomes');
    Route::post('register/incomes', [RegisteredUserController::class, 'storeIncome'])->name('register.store-incomes');
    Route::get('register/forecast', [RegisteredUserController::class, 'showForecast'])->name('register.forecast');
    Route::post('register/finalize', [RegisteredUserController::class, 'finalizeRegistration'])->name('register.finalize');
});

Route::middleware(['permission:admin'])->group(function () {
    Route::get('admin/users', [AdminController::class, 'showUsers'])->name('admin.users');
    Route::get('/admin/user/{userId}/categories', [AdminController::class, 'showUserCategories'])->name('admin.userCategories');
    Route::get('admin/categories', [AdminController::class, 'displayCategories'])->name('admin.categories');
    Route::get('admin/categories/create', [AdminController::class, 'showCreateCategory'])->name('admin.createCategory');
    Route::post('admin/categories/show', [AdminController::class, 'createCategory'])->name('admin.storeCategory');
    Route::get('admin/dashboard', [AdminController::class, 'showDashboard'])->name('admin.dashboard');
    Route::delete('/admin/category/{id}', [AdminController::class, 'destroyCategory'])->name('admin.category.destroy');
    Route::get('admin/categories/{id}/edit', [AdminController::class, 'editCategory'])->name('admin.editCategory');
    Route::put('admin/categories/{id}', [AdminController::class, 'updateCategory'])->name('admin.updateCategory');
    Route::delete('/admin/categories/{id}', [AdminController::class, 'destroyCategory'])->name('admin.destroyCategory');
    Route::post('users.search', [AdminController::class, 'search'])->name('users.search');
    Route::post('user.searchUserCategories', [AdminController::class, 'searchUserCategory'])->name('admin.searchUserCategories');
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::post('/roles/store', [RoleController::class, 'store'])->name('roles.store');
    Route::put('roles/{user}/assign', [RoleController::class, 'update'])->name('roles.update');
    Route::put('roles/{user}/remove', [RoleController::class, 'updateRemove'])->name('roles.updateRemove');
    Route::post('roles.search', [RoleController::class, 'search'])->name('roles.search');
    Route::get('/admin/assign-permissions', [RolePermissionController::class, 'index'])->name('admin.assign-permissions');
    Route::post('/admin/assign-permissions', [RolePermissionController::class, 'update'])->name('admin.assign-permissions.update');
    Route::post('permissions.search', [RolePermissionController::class, 'search'])->name('permissions.search');
});

Route::middleware(['auth'])->group(function () {
//    Route::get('/admin/assign-permissions', [RolePermissionController::class, 'index'])->name('admin.assign-permissions');
//    Route::post('/admin/assign-permissions', [RolePermissionController::class, 'update'])->name('admin.assign-permissions.update');
//    Route::post('permissions.search', [RolePermissionController::class, 'search'])->name('permissions.search');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/select-role', [AuthenticatedSessionController::class, 'selectRole'])->name('select.role');
    Route::post('/select-role', [AuthenticatedSessionController::class, 'chooseRole'])->name('choose.role');
});

require __DIR__.'/auth.php';
