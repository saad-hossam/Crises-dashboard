<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProductionLineController;
use App\Http\Controllers\Admin\ProductionLinePreviewController;
use App\Http\Controllers\Admin\ProductionLineExcelController;
use App\Http\Controllers\Admin\ProductionLineImportController;
use App\Http\Controllers\Admin\ImportBatchController;
use App\Http\Controllers\Admin\ExcelModuleController;

Route::get('/language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
        Route::resource('units', UnitController::class);

        // Production Lines (legacy)
        Route::get('/production-lines', [ProductionLineController::class, 'index'])
            ->name('production-lines.index');
        Route::post('/production-lines/upload-excel', [ProductionLineExcelController::class, 'store'])
            ->name('production-lines.upload-excel');
        Route::get('/production-lines/preview', [ProductionLinePreviewController::class, 'index'])
            ->name('production-lines.preview');
        Route::post('/production-lines/import', [ProductionLineImportController::class, 'store'])
            ->name('production-lines.import');
        Route::get('/production-lines/{productionLine}', [ProductionLineController::class, 'show'])
            ->name('production-lines.show');

        Route::get('/import-batches', [ImportBatchController::class, 'index'])
            ->name('import-batches.index');
        Route::get('/import-batches/{importBatch}', [ImportBatchController::class, 'show'])
            ->name('import-batches.show');

        /*
        |--------------------------------------------------------------------------
        | Dynamic Excel Modules
        |--------------------------------------------------------------------------
        | مهم: المسارات الثابتة قبل {module}
        */
        Route::prefix('excel-modules')->name('excel-modules.')->group(function () {
            Route::get('/dashboard', [ExcelModuleController::class, 'dashboard'])->name('dashboard');
            Route::get('/', [ExcelModuleController::class, 'index'])->name('index');
            Route::get('/upload', [ExcelModuleController::class, 'create'])->name('create');
            // جداول مخصصة لكل موديول
            Route::get('/table/{moduleCode}', [ExcelModuleController::class, 'showTable'])->name('table');
            Route::get('/table/{moduleCode}/{id}', [ExcelModuleController::class, 'showTableRecord'])->name('table.record');
            // POST = رفع الملف وتحليلها | GET = عرض المعاينة من الـ session (بعد الرفع أو عند Refresh)
            Route::match(['get', 'post'], '/preview', [ExcelModuleController::class, 'preview'])->name('preview');
            Route::post('/import', [ExcelModuleController::class, 'import'])->name('import');

            Route::get('/{module}/export', [ExcelModuleController::class, 'export'])
                ->whereNumber('module')
                ->name('export');

            Route::get('/{module}/records/{record}', [ExcelModuleController::class, 'showRecord'])
                ->whereNumber('module')
                ->whereNumber('record')
                ->name('records.show');

            Route::delete('/{module}', [ExcelModuleController::class, 'destroy'])
                ->whereNumber('module')
                ->name('destroy');

            Route::get('/{module}', [ExcelModuleController::class, 'show'])
                ->whereNumber('module')
                ->name('show');
        });
    });
});

Route::get('/', fn () => redirect()->route('login'));
