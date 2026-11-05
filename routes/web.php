
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Language switching
|--------------------------------------------------------------------------
*/

Route::get('/language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch');

/*
|--------------------------------------------------------------------------
| Guest routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login']);

});

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    Route::prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Users
        Route::resource('users', UserController::class);

        // Roles
        Route::resource('roles', RoleController::class);

        // Permissions
        Route::resource('permissions', PermissionController::class);

        // Units
        Route::resource('units', UnitController::class);

    });

});

/*
|--------------------------------------------------------------------------
| Root redirect
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('login'));
