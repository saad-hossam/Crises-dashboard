<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductionLineImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProductionLineImportController extends Controller
{
    /**
     * Confirm and execute Production Lines import.
     */
    public function store(
        Request $request,
        ProductionLineImportService $importService
    ): RedirectResponse {

        /*
         * The upload controller must store this information
         * persistently in the session.
         */
        $upload = $request->session()->get(
            'production_lines_upload'
        );

        if (
            !is_array($upload)
            || empty($upload['stored_path'])
        ) {
            return redirect()
                ->route('admin.production-lines.preview')
                ->with(
                    'error',
                    'لا يوجد ملف Excel مرفوع وجاهز للاستيراد. ارفع الملف مرة أخرى.'
                );
        }

        try {
            /*
             * Execute Insert Only import.
             */
            $batch = $importService->import(
                $upload['stored_path'],
                (int) $request->user()->id
            );

            /*
             * Remove the pending upload from the session.
             */
            $request->session()->forget(
                'production_lines_upload'
            );

            return redirect()
                ->route('admin.production-lines.preview')
                ->with(
                    'success',
                    "تم الاستيراد بنجاح. رقم العملية: {$batch->id}. "
                    . "السجلات المستوردة: {$batch->imported_rows}."
                )
                ->with('import_batch_id', $batch->id);

        } catch (ValidationException $exception) {
            throw $exception;

        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('admin.production-lines.preview')
                ->with(
                    'error',
                    'حدث خطأ أثناء استيراد خطوط الإنتاج. '
                    . 'تم الاحتفاظ بسجل العملية إن كانت قد بدأت. '
                    . 'راجع سجل الأخطاء لمعرفة التفاصيل.'
                );
        }
    }
}
