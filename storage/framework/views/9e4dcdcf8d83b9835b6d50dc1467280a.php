<?php $__env->startSection('title', __('dashboard.title')); ?>
<?php $__env->startSection('page_title', __('dashboard.title')); ?>

<?php $__env->startSection('content'); ?>

<div class="dash-hero mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <p class="dash-hero-kicker mb-1">
                <i class="bi bi-lightning-charge-fill me-1"></i><?php echo e(__('dashboard.analysis')); ?>

            </p>
            <h1 class="dash-hero-title mb-1">
                <?php echo e(__('dashboard.welcome', ['name' => auth()->user()?->name ?? ''])); ?>

            </h1>
            <p class="dash-hero-sub mb-0">
                <?php echo e(now()->translatedFormat('l, d F Y')); ?> · <?php echo e(__('dashboard.description')); ?>

            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.excel-modules.index')); ?>" class="btn btn-light btn-sm">
                <i class="bi bi-grid me-1"></i><?php echo e(__('excel.modules')); ?>

            </a>
            <a href="<?php echo e(route('admin.excel-modules.create')); ?>" class="btn btn-light btn-sm fw-semibold">
                <i class="bi bi-cloud-upload me-1"></i><?php echo e(__('dashboard.import_now')); ?>

            </a>
        </div>
    </div>
</div>


<div class="row g-3 mb-4">
    <?php $__currentLoopData = $stats ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-6 col-lg-3">
            <div class="kpi-card h-100">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div class="min-w-0">
                        <div class="kpi-label"><?php echo e($stat['label']); ?></div>
                        <div class="kpi-value"><?php echo e(number_format($stat['value'])); ?></div>
                    </div>
                    <div class="kpi-icon text-<?php echo e($stat['color']); ?> bg-<?php echo e($stat['color']); ?>-subtle">
                        <i class="bi <?php echo e($stat['icon']); ?>"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="row g-3 mb-4">
    <div class="col-12 col-xl-12">
        <div class="panel-card h-100">
            <div class="panel-head">
                <span><i class="bi bi-bar-chart-fill text-primary me-2"></i><?php echo e(__('dashboard.records_overview')); ?></span>
            </div>
            <div class="panel-body">
                <?php if(array_sum($chartCounts ?? []) > 0): ?>
                    <div class="chart-box"><canvas id="chartModules"></canvas></div>
                <?php else: ?>
                    <div class="empty-chart"><?php echo e(__('dashboard.chart_placeholder')); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
  
    <div class="col-12 col-md-6 col-xl-12">
        <div class="panel-card h-100">
            <div class="panel-head">
                <span><i class="bi bi-geo-alt-fill text-danger me-2"></i><?php echo e(__('dashboard.records_by_category')); ?></span>
            </div>
            <div class="panel-body">
                <?php if(array_sum($govCounts ?? []) > 0): ?>
                    <div class="chart-box chart-box-sm"><canvas id="chartGov"></canvas></div>
                <?php else: ?>
                    <div class="empty-chart"><?php echo e(__('dashboard.chart_placeholder')); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
</div>


<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="section-title">
        <i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i><?php echo e(__('dashboard.module_stats')); ?>

    </h2>
    <a href="<?php echo e(route('admin.excel-modules.index')); ?>" class="btn btn-outline-primary btn-sm">
        <?php echo e(__('excel.modules')); ?>

    </a>
</div>

<div class="row g-3 mb-4">
<?php $__empty_1 = true; $__currentLoopData = $moduleStats ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="col-12 col-sm-6 col-xl-4 col-xxl-3">
        <a href="<?php echo e(route('admin.excel-modules.table', $m['code'])); ?>" class="text-decoration-none d-block h-100">
            <article class="module-card-v2 h-100">
                <div class="module-card-v2-glow"></div>
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="module-card-icon">
                        <i class="bi <?php echo e($m['icon']); ?>"></i>
                    </div>
                    <div class="min-w-0 flex-grow-1">
                        <div class="d-flex justify-content-between gap-2 align-items-start">
                            <h3 class="module-title text-truncate mb-0"><?php echo e($m['name']); ?></h3>
                            <span class="module-code-badge"><?php echo e($m['code']); ?></span>
                        </div>
                        <div class="module-meta"><code><?php echo e($m['table']); ?></code></div>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-4">
                        <div class="mini-stat">
                            <div class="mini-stat-val"><?php echo e(number_format($m['count'])); ?></div>
                            <div class="mini-stat-lbl"><?php echo e(__('excel.records')); ?></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mini-stat">
                            <div class="mini-stat-val"><?php echo e(number_format($m['with_coords'])); ?></div>
                            <div class="mini-stat-lbl"><i class="bi bi-geo-alt"></i> GPS</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mini-stat">
                            <div class="mini-stat-val"><?php echo e($m['coverage']); ?>%</div>
                            <div class="mini-stat-lbl"><?php echo e(__('excel.location')); ?></div>
                        </div>
                    </div>
                </div>
                <div class="module-card-v2-cta">
                    <span><?php echo e(__('excel.view_data')); ?></span>
                    <i class="bi bi-arrow-<?php echo e(app()->getLocale() === 'ar' ? 'left' : 'right'); ?>"></i>
                </div>
            </article>
        </a>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-12">
        <div class="alert alert-info mb-0"><?php echo e(__('dashboard.no_modules')); ?></div>
    </div>
<?php endif; ?>
</div>


<div class="panel-card">
    <div class="panel-head">
        <span><i class="bi bi-clock-history me-2"></i><?php echo e(__('dashboard.recent_imports')); ?></span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 dash-table">
            <thead>
                <tr>
                    <th class="ps-3"><?php echo e(__('excel.modules')); ?></th>
                    <th class="d-none d-md-table-cell"><?php echo e(__('dashboard.file')); ?></th>
                    <th><?php echo e(__('dashboard.rows')); ?></th>
                    <th><?php echo e(__('dashboard.status')); ?></th>
                    <th class="pe-3"><?php echo e(__('dashboard.time')); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $recentImports ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="ps-3">
                        <span class="badge bg-primary-subtle text-primary border"><?php echo e($batch->module_code); ?></span>
                        <div class="small text-muted text-truncate" style="max-width:160px"><?php echo e($batch->module_name_ar); ?></div>
                    </td>
                    <td class="d-none d-md-table-cell small text-truncate" style="max-width:180px">
                        <?php echo e($batch->original_filename); ?>

                    </td>
                    <td class="small">
                        <?php echo e(number_format($batch->imported_rows)); ?>/<?php echo e(number_format($batch->total_rows)); ?>

                    </td>
                    <td>
                        <?php if($batch->status === 'completed'): ?>
                            <span class="badge bg-success-subtle text-success">OK</span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger"><?php echo e($batch->status); ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="pe-3 small text-muted text-nowrap"><?php echo e($batch->created_at?->diffForHumans()); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4"><?php echo e(__('dashboard.no_data')); ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    const isRtl = document.documentElement.dir === 'rtl';
    const css = getComputedStyle(document.documentElement);

    // Uses --aoi-* aliases we defined in CSS root
    const primary  = (css.getPropertyValue('--aoi-primary')   || '#E87524').trim() || '#E87524';
    const primary2 = (css.getPropertyValue('--aoi-primary-2') || '#06b6d4').trim() || '#06b6d4';
    const accent   = (css.getPropertyValue('--aoi-accent')    || '#17365D').trim() || '#17365D';

    const palette = [primary, primary2, accent, '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#64748b', '#ec4899', '#84cc16'];

    Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
    Chart.defaults.font.size = 12;
    Chart.defaults.color = (css.getPropertyValue('--aoi-muted') || '#64748b').trim() || '#64748b';

    function makeChart(id, type, labels, data) {
        const el = document.getElementById(id);
        if (!el || !Array.isArray(labels) || !labels.length) return;

        const isDonut = type === 'doughnut' || type === 'pie';

        new Chart(el, {
            type,
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: palette.slice(0, Math.max(labels.length, 1)),
                    borderWidth: 0,
                    borderRadius: type === 'bar' ? 8 : 0,
                    cutout: type === 'doughnut' ? '62%' : undefined,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: isDonut,
                        position: 'bottom',
                        rtl: isRtl,
                        labels: { boxWidth: 10, boxHeight: 10, padding: 12, font: { size: 11 } }
                    },
                    tooltip: { rtl: isRtl }
                },
                scales: isDonut ? {} : {
                    x: { grid: { display: false }, ticks: { font: { size: 10 }, maxRotation: 40 } },
                    y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } }, grid: { color: 'rgba(15,23,42,.06)' } }
                }
            }
        });
    }

    makeChart('chartModules',   'bar',      <?php echo json_encode($chartLabels ?? [], 15, 512) ?>,     <?php echo json_encode($chartCounts ?? [], 15, 512) ?>);
    makeChart('chartStatus',    'doughnut', <?php echo json_encode($statusLabels ?? [], 15, 512) ?>,    <?php echo json_encode($statusCounts ?? [], 15, 512) ?>);
    makeChart('chartGov',       'bar',      <?php echo json_encode($govLabels ?? [], 15, 512) ?>,       <?php echo json_encode($govCounts ?? [], 15, 512) ?>);
    makeChart('chartReadiness', 'doughnut', <?php echo json_encode($readinessLabels ?? [], 15, 512) ?>, <?php echo json_encode($readinessCounts ?? [], 15, 512) ?>);
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/admin/dashboard/index.blade.php ENDPATH**/ ?>