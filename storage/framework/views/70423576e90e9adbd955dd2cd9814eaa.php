<?php $__env->startSection('title', 'لوحة بيانات Excel'); ?>
<?php $__env->startSection('page_title', 'لوحة بيانات Excel'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active" aria-current="page">لوحة بيانات Excel</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">عدد الموديولات</div>
                    <div class="fs-3 fw-bold text-primary"><?php echo e(number_format($totalModules)); ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">إجمالي الصفوف</div>
                    <div class="fs-3 fw-bold text-success"><?php echo e(number_format($totalRecords)); ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">آخر استيراد</div>
                    <div class="fw-semibold">
                        <?php if($lastImport): ?>
                            <?php echo e($lastImport->created_at->diffForHumans()); ?>

                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </div>
                    <?php if($lastImport?->module): ?>
                        <div class="small text-muted text-truncate"><?php echo e($lastImport->module->name_ar); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <a href="<?php echo e(route('admin.excel-modules.create')); ?>" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i> رفع ملف جديد
                    </a>
                    <a href="<?php echo e(route('admin.excel-modules.index')); ?>" class="btn btn-outline-secondary btn-sm mt-2">
                        كل الموديولات
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    توزيع الصفوف حسب الموديول
                </div>
                <div class="card-body">
                    <?php if(count($chartLabels) > 0): ?>
                        <canvas id="modulesChart" height="260"></canvas>
                    <?php else: ?>
                        <div class="text-center text-muted py-5">
                            لا توجد بيانات بعد
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    آخر عمليات الاستيراد
                </div>
                <div class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $recentBatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div class="text-truncate pe-2">
                                    <div class="fw-semibold small">
                                        <?php echo e($batch->module?->name_ar ?? '—'); ?>

                                    </div>
                                    <div class="text-muted small text-truncate">
                                        <?php echo e($batch->original_filename); ?>

                                    </div>
                                </div>
                                <div class="text-end small text-nowrap">
                                    <div class="badge bg-success"><?php echo e(number_format($batch->imported_rows)); ?> صف</div>
                                    <div class="text-muted"><?php echo e($batch->created_at->diffForHumans()); ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="list-group-item text-center text-muted py-4">
                            لا توجد عمليات استيراد بعد
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">كل الموديولات</span>
            <a href="<?php echo e(route('admin.excel-modules.index')); ?>" class="btn btn-sm btn-outline-primary">عرض كروت</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>الموديول</th>
                        <th>الكود</th>
                        <th class="text-end">عدد الصفوف</th>
                        <th>آخر استيراد</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="fw-semibold"><?php echo e($module->name_ar); ?></td>
                            <td><code><?php echo e($module->code); ?></code></td>
                            <td class="text-end"><?php echo e(number_format($module->records_count)); ?></td>
                            <td class="small text-muted">
                                <?php echo e($module->last_imported_at?->format('Y-m-d H:i') ?? '—'); ?>

                            </td>
                            <td class="text-end">
                                <a href="<?php echo e(route('admin.excel-modules.show', $module)); ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    عرض
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">لا توجد موديولات</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php if(count($chartLabels) > 0): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('modulesChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chartLabels, 15, 512) ?>,
            datasets: [{
                label: 'عدد الصفوف',
                data: <?php echo json_encode($chartData, 15, 512) ?>,
                backgroundColor: 'rgba(13, 110, 253, 0.7)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 0,
                        font: { size: 11 }
                    }
                }
            }
        }
    });
});
</script>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/admin/excel-modules/dashboard.blade.php ENDPATH**/ ?>