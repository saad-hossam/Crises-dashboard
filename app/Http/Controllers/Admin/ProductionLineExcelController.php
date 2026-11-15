<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProductionLineExcelController extends Controller
{
    /**
     * Upload Excel file, extract its records and generate preview.
     *
     * No production_lines database insertion is performed here.
     */
    public function store(Request $request): RedirectResponse
    {
        /*
         * 1. Validate uploaded Excel file.
         */
        $validated = $request->validate(
            [
                'excel_file' => [
                    'required',
                    'file',
                    'mimes:xlsx,xls',
                    'max:20480',
                ],
            ],
            [
                'excel_file.required' => 'يرجى اختيار ملف Excel.',
                'excel_file.file' => 'الملف المرفوع غير صالح.',
                'excel_file.mimes' => 'يجب أن يكون الملف بصيغة XLSX أو XLS.',
                'excel_file.max' => 'حجم الملف يجب ألا يتجاوز 20 ميجابايت.',
            ]
        );

        $file = $validated['excel_file'];

        try {
            /*
             * 2. Calculate SHA-256.
             */
            $fileHash = hash_file('sha256', $file->getRealPath());

            if ($fileHash === false) {
                return back()
                    ->withInput()
                    ->with('error', 'تعذر قراءة الملف لحساب بصمته الرقمية.');
            }

            /*
             * 3. Preserve the original filename safely.
             */
            $originalName = basename(
                str_replace('\\', '/', $file->getClientOriginalName())
            );

            $extension = strtolower(
                $file->getClientOriginalExtension()
            );

            /*
             * 4. Save the uploaded file privately.
             *
             * SHA-256 directory keeps different file contents separate.
             * Re-uploading the same file replaces its stored copy.
             */
            $directory = 'production-lines/uploads/' . $fileHash;

            $storedPath = $file->storeAs(
                $directory,
                $originalName,
                'local'
            );

            if (!$storedPath) {
                return back()
                    ->withInput()
                    ->with('error', 'تعذر حفظ ملف Excel على الخادم.');
            }

            $absolutePath = Storage::disk('local')->path($storedPath);

            if (!is_file($absolutePath)) {
                return back()
                    ->withInput()
                    ->with('error', 'لم يتم العثور على الملف بعد حفظه.');
            }

            /*
             * 5. Remove previous generated extraction and preview files.
             *
             * This prevents an old preview from being displayed when
             * processing a new uploaded workbook fails.
             *
             * The uploaded source file is NOT deleted.
             */
            File::delete([
                storage_path('app/extracted/production_lines.json'),
                storage_path('app/previews/production_lines_preview.json'),
            ]);

            /*
             * 6. Run Excel extraction command.
             */
            $extractExitCode = Artisan::call(
                'excel:extract-production-lines',
                [
                    'file' => $absolutePath,
                ]
            );

            $extractOutput = trim(Artisan::output());

            if ($extractExitCode !== 0) {
                report(
                    new \RuntimeException(
                        'Production Lines Excel extraction failed: '
                        . $extractOutput
                    )
                );

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'فشل استخراج البيانات من ملف Excel. '
                        . 'راجع سجل التطبيق لمعرفة التفاصيل.'
                    );
            }

            /*
             * 7. Run validation and preview command.
             */
            $previewExitCode = Artisan::call(
                'excel:preview-production-lines'
            );

            $previewOutput = trim(Artisan::output());

            if ($previewExitCode !== 0) {
                report(
                    new \RuntimeException(
                        'Production Lines Excel preview failed: '
                        . $previewOutput
                    )
                );

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'تم استخراج الملف، لكن فشلت مرحلة المعاينة والتحقق. '
                        . 'راجع سجل التطبيق لمعرفة التفاصيل.'
                    );
            }

            /*
             * 8. Verify generated preview file.
             */
            $previewPath = storage_path(
                'app/previews/production_lines_preview.json'
            );

            if (!File::exists($previewPath)) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'انتهى أمر المعاينة دون إنشاء ملف التقرير.'
                    );
            }

            /*
             * 9. Store upload information in session.
             */
            session()->put('production_lines_upload', [
                'original_name' => $originalName,
                'extension' => $extension,
                'sha256' => $fileHash,
                'stored_path' => $storedPath,
                'uploaded_at' => now()->toIso8601String(),
            ]);

            /*
             * 10. Redirect to preview screen.
             */
            return redirect()
                ->route('admin.production-lines.preview')
                ->with(
                    'success',
                    'تم رفع ملف Excel واستخراج بياناته والتحقق منها بنجاح. '
                    . 'يمكنك الآن مراجعة السجلات قبل الاستيراد.'
                );

        } catch (Throwable $exception) {

            report($exception);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'حدث خطأ أثناء معالجة ملف Excel. '
                    . 'راجع سجل التطبيق لمعرفة التفاصيل.'
                );
        }
    }
}
