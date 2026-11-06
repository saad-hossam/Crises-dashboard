<?php
/**
 * أضف هذا داخل routes/web.php
 * داخل مجموعة الـ admin (مع middleware auth)
 */

use App\Http\Controllers\Admin\ExcelModuleController;

Route::prefix('admin/excel-modules')->name('admin.excel-modules.')->group(function () {
    Route::get('/dashboard',       [ExcelModuleController::class, 'dashboard'])->name('dashboard');
    Route::get('/',                [ExcelModuleController::class, 'index'])->name('index');
    Route::get('/upload',          [ExcelModuleController::class, 'create'])->name('create');
    Route::post('/preview',        [ExcelModuleController::class, 'preview'])->name('preview');
    Route::post('/import',         [ExcelModuleController::class, 'import'])->name('import');
    Route::get('/{module}',        [ExcelModuleController::class, 'show'])->name('show');
    Route::get('/{module}/export', [ExcelModuleController::class, 'export'])->name('export');
    Route::delete('/{module}',     [ExcelModuleController::class, 'destroy'])->name('destroy');
});
