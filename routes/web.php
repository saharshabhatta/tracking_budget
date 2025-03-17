<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\checkForForecast;
use App\Http\Middleware\UserMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('dashboard', [DashboardController::class, 'index'])->middleware('auth', 'forecast')->name('dashboard');
Route::get('/dashboard/form-for-new-month/{month}', [DashboardController::class, 'showFormForNewMonth'])->name('dashboard.formForNewMonth');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register/personal-info', [RegisteredUserController::class, 'storePersonalInfo'])->name('register.personal-info');
Route::get('register/categories', [RegisteredUserController::class, 'showCategories'])->name('register.categories');
Route::post('register/categories', [RegisteredUserController::class, 'storeCategories'])->name('register.store-categories');
Route::get('register/incomes', [RegisteredUserController::class, 'showIncome'])->name('register.incomes');
Route::post('register/incomes', [RegisteredUserController::class, 'storeIncome'])->name('register.store-incomes');
Route::get('register/forecast', [RegisteredUserController::class, 'showForecast'])->name('register.forecast');
Route::post('register/finalize', [RegisteredUserController::class, 'finalizeRegistration'])->name('register.finalize');

Route::resource('categories', CategoryController::class)->middleware('forecast');
Route::resource('expenses', ExpenseController::class)->middleware('forecast');


Route::middleware([AdminMiddleware::class])->group(function () {
    Route::get('admin/users', [AdminController::class, 'showUsers'])->name('admin.users');

    Route::get('/admin/user/{userId}/categories', [AdminController::class, 'showUserCategories']);

    Route::get('admin/categories', [AdminController::class, 'displayCategories'])->name('admin.categories');

    Route::post('admin/categories/create', [AdminController::class, 'showCreateCategory'])->name('admin.createCategory');

    Route::post('admin/categories/show', [AdminController::class, 'createCategory'])->name('admin.storeCategory');

    Route::get('admin/dashboard', [AdminController::class, 'showDashboard'])->name('admin.dashboard');
});
require __DIR__.'/auth.php';
