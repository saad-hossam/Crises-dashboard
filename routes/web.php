<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Language switching (public)
|--------------------------------------------------------------------------
*/
Route::get('/language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch');

/*
|--------------------------------------------------------------------------
| Guest routes (unauthenticated only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::prefix('admin')->name('admin.')->group(function () {

        // Dashboard — live counts via DashboardController
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Users
        Route::resource('users', UserController::class);

        // Roles
        Route::resource('roles', RoleController::class);

        // Permissions — full CRUD
        Route::resource('permissions', PermissionController::class);

        // Units (master data)
        Route::resource('units', UnitController::class);

       
    });
});

/*
|--------------------------------------------------------------------------
| Root redirect
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));