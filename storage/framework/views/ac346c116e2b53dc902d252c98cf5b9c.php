<?php $__env->startSection('title', 'رفع ملف Excel'); ?>
<?php $__env->startSection('page_title', 'رفع ملف Excel'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('admin.excel-modules.index')); ?>">موديولات Excel</a></li>
<li class="breadcrumb-item active">رفع ملف</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:72px;height:72px">
                        <i class="bi bi-cloud-upload fs-2"></i>
                    </div>
                    <h4 class="fw-bold">رفع ملف Excel</h4>
                    <p class="text-muted mb-0">الملف يُكتشف تلقائياً من شيت <code>_System</code> (ModuleCode)</p>
                </div>

                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 pe-3">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('admin.excel-modules.preview')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">اختر ملف .xlsx</label>
                        <input type="file" name="excel_file" accept=".xlsx,.xls" class="form-control form-control-lg" required>
                        <div class="form-text">الحد الأقصى 50 ميجابايت · يجب أن يحتوي على شيت البيانات و _System</div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-eye me-1"></i> معاينة قبل الاستيراد
                        </button>
                        <a href="<?php echo e(route('admin.excel-modules.index')); ?>" class="btn btn-outline-secondary">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h6 class="fw-semibold mb-3"><i class="bi bi-lightbulb text-warning me-2"></i>ملاحظات</h6>
                <ul class="mb-0 text-muted small">
                    <li>النظام ديناميكي: أي ملف من الـ 26 بنفس القالب يعمل بدون تعديل كود</li>
                    <li>عند إعادة الاستيراد لنفس الموديول يمكن استبدال البيانات القديمة</li>
                    <li>البيانات تُحفظ JSON مع فهارس لكود الوحدة والإحداثيات</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/admin/excel-modules/create.blade.php ENDPATH**/ ?>