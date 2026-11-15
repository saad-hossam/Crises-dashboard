

<?php $__env->startSection('title', __('dashboard.title')); ?>
<?php $__env->startSection('page_title', __('dashboard.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="dash-page" dir="rtl">

    
    <div class="dash-hero">
        <div class="dash-hero-content">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="dash-kicker">
                        <i class="bi bi-lightning-charge-fill"></i>
                        لوحة التحكم
                    </span>
                    <h1 class="dash-title">
                        مرحباً، <?php echo e(auth()->user()?->name ?? 'مستخدم'); ?>

                    </h1>
                    <p class="dash-sub">
                        <i class="bi bi-calendar3 me-1"></i>
                        <?php echo e(now()->translatedFormat('l, d F Y')); ?>

                    </p>
                </div>
                <div class="dash-actions">
                    <a href="<?php echo e(route('admin.excel-modules.index') ?? '#'); ?>" class="dash-btn">
                        <i class="bi bi-grid"></i>
                        الموديولات
                    </a>
                    <a href="<?php echo e(route('admin.excel-modules.create') ?? '#'); ?>" class="dash-btn dash-btn-primary">
                        <i class="bi bi-cloud-upload"></i>
                        استيراد جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    
    <div class="kpi-strip">
        <div class="kpi-tile" style="--kpi-color: #E87524;">
            <div class="kpi-tile-head">
                <span class="kpi-tile-label">إجمالي السجلات</span>
                <span class="kpi-tile-icon"><i class="bi bi-database-fill"></i></span>
            </div>
            <div class="kpi-tile-value"><?php echo e(number_format($totalRecords ?? 0)); ?></div>
            <div class="kpi-tile-foot">
                <span class="kpi-tile-sub">في كل الجداول</span>
            </div>
        </div>

        <div class="kpi-tile" style="--kpi-color: #f59e0b;">
            <div class="kpi-tile-head">
                <span class="kpi-tile-label">الماكينات</span>
                <span class="kpi-tile-icon"><i class="bi bi-cpu"></i></span>
            </div>
            <div class="kpi-tile-value"><?php echo e(number_format($machineCount ?? 0)); ?></div>
            <div class="kpi-tile-foot">
                <span class="kpi-tile-sub">إجمالي الماكينات</span>
            </div>
        </div>

        <div class="kpi-tile" style="--kpi-color: #06b6d4;">
            <div class="kpi-tile-head">
                <span class="kpi-tile-label">خطوط الإنتاج</span>
                <span class="kpi-tile-icon"><i class="bi bi-diagram-3"></i></span>
            </div>
            <div class="kpi-tile-value"><?php echo e(number_format($lineCount ?? 0)); ?></div>
            <div class="kpi-tile-foot">
                <span class="kpi-tile-sub">إجمالي الخطوط</span>
            </div>
        </div>

        <div class="kpi-tile" style="--kpi-color: #10b981;">
            <div class="kpi-tile-head">
                <span class="kpi-tile-label">الموديولات النشطة</span>
                <span class="kpi-tile-icon"><i class="bi bi-grid-3x3-gap-fill"></i></span>
            </div>
            <div class="kpi-tile-value"><?php echo e(number_format($activeModules ?? 0)); ?></div>
            <div class="kpi-tile-foot">
                <span class="kpi-tile-sub">من إجمالي <?php echo e($totalModules ?? 0); ?> موديول</span>
            </div>
        </div>
    </div>

    
    <div class="dash-grid">

        
        <div class="dash-card dash-card-lg">
            <div class="dash-card-head">
                <div>
                    <h3 class="dash-card-title">
                        <i class="bi bi-bar-chart-fill"></i>
                        توزيع السجلات حسب الموديول
                    </h3>
                    <p class="dash-card-sub">أكبر 10 موديولات</p>
                </div>
                <div class="dash-card-badge">
                    <i class="bi bi-database"></i>
                    <?php echo e(number_format($totalRecords ?? 0)); ?> سجل
                </div>
            </div>
            <?php if(count($top10ModulesLabels ?? []) > 0): ?>
                <div class="dash-chart dash-chart-lg">
                    <canvas id="chartTop10"></canvas>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>لا توجد بيانات</p>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="dash-card">
            <div class="dash-card-head">
                <div>
                    <h3 class="dash-card-title">
                        <i class="bi bi-list-ol"></i>
                        ترتيب الموديولات
                    </h3>
                    <p class="dash-card-sub">حسب عدد السجلات</p>
                </div>
            </div>
            <div class="ranking-list">
                <?php $__empty_1 = true; $__currentLoopData = $sortedModules ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="ranking-item">
                        <span class="ranking-num" style="background: <?php echo e($m['color']); ?>;"><?php echo e($i + 1); ?></span>
                        <span class="ranking-name"><?php echo e($m['name']); ?></span>
                        <span class="ranking-count" style="color: <?php echo e($m['color']); ?>;">
                            <?php echo e(number_format($m['count'])); ?>

                        </span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>لا توجد بيانات</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="dash-section-head">
        <h2 class="dash-section-title">
            <i class="bi bi-grid-3x3-gap-fill"></i>
            الموديولات
            <?php if(isset($paginatedModules)): ?>
                <span class="dash-section-count"><?php echo e(number_format($paginatedModules->total())); ?></span>
            <?php endif; ?>
        </h2>
        <a href="<?php echo e(route('admin.excel-modules.index') ?? '#'); ?>" class="btn btn-sm btn-outline-primary">
            عرض الكل
            <i class="bi bi-arrow-left ms-1"></i>
        </a>
    </div>

    <div class="row g-3 mb-3">
        <?php
            $modulesToShow = $paginatedModules ?? collect($moduleStats ?? []);
        ?>
        <?php $__empty_1 = true; $__currentLoopData = $modulesToShow; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-12 col-sm-6 col-xl-4 col-xxl-3">
                <a href="<?php echo e(route('admin.excel-modules.table', $m['code']) ?? '#'); ?>"
                   class="module-card"
                   style="--mod-color: <?php echo e($m['color']); ?>;">
                    <div class="module-card-head">
                        <div class="module-card-icon">
                            <i class="bi <?php echo e($m['icon']); ?>"></i>
                        </div>
                        <div class="min-w-0 flex-grow-1">
                            <h3 class="module-card-name"><?php echo e($m['name']); ?></h3>
                            <span class="module-card-code"><?php echo e($m['code']); ?></span>
                        </div>
                    </div>

                    <div class="module-card-value">
                        <?php echo e(number_format($m['count'])); ?>

                        <span class="module-card-value-lbl">سجل</span>
                    </div>

                    <div class="module-card-metrics">
                        <div class="module-card-metric">
                            <span class="metric-label">GPS</span>
                            <span class="metric-value"><?php echo e(number_format($m['with_coords'])); ?></span>
                        </div>
                        <div class="module-card-metric">
                            <span class="metric-label">التغطية</span>
                            <span class="metric-value"><?php echo e($m['coverage']); ?>%</span>
                        </div>
                    </div>

                    <div class="module-card-progress">
                        <div class="module-card-progress-fill"
                             style="width: <?php echo e($m['coverage']); ?>%; background: <?php echo e($m['color']); ?>;"></div>
                    </div>

                    <div class="module-card-cta">
                        <span>عرض البيانات</span>
                        <i class="bi bi-arrow-left"></i>
                    </div>
                </a>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12">
                <div class="dash-card">
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>لا توجد موديولات</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    
    <?php if(isset($paginatedModules) && $paginatedModules->hasPages()): ?>
        <div class="dash-pagination">
            <div class="dash-pagination-info">
                عرض <?php echo e($paginatedModules->firstItem()); ?>–<?php echo e($paginatedModules->lastItem()); ?>

                من <?php echo e(number_format($paginatedModules->total())); ?> موديول
            </div>
            <div class="dash-pagination-links">
                <?php echo e($paginatedModules->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    <?php endif; ?>

    
    <?php if(($recentImports ?? collect())->count() > 0): ?>
        <div class="dash-card">
            <div class="dash-card-head">
                <div>
                    <h3 class="dash-card-title">
                        <i class="bi bi-clock-history"></i>
                        آخر عمليات الاستيراد
                    </h3>
                </div>
            </div>
            <div class="table-responsive">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>الموديول</th>
                            <th class="d-none d-md-table-cell">الملف</th>
                            <th>السجلات</th>
                            <th>الحالة</th>
                            <th>الوقت</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $recentImports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <span class="badge-module"><?php echo e($batch->module_code); ?></span>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="text-truncate d-inline-block" style="max-width: 200px;">
                                        <?php echo e($batch->original_filename); ?>

                                    </span>
                                </td>
                                <td><?php echo e(number_format($batch->imported_rows)); ?></td>
                                <td>
                                    <?php if($batch->status === 'completed'): ?>
                                        <span class="badge-status badge-success">مكتمل</span>
                                    <?php else: ?>
                                        <span class="badge-status badge-danger"><?php echo e($batch->status); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small text-nowrap">
                                    <?php echo e($batch->created_at?->diffForHumans()); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    const isRtl = document.documentElement.dir === 'rtl';

    // ✅ Palette جديدة بدون ألوان غامقة
    const palette = [
        '#E87524', '#FF9A56', '#5B9BD5', '#7EC8E3', '#10b981',
        '#34D399', '#f59e0b', '#FBBF24', '#A78BFA', '#EC4899',
        '#06b6d4', '#22D3EE', '#84cc16', '#F97316', '#F43F5E'
    ];

    Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#64748b';

    const fmt = n => Number(n).toLocaleString('en-US');

    // ============================================================
    // Top 10 Modules (Horizontal Bar)
    // ============================================================
    const top10El = document.getElementById('chartTop10');
    if (top10El) {
        const labels = <?php echo json_encode($top10ModulesLabels ?? [], 15, 512) ?>;
        const data   = <?php echo json_encode($top10ModulesData ?? [], 15, 512) ?>;
        if (labels.length) {
            new Chart(top10El, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        data,
                        backgroundColor: labels.map((_, i) => palette[i % palette.length]),
                        borderRadius: 8,
                        borderSkipped: false,
                        barThickness: 22,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            rtl: isRtl,
                            backgroundColor: 'rgba(255, 255, 255, 0.98)',
                            titleColor: '#0f172a',
                            bodyColor: '#334155',
                            borderColor: '#e2e8f0',
                            borderWidth: 1,
                            padding: 10,
                            cornerRadius: 8,
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 12 },
                            callbacks: {
                                label: ctx => {
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total ? ((ctx.parsed.x / total) * 100).toFixed(1) : 0;
                                    return ` ${fmt(ctx.parsed.x)} سجل (${pct}%)`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                callback: v => fmt(v),
                                font: { size: 10 },
                                color: '#64748b',
                            },
                            grid: { color: 'rgba(15,23,42,.06)' }
                        },
                        y: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 11, weight: '600' },
                                color: '#475569',
                            }
                        }
                    }
                }
            });
        }
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* ============================================================
   DASHBOARD — Modern Redesign
   ============================================================ */
.dash-page {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    max-width: 1600px;
    margin: 0 auto;
}

/* HERO */
.dash-hero {
    position: relative;
    border-radius: 1.5rem;
    padding: 2rem;
    margin-bottom: 1.75rem;
    background: linear-gradient(135deg, #E87524 0%, #CF5F15 55%, #17365D 100%);
    color: #fff;
    overflow: hidden;
    box-shadow: 0 20px 40px -12px rgba(232, 117, 36, 0.35);
}
.dash-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 15% 25%, rgba(255, 255, 255, 0.15) 0%, transparent 40%),
        radial-gradient(circle at 85% 75%, rgba(255, 255, 255, 0.1) 0%, transparent 45%);
    pointer-events: none;
}
.dash-hero::after {
    content: '';
    position: absolute;
    bottom: -60px;
    inset-inline-end: -60px;
    width: 320px;
    height: 320px;
    border-radius: 50%;
    border: 40px solid rgba(255, 255, 255, 0.05);
    pointer-events: none;
}
.dash-hero-content { position: relative; z-index: 2; }
.dash-kicker {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    background: rgba(255, 255, 255, 0.18);
    backdrop-filter: blur(8px);
    padding: .35rem .85rem;
    border-radius: 50px;
    font-size: .78rem;
    font-weight: 600;
    letter-spacing: .3px;
    margin-bottom: 1rem;
    border: 1px solid rgba(255, 255, 255, .2);
}
.dash-title {
    font-size: clamp(1.4rem, 2.5vw, 2rem);
    font-weight: 800;
    margin-bottom: .5rem;
    line-height: 1.25;
    color: #fff;
}
.dash-sub {
    font-size: .92rem;
    color: rgba(255, 255, 255, .85);
    margin-bottom: 0;
}
.dash-actions { display: flex; gap: .65rem; flex-wrap: wrap; }
.dash-btn {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .65rem 1.15rem;
    border-radius: .75rem;
    font-size: .85rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s;
    border: 1px solid rgba(255, 255, 255, .25);
    background: rgba(255, 255, 255, .15);
    color: #fff;
    backdrop-filter: blur(6px);
}
.dash-btn:hover {
    background: rgba(255, 255, 255, .25);
    color: #fff;
    transform: translateY(-2px);
}
.dash-btn-primary {
    background: #fff;
    color: #E87524;
    border-color: #fff;
}
.dash-btn-primary:hover { background: #f8fafc; color: #E87524; }

/* KPI STRIP */
.kpi-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
    margin-bottom: 1.75rem;
}
.kpi-tile {
    background: #fff;
    border-radius: 1.15rem;
    padding: 1.25rem 1.25rem 1rem;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
    border: 1px solid rgba(15, 23, 42, 0.04);
    position: relative;
    overflow: hidden;
    transition: all .25s ease;
}
.kpi-tile::before {
    content: '';
    position: absolute;
    top: 0;
    inset-inline-start: 0;
    width: 4px;
    height: 100%;
    background: var(--kpi-color, #E87524);
    border-radius: 0 4px 4px 0;
}
.kpi-tile:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 30px rgba(15, 23, 42, 0.1);
    border-color: var(--kpi-color, #E87524);
}
.kpi-tile-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: .5rem;
}
.kpi-tile-label {
    font-size: .78rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .3px;
}
.kpi-tile-icon {
    width: 38px;
    height: 38px;
    border-radius: .65rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: #fff;
    background: var(--kpi-color, #E87524);
    box-shadow: 0 4px 10px -3px var(--kpi-color, #E87524);
    flex-shrink: 0;
}
.kpi-tile-value {
    font-size: 1.85rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
    letter-spacing: -0.5px;
    margin-bottom: .5rem;
}
.kpi-tile-foot {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: .4rem;
    font-size: .72rem;
}
.kpi-tile-sub { color: #94a3b8; }

/* GRID */
.dash-grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 1rem;
    margin-bottom: 1.75rem;
}
.dash-card {
    grid-column: span 4;
    background: #fff;
    border-radius: 1.15rem;
    padding: 1.5rem;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
    border: 1px solid rgba(15, 23, 42, 0.04);
    display: flex;
    flex-direction: column;
}
.dash-card-lg { grid-column: span 8; }
.dash-card-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.25rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f1f5f9;
    gap: 1rem;
}
.dash-card-title {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 .15rem;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
}
.dash-card-title i { color: #E87524; }
.dash-card-sub {
    font-size: .78rem;
    color: #94a3b8;
    margin: 0;
}
.dash-card-badge {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    background: #dcfce7;
    color: #166534;
    padding: .35rem .75rem;
    border-radius: 50px;
    font-size: .78rem;
    font-weight: 700;
    flex-shrink: 0;
}
.dash-chart { position: relative; height: 260px; flex: 1; }
.dash-chart-lg { height: 320px; }

/* RANKING LIST */
.ranking-list {
    max-height: 300px;
    overflow-y: auto;
}
.ranking-item {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .55rem 0;
    border-bottom: 1px solid #f1f5f9;
}
.ranking-item:last-child { border-bottom: 0; }
.ranking-num {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .72rem;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
}
.ranking-name {
    flex: 1;
    font-size: .85rem;
    font-weight: 600;
    color: #0f172a;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.ranking-count {
    font-size: .85rem;
    font-weight: 700;
}

/* SECTION HEAD */
.dash-section-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 2rem 0 1.25rem;
    flex-wrap: wrap;
    gap: .75rem;
}
.dash-section-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
}
.dash-section-title i { color: #E87524; }
.dash-section-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 24px;
    padding: 0 .5rem;
    border-radius: 50px;
    background: #dbeafe;
    color: #1e40af;
    font-size: .72rem;
    font-weight: 700;
    margin-inline-start: .35rem;
}

/* MODULE CARDS */
.module-card {
    display: block;
    text-decoration: none;
    color: inherit;
    background: #fff;
    border-radius: 1rem;
    padding: 1.15rem;
    border: 1px solid #e2e8f0;
    height: 100%;
    transition: all .25s ease;
    position: relative;
    overflow: hidden;
}
.module-card::before {
    content: '';
    position: absolute;
    top: 0;
    inset-inline-end: 0;
    width: 100%;
    height: 3px;
    background: var(--mod-color, #E87524);
    transform: scaleX(0);
    transform-origin: inset-inline-end;
    transition: transform .3s ease;
}
.module-card:hover {
    color: inherit;
    transform: translateY(-3px);
    box-shadow: 0 12px 25px rgba(15, 23, 42, 0.1);
    border-color: transparent;
}
.module-card:hover::before { transform: scaleX(1); }
.module-card-head {
    display: flex;
    align-items: flex-start;
    gap: .75rem;
    margin-bottom: .9rem;
}
.module-card-icon {
    width: 42px;
    height: 42px;
    border-radius: .75rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--mod-color, #E87524);
    color: #fff;
    font-size: 1.15rem;
    flex-shrink: 0;
}
.module-card-name {
    font-size: .95rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 .15rem;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.module-card-code {
    font-size: .7rem;
    color: #64748b;
    font-family: 'Courier New', monospace;
    background: #f1f5f9;
    padding: .15rem .5rem;
    border-radius: 5px;
    display: inline-block;
}
.module-card-value {
    font-size: 1.6rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
    letter-spacing: -0.5px;
    margin-bottom: .75rem;
}
.module-card-value-lbl {
    font-size: .75rem;
    font-weight: 600;
    color: #94a3b8;
    letter-spacing: 0;
}
.module-card-metrics {
    display: flex;
    gap: .35rem;
    margin-bottom: .75rem;
}
.module-card-metric {
    flex: 1;
    background: #f8fafc;
    border-radius: .5rem;
    padding: .45rem .35rem;
    text-align: center;
}
.metric-label {
    display: block;
    font-size: .65rem;
    color: #94a3b8;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: .15rem;
}
.metric-value {
    display: block;
    font-size: .85rem;
    font-weight: 700;
    color: #0f172a;
}
.module-card-progress {
    height: 4px;
    background: #f1f5f9;
    border-radius: 50px;
    overflow: hidden;
    margin-bottom: .75rem;
}
.module-card-progress-fill {
    height: 100%;
    border-radius: 50px;
    transition: width .6s ease;
}
.module-card-cta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: .78rem;
    font-weight: 600;
    color: var(--mod-color, #E87524);
    padding-top: .75rem;
    border-top: 1px dashed #e2e8f0;
}

/* PAGINATION */
.dash-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: .75rem;
    padding: .85rem 1rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: .85rem;
    margin-bottom: 1.75rem;
}
.dash-pagination-info {
    font-size: .82rem;
    color: #64748b;
    font-weight: 500;
}
.dash-pagination-links .pagination {
    margin: 0;
    flex-wrap: wrap;
    gap: .15rem;
}
.dash-pagination-links .page-link {
    font-size: .8rem;
    padding: .35rem .65rem;
    border-radius: .45rem;
    color: #E87524;
    min-width: 34px;
    text-align: center;
    background: #fff;
    border: 1px solid #e2e8f0;
    transition: all .15s;
}
.dash-pagination-links .page-link:hover {
    background: #fff4eb;
    border-color: #fed7aa;
    color: #CF5F15;
}
.dash-pagination-links .page-item.active .page-link {
    background: #E87524;
    border-color: #E87524;
    color: #fff;
}
.dash-pagination-links .page-item.disabled .page-link {
    color: #cbd5e1;
    background: #f8fafc;
    border-color: #e2e8f0;
}

/* TABLE */
.dash-table { width: 100%; margin: 0; }
.dash-table thead th {
    font-size: .72rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .4px;
    padding: .75rem .85rem;
    border-bottom: 2px solid #f1f5f9;
    background: #fafbfc;
    text-align: start;
}
.dash-table tbody td {
    padding: .85rem;
    font-size: .85rem;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
.dash-table tbody tr:hover { background: #f8fafc; }
.dash-table tbody tr:last-child td { border-bottom: 0; }

.badge-module {
    display: inline-block;
    padding: .2rem .55rem;
    border-radius: 5px;
    background: #dbeafe;
    color: #1e40af;
    font-size: .72rem;
    font-weight: 700;
    font-family: 'Courier New', monospace;
}
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    padding: .25rem .65rem;
    border-radius: 50px;
    font-size: .72rem;
    font-weight: 600;
}
.badge-success { background: #dcfce7; color: #166534; }
.badge-danger  { background: #fee2e2; color: #991b1b; }

/* EMPTY */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 1rem;
    color: #94a3b8;
    text-align: center;
    min-height: 200px;
}
.empty-state i {
    font-size: 2.5rem;
    opacity: .4;
    margin-bottom: .75rem;
}
.empty-state p { font-size: .88rem; margin: 0; }

/* RESPONSIVE */
@media (max-width: 1199.98px) {
    .dash-card    { grid-column: span 12; }
    .dash-card-lg { grid-column: span 12; }
}
@media (max-width: 991.98px) {
    .dash-hero { padding: 1.75rem 1.5rem; }
    .kpi-strip { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 767.98px) {
    .dash-hero { padding: 1.5rem 1.25rem; border-radius: 1rem; }
    .dash-title { font-size: 1.2rem; }
    .dash-sub { font-size: .82rem; }
    .kpi-strip { grid-template-columns: repeat(2, 1fr); gap: .65rem; }
    .kpi-tile { padding: 1rem; }
    .kpi-tile-value { font-size: 1.4rem; }
    .kpi-tile-icon { width: 32px; height: 32px; font-size: .85rem; }
    .dash-card,
    .dash-card-lg { grid-column: span 12; padding: 1.15rem; }
    .dash-chart { height: 240px; }
    .dash-chart-lg { height: 260px; }
    .dash-card-head { margin-bottom: 1rem; padding-bottom: .85rem; }
    .dash-card-title { font-size: .92rem; }
    .dash-card-badge { font-size: .7rem; padding: .25rem .55rem; }
    .dash-pagination {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }
    .dash-pagination-links .pagination {
        justify-content: center;
    }
    .dash-pagination-links .page-link {
        font-size: .72rem;
        padding: .3rem .5rem;
        min-width: 30px;
    }
}
@media (max-width: 575.98px) {
    .kpi-strip { grid-template-columns: 1fr; }
    .dash-hero { padding: 1.25rem 1rem; }
    .dash-actions { width: 100%; }
    .dash-btn { flex: 1; justify-content: center; }
    .dash-chart { height: 220px; }
}

/* DARK MODE */
.admin-wrapper.dark-mode .kpi-tile,
.admin-wrapper.dark-mode .dash-card,
.admin-wrapper.dark-mode .module-card,
.admin-wrapper.dark-mode .dash-pagination {
    background: #1e293b;
    border-color: #334155;
}
.admin-wrapper.dark-mode .kpi-tile-value,
.admin-wrapper.dark-mode .kpi-tile-label,
.admin-wrapper.dark-mode .dash-card-title,
.admin-wrapper.dark-mode .dash-section-title,
.admin-wrapper.dark-mode .module-card-name,
.admin-wrapper.dark-mode .module-card-value,
.admin-wrapper.dark-mode .metric-value,
.admin-wrapper.dark-mode .ranking-name {
    color: #f1f5f9;
}
.admin-wrapper.dark-mode .kpi-tile-sub,
.admin-wrapper.dark-mode .dash-card-sub,
.admin-wrapper.dark-mode .metric-label,
.admin-wrapper.dark-mode .dash-pagination-info {
    color: #94a3b8;
}
.admin-wrapper.dark-mode .module-card-metric,
.admin-wrapper.dark-mode .module-card-progress {
    background: #0f172a;
}
.admin-wrapper.dark-mode .ranking-item {
    border-color: #334155;
}
.admin-wrapper.dark-mode .dash-table thead th {
    background: #17243a;
    color: #cbd5e1;
    border-color: #334155;
}
.admin-wrapper.dark-mode .dash-table tbody td {
    color: #cbd5e1;
    border-color: #334155;
}
.admin-wrapper.dark-mode .dash-table tbody tr:hover {
    background: #263750;
}
.admin-wrapper.dark-mode .dash-pagination-links .page-link {
    background: #1e293b;
    border-color: #334155;
    color: #FF9A56;
}
.admin-wrapper.dark-mode .dash-pagination-links .page-item.active .page-link {
    background: #E87524;
    border-color: #E87524;
    color: #fff;
}
.admin-wrapper.dark-mode .dash-section-count {
    background: rgba(232, 117, 36, 0.2);
    color: #FF9A56;
}
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\pc\Music\Crises-dashboard\Crises-dashboard\resources\views/admin/dashboard/index.blade.php ENDPATH**/ ?>