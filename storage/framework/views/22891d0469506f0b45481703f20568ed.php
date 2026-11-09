<?php $__env->startSection('title', $config['name_ar']); ?>
<?php $__env->startSection('page_title', $config['name_ar']); ?>

<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('admin.excel-modules.index')); ?>">موديولات Excel</a></li>
<li class="breadcrumb-item active"><?php echo e($config['name_ar']); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* رفع بطاقة الفلترة فوق بطاقة الجدول حتى لا يُقصّ الـ dropdown */
    .jm-filter-card {
        position: relative;
        overflow: visible;
        z-index: 1060;
    }
    .jm-table-card {
        position: relative;
        z-index: 1;
    }
    .jm-unit-col {
        position: relative;
        z-index: 2;
    }

    #jm-unit-dropdown {
        position: absolute;
        inset-inline-start: 0;
        inset-inline-end: 0;
        top: 100%;
        z-index: 1055;
    }
    #jm-unit-dropdown .dropdown-item {
        cursor: pointer;
        white-space: normal;
        word-break: break-word;
    }
    #jm-unit-dropdown .dropdown-item.active,
    #jm-unit-dropdown .dropdown-item:active {
        background-color: var(--bs-primary);
        color: #fff;
    }
    #jm-unit-dropdown .dropdown-item mark {
        background: var(--bs-warning-bg-subtle);
        padding: 0 1px;
        border-radius: 2px;
    }
    #jm-unit-dropdown .jm-empty {
        padding: .5rem .75rem;
        color: var(--bs-secondary-color);
        font-size: .85rem;
    }

    .ao-table-scroll {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior-x: contain;
    }
    .ao-data-table {
        min-width: 640px;
        margin: 0;
        font-size: 13px;
    }
    .ao-data-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: var(--aoi-surface, #fff);
        border-bottom: 1px solid var(--aoi-border, #e2e8f0);
        font-size: 11.5px;
        font-weight: 700;
        color: var(--aoi-muted, #64748b);
        text-transform: none;
        white-space: nowrap;
        padding: .75rem .85rem;
    }
    .ao-data-table td {
        padding: .7rem .85rem;
        border-bottom: 1px solid var(--aoi-border, #e2e8f0);
        vertical-align: middle;
    }
    .ao-col-sticky {
        position: sticky;
        inset-inline-start: 0;
        z-index: 1;
        background: var(--aoi-surface, #fff);
        box-shadow: 4px 0 8px rgba(15,23,42,.04);
    }
    thead .ao-col-sticky { z-index: 3; }
    .ao-col-id { width: 64px; min-width: 64px; }
    .ao-col-actions { width: 88px; min-width: 88px; }
    .ao-cell {
        display: inline-block;
        max-width: 160px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: bottom;
    }
    .ao-row-clickable { cursor: pointer; }
    .ao-row-clickable:hover { background: rgba(var(--aoi-primary-rgb, 29,78,216), .05); }

    /* hide extra columns on medium screens */
    @media (max-width: 1199.98px) {
        .ao-col-xl-only { display: none !important; }
    }
    @media (max-width: 991.98px) {
        .ao-col-lg-only { display: none !important; }
        .ao-data-table { min-width: 520px; }
        .ao-cell { max-width: 120px; }
    }

    /* Mobile card list */
    .ao-mobile-list { padding: .5rem; }
    .ao-mobile-item {
        display: block;
        background: var(--aoi-surface, #fff);
        border: 1px solid var(--aoi-border, #e2e8f0);
        border-radius: .9rem;
        padding: .85rem 1rem;
        margin-bottom: .6rem;
        color: inherit;
        box-shadow: 0 4px 12px rgba(15,23,42,.04);
    }
    .ao-mobile-item:active { transform: scale(.99); }
    .ao-mobile-item-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: .55rem;
    }
    .ao-mobile-id {
        font-size: 12px;
        font-weight: 700;
        color: var(--aoi-primary, #1d4ed8);
        background: rgba(var(--aoi-primary-rgb, 29,78,216), .1);
        padding: .15rem .45rem;
        border-radius: 6px;
    }
    .ao-mobile-fields { display: grid; gap: .35rem; }
    .ao-mobile-field {
        display: grid;
        grid-template-columns: 38% 1fr;
        gap: .5rem;
        font-size: 12.5px;
    }
    .ao-mobile-label {
        color: var(--aoi-muted, #64748b);
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .ao-mobile-value {
        color: var(--aoi-text, #0f172a);
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        text-align: end;
    }
    .ao-table-footer {
        background: var(--aoi-surface, #fff) !important;
        border-top: 1px solid var(--aoi-border, #e2e8f0);
    }
    .ao-pagination .pagination { margin: 0; flex-wrap: wrap; justify-content: center; }
    .ao-pagination .page-link {
        font-size: 13px;
        padding: .35rem .65rem;
        color: var(--aoi-primary, #1d4ed8);
    }
    .ao-pagination .page-item.active .page-link {
        background: var(--aoi-primary, #1d4ed8);
        border-color: var(--aoi-primary, #1d4ed8);
    }

    @media (max-width: 575.98px) {
        .jm-filter-card .row > [class*="col-"] { margin-bottom: .15rem; }
        .jm-filter-card .btn { width: 100%; }
        .ao-mobile-field { grid-template-columns: 1fr; }
        .ao-mobile-value { text-align: start; }
    }

</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>



<?php if(!empty($moduleStats)): ?>
<div class="row g-3 mb-4">
    <?php $__currentLoopData = $moduleStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-6 col-md-3">
            <div class="kpi-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label"><?php echo e($stat['label']); ?></div>
                        <div class="kpi-value"><?php echo e(number_format($stat['value'])); ?></div>
                    </div>
                    <div class="kpi-icon text-<?php echo e($stat['color'] ?? 'primary'); ?>">
                        <i class="bi <?php echo e($stat['icon'] ?? 'bi-bar-chart'); ?>"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>


<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="panel-card h-100">
            <div class="panel-head"><i class="bi bi-pie-chart-fill text-primary me-1"></i> حالة التشغيل</div>
            <div class="panel-body"><div class="chart-box chart-box-sm"><canvas id="modStatusChart"></canvas></div></div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="panel-card h-100">
            <div class="panel-head"><i class="bi bi-geo-alt-fill text-primary me-1"></i> المحافظات</div>
            <div class="panel-body"><div class="chart-box chart-box-sm"><canvas id="modGovChart"></canvas></div></div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="panel-card h-100">
            <div class="panel-head"><i class="bi bi-shield-check text-primary me-1"></i> الجاهزية</div>
            <div class="panel-body"><div class="chart-box chart-box-sm"><canvas id="modReadyChart"></canvas></div></div>
        </div>
    </div>
</div>




<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h4 class="mb-1 fw-bold"><?php echo e($config['name_ar']); ?></h4>
        <div class="text-muted small d-flex flex-wrap align-items-center gap-2">
            <span class="badge bg-primary-subtle text-primary border"><?php echo e($moduleCode); ?></span>
            <code class="mb-0"><?php echo e($config['table']); ?></code>
            <span class="vr d-none d-sm-inline"></span>
            <span><i class="bi bi-collection me-1"></i><?php echo e(number_format($records->total())); ?> سجل</span>
            <?php if($records->total() > 0): ?>
                <span class="text-muted">عرض <?php echo e($records->firstItem()); ?>–<?php echo e($records->lastItem()); ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.excel-modules.create')); ?>" class="btn btn-primary">
            <i class="bi bi-upload me-1"></i> استيراد
        </a>
        <a href="<?php echo e(route('admin.excel-modules.index')); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-grid me-1"></i> الموديولات
        </a>
    </div>
</div>


<div class="card border-0 shadow-sm mb-4 jm-filter-card">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small text-muted mb-1">بحث</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="<?php echo e(request('q')); ?>"
                           class="form-control" placeholder="بحث في الأعمدة...">
                </div>
            </div>
            <div class="col-md-3 jm-unit-col">
                <label class="form-label small text-muted mb-1">اسم الوحدة</label>
                <input type="text"
                       name="unit_name"
                       id="jm-unit-name"
                       value="<?php echo e(request('unit_name')); ?>"
                       class="form-control"
                       placeholder="مثال: صقر"
                       autocomplete="off"
                       role="combobox"
                       aria-autocomplete="list"
                       aria-expanded="false"
                       aria-controls="jm-unit-dropdown">
                <div id="jm-unit-dropdown"
                     class="dropdown-menu w-100 shadow-sm border-0 mt-1"
                     style="max-height: 260px; overflow-y: auto; display: none;"
                     role="listbox">
                    
                </div>
                <input type="hidden" id="jm-unit-names-data"
                       value="<?php echo e(($unitNames ?? collect())->toJson()); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">لكل صفحة</label>
                <select name="per_page" class="form-select">
                    <?php $__currentLoopData = [25,50,100,200]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($n); ?>" <?php if(request('per_page',50)==$n): echo 'selected'; endif; ?>><?php echo e($n); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary flex-fill" type="submit">
                    <i class="bi bi-funnel me-1"></i> فلترة
                </button>
                <a href="<?php echo e(route('admin.excel-modules.table', $moduleCode)); ?>"
                   class="btn btn-outline-secondary" title="إعادة">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>

        <?php if(request('q') || request('unit_name')): ?>
            <div class="mt-3 d-flex flex-wrap gap-2 align-items-center small">
                <span class="text-muted">فلاتر نشطة:</span>
                <?php if(request('q')): ?>
                    <span class="badge bg-light text-dark border">
                        بحث: <?php echo e(request('q')); ?>

                    </span>
                <?php endif; ?>
                <?php if(request('unit_name')): ?>
                    <span class="badge bg-light text-dark border">
                        وحدة: <?php echo e(request('unit_name')); ?>

                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>


<?php
    $visibleCols = array_values(array_slice($columns, 0, 8));
?>

<div class="card border-0 shadow-sm jm-table-card ao-data-card">
    
    <div class="ao-table-wrap d-none d-md-block">
        <div class="table-responsive ao-table-scroll">
            <table class="table table-hover align-middle mb-0 ao-data-table">
                <thead>
                    <tr>
                        <th class="ao-col-sticky ao-col-id">#</th>
                        <?php $__currentLoopData = $visibleCols; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th class="ao-col <?php echo e($i >= 3 ? 'ao-col-lg-only' : ''); ?> <?php echo e($i >= 5 ? 'ao-col-xl-only' : ''); ?>">
                                <?php echo e(str_replace('_', ' ', $col)); ?>

                            </th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <th class="ao-col-actions text-center">تفاصيل</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $recordUrl = route('admin.excel-modules.table.record', [$moduleCode, $record->id]); ?>
                    <tr class="ao-row-clickable" onclick="window.location='<?php echo e($recordUrl); ?>'">
                        <td class="ao-col-sticky ao-col-id text-muted"><?php echo e($record->id); ?></td>
                        <?php $__currentLoopData = $visibleCols; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $value = (string) ($record->{$col} ?? '—'); ?>
                            <td class="ao-col <?php echo e($i >= 3 ? 'ao-col-lg-only' : ''); ?> <?php echo e($i >= 5 ? 'ao-col-xl-only' : ''); ?>">
                                <span class="ao-cell" title="<?php echo e($value); ?>"><?php echo e(\Illuminate\Support\Str::limit($value, 36)); ?></span>
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td class="ao-col-actions text-center" onclick="event.stopPropagation()">
                            <a class="btn btn-sm btn-outline-primary ao-eye-btn" href="<?php echo e($recordUrl); ?>" title="عرض">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="<?php echo e(count($visibleCols) + 2); ?>" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                            لا توجد بيانات.
                            <a href="<?php echo e(route('admin.excel-modules.create')); ?>">استورد ملفاً</a>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="ao-mobile-list d-md-none">
        <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $recordUrl = route('admin.excel-modules.table.record', [$moduleCode, $record->id]); ?>
            <a href="<?php echo e($recordUrl); ?>" class="ao-mobile-item text-decoration-none">
                <div class="ao-mobile-item-top">
                    <span class="ao-mobile-id">#<?php echo e($record->id); ?></span>
                    <i class="bi bi-chevron-<?php echo e(app()->getLocale()==='ar' ? 'left' : 'right'); ?> text-muted"></i>
                </div>
                <div class="ao-mobile-fields">
                    <?php $__currentLoopData = array_slice($visibleCols, 0, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $value = (string) ($record->{$col} ?? '—'); ?>
                        <div class="ao-mobile-field">
                            <span class="ao-mobile-label"><?php echo e(str_replace('_', ' ', $col)); ?></span>
                            <span class="ao-mobile-value"><?php echo e(\Illuminate\Support\Str::limit($value, 48)); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                لا توجد بيانات.
            </div>
        <?php endif; ?>
    </div>

    <?php if($records->total() > 0): ?>
        <div class="card-footer ao-table-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="small text-muted">
                عرض <?php echo e($records->firstItem()); ?>–<?php echo e($records->lastItem()); ?>

                من <?php echo e(number_format($records->total())); ?>

            </div>
            <div class="ao-pagination">
                <?php echo e($records->links()); ?>

            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    const input    = document.getElementById('jm-unit-name');
    const dropdown = document.getElementById('jm-unit-dropdown');
    const dataEl   = document.getElementById('jm-unit-names-data');
    if (!input || !dropdown || !dataEl) return;

    let names = [];
    try { names = JSON.parse(dataEl.value || '[]'); } catch (e) { names = []; }

    let activeIndex = -1;

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }

    function highlight(name, query) {
        if (!query) return escapeHtml(name);
        const i = name.toLowerCase().indexOf(query.toLowerCase());
        if (i === -1) return escapeHtml(name);
        return escapeHtml(name.slice(0, i))
            + '<mark>' + escapeHtml(name.slice(i, i + query.length)) + '</mark>'
            + escapeHtml(name.slice(i + query.length));
    }

    function render(query) {
        const q = (query || '').trim().toLowerCase();
        const matches = names.filter(n => !q || n.toLowerCase().includes(q)).slice(0, 50);

        if (!matches.length) {
            dropdown.innerHTML = '<div class="jm-empty">لا توجد وحدات مطابقة</div>';
        } else {
            dropdown.innerHTML = matches.map((n, idx) =>
                `<button type="button" class="dropdown-item" data-value="${escapeHtml(n)}" data-index="${idx}">
                    ${highlight(n, q)}
                </button>`
            ).join('');
        }
        activeIndex = -1;
        dropdown.style.display = 'block';
        input.setAttribute('aria-expanded', 'true');
    }

    function hide() {
        dropdown.style.display = 'none';
        input.setAttribute('aria-expanded', 'false');
        activeIndex = -1;
    }

    function setActive(idx) {
        const items = dropdown.querySelectorAll('.dropdown-item');
        items.forEach(el => el.classList.remove('active'));
        if (idx >= 0 && idx < items.length) {
            items[idx].classList.add('active');
            items[idx].scrollIntoView({ block: 'nearest' });
            activeIndex = idx;
        }
    }

    input.addEventListener('focus', () => render(input.value));
    input.addEventListener('input', () => render(input.value));

    input.addEventListener('keydown', (e) => {
        const items = dropdown.querySelectorAll('.dropdown-item');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (dropdown.style.display === 'none') render(input.value);
            setActive(Math.min(activeIndex + 1, items.length - 1));
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setActive(Math.max(activeIndex - 1, 0));
        } else if (e.key === 'Enter') {
            if (activeIndex >= 0 && items[activeIndex]) {
                e.preventDefault();
                input.value = items[activeIndex].dataset.value;
                hide();
            }
        } else if (e.key === 'Escape') {
            hide();
        }
    });

    dropdown.addEventListener('mousedown', (e) => {
        const item = e.target.closest('.dropdown-item');
        if (!item) return;
        e.preventDefault();
        input.value = item.dataset.value;
        hide();
        input.focus();
    });

    input.addEventListener('blur', () => {
        setTimeout(hide, 120);
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#jm-unit-name') && !e.target.closest('#jm-unit-dropdown')) {
            hide();
        }
    });
})();
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (typeof Chart === 'undefined') { console.error('Chart.js missing'); return; }
  const css = getComputedStyle(document.documentElement);
  const p1 = (css.getPropertyValue('--aoi-primary')||'#2563eb').trim();
  const p2 = (css.getPropertyValue('--aoi-primary-2')||'#3b82f6').trim();
  const p3 = (css.getPropertyValue('--aoi-accent')||'#60a5fa').trim();
  const palette = [p1,p2,p3,'#10b981','#f59e0b','#ef4444','#8b5cf6','#64748b'];
  const isRtl = document.documentElement.dir === 'rtl';
  Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
  Chart.defaults.font.size = 11;
  Chart.defaults.color = '#64748b';

  function draw(id, type, labels, data) {
    const el = document.getElementById(id);
    if (!el) return;
    const labs = labels || [];
    const vals = data || [];
    if (!labs.length || !vals.length) {
      const box = el.parentElement;
      if (box) box.innerHTML = '<div class="empty-chart">لا توجد بيانات كافية</div>';
      return;
    }
    const isDonut = type === 'doughnut' || type === 'pie';
    new Chart(el, {
      type: type,
      data: {
        labels: labs,
        datasets: [{
          data: vals,
          backgroundColor: palette.slice(0, Math.max(labs.length, 1)),
          borderWidth: 0,
          borderRadius: type === 'bar' ? 6 : 0,
          cutout: type === 'doughnut' ? '58%' : undefined
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: isDonut, position: 'bottom', rtl: isRtl, labels: { boxWidth: 10, font: { size: 10 } } },
          tooltip: { rtl: isRtl }
        },
        scales: isDonut ? {} : {
          x: { grid: { display: false }, ticks: { font: { size: 9 }, maxRotation: 35 } },
          y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } }, grid: { color: 'rgba(15,23,42,.06)' } }
        }
      }
    });
  }

  draw('modStatusChart', 'doughnut', <?php echo json_encode($chartStatus['labels'] ?? [], 15, 512) ?>, <?php echo json_encode($chartStatus['data'] ?? [], 15, 512) ?>);
  draw('modGovChart', 'bar', <?php echo json_encode($chartGov['labels'] ?? [], 15, 512) ?>, <?php echo json_encode($chartGov['data'] ?? [], 15, 512) ?>);
  draw('modReadyChart', 'doughnut', <?php echo json_encode($chartReadiness['labels'] ?? [], 15, 512) ?>, <?php echo json_encode($chartReadiness['data'] ?? [], 15, 512) ?>);
  
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\pc\Music\Crises-dashboard\resources\views/admin/excel-modules/table.blade.php ENDPATH**/ ?>