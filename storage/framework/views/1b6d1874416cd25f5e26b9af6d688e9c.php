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
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>


<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="kpi-card h-100">
            <div class="kpi-label"><i class="bi bi-database me-1"></i>Records</div>
            <div class="kpi-value"><?php echo e(number_format($totalCount ?? $records->total())); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card h-100">
            <div class="kpi-label"><i class="bi bi-funnel me-1"></i>Filtered</div>
            <div class="kpi-value"><?php echo e(number_format($records->total())); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card h-100">
            <div class="kpi-label"><i class="bi bi-columns me-1"></i>Columns</div>
            <div class="kpi-value"><?php echo e(count($columns)); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card h-100">
            <div class="kpi-label"><i class="bi bi-clock-history me-1"></i>Batches</div>
            <div class="kpi-value"><?php echo e(($batches ?? collect())->count()); ?></div>
        </div>
    </div>
</div>

<?php if(array_sum($chartStatus['data'] ?? []) > 0 || array_sum($chartGov['data'] ?? []) > 0 || array_sum($chartReadiness['data'] ?? []) > 0): ?>
<div class="row g-3 mb-4">
    <?php if(array_sum($chartStatus['data'] ?? []) > 0): ?>
    <div class="col-12 col-md-4">
        <div class="panel-card h-100">
            <div class="panel-head"><i class="bi bi-pie-chart-fill text-success me-1"></i> حالة التشغيل</div>
            <div class="panel-body"><div class="chart-box chart-box-sm"><canvas id="modStatusChart"></canvas></div></div>
        </div>
    </div>
    <?php endif; ?>
    <?php if(array_sum($chartGov['data'] ?? []) > 0): ?>
    <div class="col-12 col-md-4">
        <div class="panel-card h-100">
            <div class="panel-head"><i class="bi bi-geo-alt-fill text-danger me-1"></i> المحافظات</div>
            <div class="panel-body"><div class="chart-box chart-box-sm"><canvas id="modGovChart"></canvas></div></div>
        </div>
    </div>
    <?php endif; ?>
    <?php if(array_sum($chartReadiness['data'] ?? []) > 0): ?>
    <div class="col-12 col-md-4">
        <div class="panel-card h-100">
            <div class="panel-head"><i class="bi bi-shield-check text-warning me-1"></i> الجاهزية</div>
            <div class="panel-body"><div class="chart-box chart-box-sm"><canvas id="modReadyChart"></canvas></div></div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>



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


<div class="card border-0 shadow-sm jm-table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3 text-muted small" style="width:70px">#</th>
                    <?php $__currentLoopData = array_slice($columns, 0, 8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th class="text-nowrap small text-uppercase text-muted">
                            <?php echo e(str_replace('_', ' ', $col)); ?>

                        </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <th class="text-center pe-3" style="width:90px">تفاصيل</th>
                </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $recordUrl = route('admin.excel-modules.table.record', [$moduleCode, $record->id]);
                ?>
                <tr style="cursor:pointer" onclick="window.location='<?php echo e($recordUrl); ?>'">
                    <td class="ps-3 text-muted small"><?php echo e($record->id); ?></td>
                    <?php $__currentLoopData = array_slice($columns, 0, 8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $value = (string) ($record->{$col} ?? '—'); ?>
                        <td class="small" style="max-width:180px">
                            <span class="d-inline-block text-truncate"
                                  style="max-width:180px"
                                  title="<?php echo e($value); ?>">
                                <?php echo e(\Illuminate\Support\Str::limit($value, 40)); ?>

                            </span>
                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <td class="text-center pe-3" onclick="event.stopPropagation()">
                        <a class="btn btn-sm btn-outline-primary"
                           href="<?php echo e($recordUrl); ?>" title="عرض">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="<?php echo e(min(8, count($columns)) + 2); ?>" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        لا توجد بيانات.
                        <a href="<?php echo e(route('admin.excel-modules.create')); ?>" class="ms-1">استورد ملفاً</a>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($records->total() > 0): ?>
        <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="small text-muted">
                عرض <?php echo e($records->firstItem()); ?>–<?php echo e($records->lastItem()); ?>

                من <?php echo e(number_format($records->total())); ?>

            </div>
            <div>
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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/admin/excel-modules/table.blade.php ENDPATH**/ ?>