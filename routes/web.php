<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\ProductionLineController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ExcelModuleController;
use App\Http\Controllers\Admin\ImportBatchController;
use App\Http\Controllers\Admin\GlobalSearchController;
use App\Http\Controllers\Admin\ModuleAnalyticsController;
use App\Http\Controllers\Admin\ManufacturingReportController;
use App\Http\Controllers\Admin\ProductionLineExcelController;
use App\Http\Controllers\Admin\ProductionLineImportController;
use App\Http\Controllers\Admin\ProductionLinePreviewController;

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

        Route::get('/search', GlobalSearchController::class)->name('search');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
        Route::put('/settings/preferences', [SettingsController::class, 'updatePreferences'])->name('settings.preferences');

        /*
        |--------------------------------------------------------------------------
        | تقرير التصنيع الشامل (Manufacturing Report)
        |--------------------------------------------------------------------------
        */
        Route::prefix('manufacturing-report')->name('manufacturing-report.')->group(function () {
            Route::get('/', [ManufacturingReportController::class, 'index'])->name('index');
            Route::get('/data', [ManufacturingReportController::class, 'data'])->name('data');
            Route::get('/overview', [ManufacturingReportController::class, 'overview'])->name('overview');
            Route::get('/machines', [ManufacturingReportController::class, 'machines'])->name('machines');
            Route::get('/production-lines', [ManufacturingReportController::class, 'productionLines'])->name('production-lines');
            Route::get('/inventory', [ManufacturingReportController::class, 'inventory'])->name('inventory');
            Route::get('/hr', [ManufacturingReportController::class, 'hr'])->name('hr');
            Route::get('/vehicles', [ManufacturingReportController::class, 'vehicles'])->name('vehicles');
            Route::get('/capacity', [ManufacturingReportController::class, 'capacity'])->name('capacity');
            Route::get('/warehouses', [ManufacturingReportController::class, 'warehouses'])->name('warehouses');
            Route::get('/facilities', [ManufacturingReportController::class, 'facilities'])->name('facilities');
            Route::get('/export-excel', [ManufacturingReportController::class, 'exportExcel'])->name('export-excel');
            Route::get('/export-pdf', [ManufacturingReportController::class, 'exportPdf'])->name('export-pdf');
        });

        /*
        |--------------------------------------------------------------------------
        | Production Lines (legacy)
        |--------------------------------------------------------------------------
        */
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

            // ===== Dashboard & Index (static) =====
            Route::get('/dashboard', [ExcelModuleController::class, 'dashboard'])->name('dashboard');
            Route::get('/', [ExcelModuleController::class, 'index'])->name('index');

            // ===== Upload form (GET) =====
            Route::get('/upload', [ExcelModuleController::class, 'create'])->name('create');

            // ===== Preview: POST = upload+validate, GET = show preview =====
            // فصلناهم لاسمين مختلفين لتجنب اللبس
            Route::post('/preview', [ExcelModuleController::class, 'preview'])
                ->name('preview');
            Route::get('/preview', [ExcelModuleController::class, 'previewShow'])
                ->name('preview.show');

            // ===== Import confirm (POST) =====
            Route::post('/import', [ExcelModuleController::class, 'import'])
                ->name('import');

            // ===== Analytics (static before {module}) =====
            Route::get('/analytics', [ModuleAnalyticsController::class, 'dashboard'])
                ->name('analytics.index');
            Route::get('/{moduleCode}/analytics-page', [ModuleAnalyticsController::class, 'page'])
                ->where('moduleCode', '[A-Za-z_]+')
                ->name('analytics.page');
            Route::get('/{moduleCode}/analytics', [ModuleAnalyticsController::class, 'show'])
                ->where('moduleCode', '[A-Za-z_]+')
                ->name('analytics.show');

            // ===== Custom tables per module (before {module}) =====
            Route::get('/table/{moduleCode}', [ExcelModuleController::class, 'showTable'])
                ->where('moduleCode', '[A-Za-z_]+')
                ->name('table');
            Route::get('/table/{moduleCode}/{id}', [ExcelModuleController::class, 'showTableRecord'])
                ->where('moduleCode', '[A-Za-z_]+')
                ->whereNumber('id')
                ->name('table.record');

            // ===== Export / records / destroy (must be before {module}) =====
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

            // ===== Show module records (last: {module}) =====
            Route::get('/{module}', [ExcelModuleController::class, 'show'])
                ->whereNumber('module')
                ->name('show');
        });
    });
});

Route::get('/', fn () => redirect()->route('login'));