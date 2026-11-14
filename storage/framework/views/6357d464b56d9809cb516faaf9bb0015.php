<?php $__env->startSection('title', 'تقرير شامل لأصول وطاقات الهيئة العربية للتصنيع'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<style>
/* ============================================================
   MANUFACTURING REPORT — Modern Redesign
   ============================================================ */
.mfg-report {
    font-family: 'Segoe UI', Tahoma, sans-serif;
}

/* ============ HERO ============ */
.mfg-header {
    background: linear-gradient(135deg, #E87524 0%, #CF5F15 55%, #17365D 100%);
    color: #fff;
    padding: 2rem 1.75rem;
    border-radius: 1.35rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 40px -12px rgba(232, 117, 36, 0.35);
}
.mfg-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 15% 25%, rgba(255, 255, 255, 0.15) 0%, transparent 40%),
        radial-gradient(circle at 85% 75%, rgba(255, 255, 255, 0.1) 0%, transparent 45%);
    pointer-events: none;
}
.mfg-header > * { position: relative; z-index: 2; }
.mfg-header h1 {
    font-weight: 800;
    font-size: clamp(1.25rem, 2.5vw, 1.75rem);
    margin-bottom: .35rem;
    line-height: 1.3;
}
.mfg-date-badge {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    background: rgba(255, 255, 255, .18);
    backdrop-filter: blur(8px);
    padding: .45rem 1rem;
    border-radius: 50px;
    font-size: .85rem;
    font-weight: 700;
    border: 1px solid rgba(255, 255, 255, .25);
}

/* ============ STAT CARDS ============ */
.stat-card {
    background: #fff;
    border-radius: 1.15rem;
    padding: 1.25rem 1rem;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
    border: 1px solid rgba(15, 23, 42, 0.04);
    transition: all .25s ease;
    height: 100%;
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 30px rgba(15, 23, 42, 0.1);
}
.stat-icon {
    font-size: 1.75rem;
    margin-bottom: .5rem;
}
.stat-value {
    font-size: 1.65rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    letter-spacing: -0.5px;
}
.stat-label {
    font-size: .78rem;
    color: #64748b;
    font-weight: 600;
    margin-top: .25rem;
}

/* ============ TABS ============ */
.nav-tabs {
    border-bottom: 0;
    gap: .25rem;
    flex-wrap: wrap;
}
.nav-tabs .nav-link {
    border: 1px solid transparent;
    background: #fff;
    color: #64748b;
    font-weight: 600;
    font-size: .85rem;
    padding: .65rem 1.15rem;
    border-radius: .65rem .65rem 0 0;
    transition: all .15s;
}
.nav-tabs .nav-link:hover {
    color: #E87524;
    background: #fff4eb;
}
.nav-tabs .nav-link.active {
    background: #fff;
    color: #E87524;
    border-color: #e2e8f0 #e2e8f0 #fff;
    border-bottom: 3px solid #E87524;
}

/* ============ CHART CONTAINER ============ */
.chart-container {
    background: #fff;
    border-radius: 1.15rem;
    padding: 1.25rem;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
    border: 1px solid rgba(15, 23, 42, 0.04);
    margin-bottom: 0;
    height: 100%;
    position: relative;
}
.chart-container canvas {
    max-height: 280px;
    width: 100% !important;
}

/* ============ TOTAL PILL ============ */
.chart-total-pill {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    background: rgba(232, 117, 36, 0.1);
    color: #E87524;
    padding: .3rem .75rem;
    border-radius: 50px;
    font-size: .78rem;
    font-weight: 700;
    margin-bottom: .5rem;
}
.chart-total-pill i { font-size: .85rem; }

/* ============ LEGEND ============ */
.chart-legend {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: .6rem;
    margin-top: .75rem;
    font-size: .78rem;
    color: #475569;
}
.chart-legend .legend-item {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
}
.chart-legend .legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 3px;
    display: inline-block;
}

/* ============ EXPORT BTN ============ */
.chart-export-btn {
    position: absolute;
    top: .75rem;
    inset-inline-start: .75rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #475569;
    width: 30px;
    height: 30px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .15s;
    font-size: .75rem;
    z-index: 5;
}
.chart-export-btn:hover {
    background: #E87524;
    color: #fff;
    border-color: #E87524;
}

/* ============ INSIGHT CARD ============ */
.insight-card {
    background: #fff;
    border-radius: 1.15rem;
    padding: 1.25rem;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
    border: 1px solid rgba(15, 23, 42, 0.04);
    border-inline-start: 4px solid #E87524;
    height: 100%;
}
.insight-card ul { margin-bottom: 0; }
.insight-card li {
    font-size: .85rem;
    line-height: 1.5;
    padding: .4rem 0;
    border-bottom: 1px dashed #f1f5f9;
}
.insight-card li:last-child { border-bottom: 0; }

/* ============ TABLE ============ */
.table-container {
    background: #fff;
    border-radius: 1.15rem;
    padding: 1.25rem;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
    border: 1px solid rgba(15, 23, 42, 0.04);
    overflow-x: auto;
    margin-bottom: 1.5rem;
}
.table thead th {
    background: rgba(232, 117, 36, 0.05);
    color: #E87524;
    font-weight: 700;
    font-size: .72rem;
    text-transform: uppercase;
    letter-spacing: .3px;
    white-space: nowrap;
    border-bottom: 2px solid #e2e8f0;
    padding: .75rem .65rem;
}
.table tbody td {
    vertical-align: middle;
    font-size: .82rem;
    padding: .65rem;
    white-space: nowrap;
}
.table tbody tr:hover { background: #f8fafc; }

/* ============ BADGES ============ */
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: .2rem;
    padding: .3rem .65rem;
    border-radius: 50px;
    font-weight: 700;
    font-size: .72rem;
}
.badge-good    { background: #d1fae5; color: #065f46; }
.badge-warn    { background: #fef3c7; color: #92400e; }
.badge-bad     { background: #fee2e2; color: #991b1b; }
.badge-neutral { background: #f1f5f9; color: #475569; }

/* ============ INVENTORY PROGRESS ============ */
.inventory-progress {
    height: 6px;
    background: #f1f5f9;
    border-radius: 50px;
    overflow: hidden;
    margin-bottom: .15rem;
    min-width: 60px;
}
.inventory-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #34D399);
    border-radius: 50px;
    transition: width .6s ease;
}

/* ============ SEARCH ============ */
.search-box {
    border-radius: 50px;
    padding: .55rem 1.15rem;
    border: 1px solid #e2e8f0;
    font-size: .85rem;
}
.search-box:focus {
    box-shadow: 0 0 0 3px rgba(232, 117, 36, .15);
    border-color: #E87524;
    outline: none;
}

.clickable-row { cursor: pointer; }
.clickable-row:hover { background: rgba(232, 117, 36, 0.04) !important; }

/* ============ RESPONSIVE ============ */
@media (max-width: 991.98px) {
    .chart-container canvas { max-height: 240px; }
}
@media (max-width: 767.98px) {
    .mfg-header { padding: 1.5rem 1.25rem; border-radius: 1rem; }
    .stat-value { font-size: 1.3rem; }
    .stat-icon { font-size: 1.4rem; }
    .chart-container { padding: 1rem; }
    .chart-container canvas { max-height: 220px; }
    .nav-tabs .nav-link { font-size: .75rem; padding: .5rem .75rem; }
}
@media (max-width: 575.98px) {
    .stat-value { font-size: 1.2rem; }
    .stat-label { font-size: .7rem; }
    .nav-tabs { flex-wrap: nowrap; overflow-x: auto; padding-bottom: .25rem; }
    .nav-tabs .nav-link { white-space: nowrap; }
}

/* ============ DARK MODE ============ */
.admin-wrapper.dark-mode .stat-card,
.admin-wrapper.dark-mode .chart-container,
.admin-wrapper.dark-mode .insight-card,
.admin-wrapper.dark-mode .table-container,
.admin-wrapper.dark-mode .nav-tabs .nav-link {
    background: #1e293b;
    border-color: #334155;
}
.admin-wrapper.dark-mode .stat-value,
.admin-wrapper.dark-mode .table thead th,
.admin-wrapper.dark-mode .chart-container h5 {
    color: #f1f5f9;
}
.admin-wrapper.dark-mode .stat-label { color: #94a3b8; }
.admin-wrapper.dark-mode .table tbody td { color: #cbd5e1; }
.admin-wrapper.dark-mode .table tbody tr:hover { background: #263750; }
.admin-wrapper.dark-mode .nav-tabs .nav-link.active {
    color: #FF9A56;
    border-bottom-color: #FF9A56;
}
.admin-wrapper.dark-mode .inventory-progress { background: #0f172a; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="mfg-report" id="mfgReportApp">

    
    <header class="mfg-header">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1><i class="fas fa-industry me-2"></i> تقرير شامل لأصول وطاقات الهيئة العربية للتصنيع</h1>
                    <p class="mb-0 mt-2 opacity-75">تحليل تفاعلي حسب الوحدات - ملخص تنفيذي</p>
                </div>
                <div>
                    <span class="mfg-date-badge">
                        <i class="far fa-calendar-alt me-2"></i>
                        <?php echo e(now()->translatedFormat('d F Y')); ?>

                    </span>
                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid">

        
        <div class="row g-3 mb-4">
            <?php
                $stats = [
                    ['icon'=>'fa-cogs','value'=>'1,175','label'=>'إجمالي الماكينات','delay'=>0,'color'=>'#E87524'],
                    ['icon'=>'fa-users','value'=>'8,093','label'=>'إجمالي العاملين','delay'=>50,'color'=>'#5B9BD5'],
                    ['icon'=>'fa-truck','value'=>'631','label'=>'إجمالي المركبات','delay'=>100,'color'=>'#10b981'],
                    ['icon'=>'fa-warehouse','value'=>'117','label'=>'إجمالي المخازن','delay'=>150,'color'=>'#f59e0b'],
                    ['icon'=>'fa-building','value'=>'457','label'=>'إجمالي المنشآت','delay'=>200,'color'=>'#8b5cf6'],
                    ['icon'=>'fa-boxes-stacked','value'=>'35,167','label'=>'أصناف المخزون','delay'=>250,'color'=>'#06b6d4'],
                ];
            ?>
            <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="stat-card text-center" data-aos="fade-up" data-aos-delay="<?php echo e($s['delay']); ?>">
                        <div class="stat-icon" style="color: <?php echo e($s['color']); ?>;"><i class="fas <?php echo e($s['icon']); ?>"></i></div>
                        <div class="stat-value"><?php echo e($s['value']); ?></div>
                        <div class="stat-label"><?php echo e($s['label']); ?></div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <ul class="nav nav-tabs justify-content-center mb-4" id="mainTabs" role="tablist" data-aos="fade-up">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-overview" type="button"><i class="fas fa-chart-pie me-1"></i> نظرة عامة</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-machines" type="button"><i class="fas fa-cogs me-1"></i> الماكينات</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-lines" type="button"><i class="fas fa-project-diagram me-1"></i> خطوط الإنتاج</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-inventory" type="button"><i class="fas fa-boxes me-1"></i> المخزون</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-hr" type="button"><i class="fas fa-user-tie me-1"></i> الموارد البشرية</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-vehicles" type="button"><i class="fas fa-truck me-1"></i> المركبات</button></li>
        </ul>

        <div class="tab-content" id="mainTabsContent">

            
            <div class="tab-pane fade show active" id="tab-overview">
                <div class="row g-4 mb-4">
                    <div class="col-lg-7">
                        <div class="chart-container" data-aos="fade-left">
                            <button class="chart-export-btn" onclick="exportChart('machines')" title="تحميل PNG"><i class="fas fa-download"></i></button>
                            <h5 class="fw-bold mb-2"><i class="fas fa-chart-bar me-2 text-primary"></i> عدد الماكينات حسب الوحدة</h5>
                            <div id="totalMachines" class="chart-total-pill"></div>
                            <canvas id="chartMachines"></canvas>
                            <div class="chart-legend" id="legendMachines"></div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="chart-container" data-aos="fade-right">
                            <button class="chart-export-btn" onclick="exportChart('validity')" title="تحميل PNG"><i class="fas fa-download"></i></button>
                            <h5 class="fw-bold mb-2"><i class="fas fa-chart-line me-2 text-success"></i> نسبة صلاحية الماكينات (%)</h5>
                            <div id="totalValidity" class="chart-total-pill"></div>
                            <canvas id="chartValidity"></canvas>
                            <div class="chart-legend" id="legendValidity"></div>
                        </div>
                    </div>
                </div>
                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <div class="chart-container" data-aos="fade-up">
                            <button class="chart-export-btn" onclick="exportChart('employees')" title="تحميل PNG"><i class="fas fa-download"></i></button>
                            <h5 class="fw-bold mb-2"><i class="fas fa-users me-2 text-info"></i> توزيع العاملين حسب الوحدة</h5>
                            <div id="totalEmployees" class="chart-total-pill"></div>
                            <canvas id="chartEmployees"></canvas>
                            <div class="chart-legend" id="legendEmployees"></div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="insight-card" data-aos="fade-up" data-aos-delay="100">
                            <h5 class="fw-bold mb-3"><i class="fas fa-lightbulb me-2 text-warning"></i> ملخص تنفيذي سريع</h5>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check-circle text-success me-2"></i> أكبر الوحدات (ماكينات): <strong>قادر (471)</strong> ← الطائرات (422) ← حلوان (208)</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i> أفضل حالة ماكينات: <strong>أتيكو 100%</strong> | الطائرات 90.5%</li>
                                <li><i class="fas fa-chart-line text-primary me-2"></i> أعلى استغلال طاقة: <strong>حلوان ≈87%</strong> | الطائرات ≈58%</li>
                                <li><i class="fas fa-project-diagram text-warning me-2"></i> خطوط الإنتاج: <strong>105 خط</strong>, قادر 55.6% فقط</li>
                                <li><i class="fas fa-gas-pump text-danger me-2"></i> مخزون حرج: بنزين المحركات 5 أيام، بنزين/سولار الطائرات 7-8 أيام</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="table-container" data-aos="fade-up">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h5 class="fw-bold mb-0"><i class="fas fa-table me-2 text-primary"></i> جدول النظرة العامة الكامل</h5>
                        <input type="text" id="overviewSearch" class="form-control search-box" style="max-width: 250px;" placeholder="ابحث عن وحدة...">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm" id="overviewTable">
                            <thead>
                                <tr>
                                    <th>الوحدة</th><th>عدد الماكينات</th><th>نسبة الصلاحية %</th>
                                    <th>خطوط الإنتاج</th><th>نسبة الخطوط العاملة %</th>
                                    <th>متوسط استغلال الخطوط %</th><th>العاملين</th>
                                    <th>المركبات</th><th>مركبات صالحة</th>
                                    <th>المخازن</th><th>المنشآت</th><th>منتجات طاقات</th>
                                </tr>
                            </thead>
                            <tbody id="overviewTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="tab-pane fade" id="tab-machines">
                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <div class="chart-container" data-aos="fade-left">
                            <button class="chart-export-btn" onclick="exportChart('machineStatus')" title="تحميل PNG"><i class="fas fa-download"></i></button>
                            <h5 class="fw-bold mb-2"><i class="fas fa-cogs me-2 text-primary"></i> حالة الماكينات</h5>
                            <div id="totalMachineStatus" class="chart-total-pill"></div>
                            <canvas id="chartMachineStatus"></canvas>
                            <div class="chart-legend" id="legendMachineStatus"></div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="chart-container" data-aos="fade-right">
                            <button class="chart-export-btn" onclick="exportChart('machineUtil')" title="تحميل PNG"><i class="fas fa-download"></i></button>
                            <h5 class="fw-bold mb-2"><i class="fas fa-percent me-2 text-success"></i> نسبة الاستغلال %</h5>
                            <div id="totalMachineUtil" class="chart-total-pill"></div>
                            <canvas id="chartMachineUtil"></canvas>
                            <div class="chart-legend" id="legendMachineUtil"></div>
                        </div>
                    </div>
                </div>
                <div class="table-container" data-aos="fade-up">
                    <h5 class="fw-bold mb-3"><i class="fas fa-table me-2 text-primary"></i> تفاصيل الماكينات</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>الوحدة</th><th>العدد</th><th>صالح</th><th>غير صالح</th>
                                    <th>غير محدد</th><th>طاقة متاحة</th><th>طاقة مستغلة</th>
                                    <th>معدل التحميل</th><th>الصلاحية %</th><th>الاستغلال %</th>
                                </tr>
                            </thead>
                            <tbody id="machinesTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="tab-pane fade" id="tab-lines">
                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <div class="chart-container" data-aos="fade-left">
                            <button class="chart-export-btn" onclick="exportChart('linesCount')" title="تحميل PNG"><i class="fas fa-download"></i></button>
                            <h5 class="fw-bold mb-2"><i class="fas fa-project-diagram me-2 text-primary"></i> عدد خطوط الإنتاج</h5>
                            <div id="totalLinesCount" class="chart-total-pill"></div>
                            <canvas id="chartLinesCount"></canvas>
                            <div class="chart-legend" id="legendLinesCount"></div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="chart-container" data-aos="fade-right">
                            <button class="chart-export-btn" onclick="exportChart('linesWorking')" title="تحميل PNG"><i class="fas fa-download"></i></button>
                            <h5 class="fw-bold mb-2"><i class="fas fa-check-circle me-2 text-success"></i> نسبة الخطوط العاملة %</h5>
                            <div id="totalLinesWorking" class="chart-total-pill"></div>
                            <canvas id="chartLinesWorking"></canvas>
                            <div class="chart-legend" id="legendLinesWorking"></div>
                        </div>
                    </div>
                </div>
                <div class="table-container" data-aos="fade-up">
                    <h5 class="fw-bold mb-3"><i class="fas fa-table me-2 text-primary"></i> خطوط الإنتاج</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr><th>الوحدة</th><th>العدد</th><th>يعمل</th><th>لا يعمل</th><th>تحت التجهيز</th><th>متوسط الاستغلال</th><th>نسبة العاملة %</th></tr>
                            </thead>
                            <tbody id="linesTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="tab-pane fade" id="tab-inventory">

                
                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <div class="chart-container" data-aos="fade-left">
                            <button class="chart-export-btn" onclick="exportChart('inventoryItems')" title="تحميل PNG">
                                <i class="fas fa-download"></i>
                            </button>
                            <h5 class="fw-bold mb-2">
                                <i class="fas fa-boxes me-2 text-primary"></i>
                                عدد الأصناف حسب الوحدة
                            </h5>
                            <div id="totalInventoryItems" class="chart-total-pill"></div>
                            <canvas id="chartInventoryItems"></canvas>
                            <div class="chart-legend" id="legendInventoryItems"></div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="chart-container" data-aos="fade-right">
                            <button class="chart-export-btn" onclick="exportChart('inventoryStatus')" title="تحميل PNG">
                                <i class="fas fa-download"></i>
                            </button>
                            <h5 class="fw-bold mb-2">
                                <i class="fas fa-triangle-exclamation me-2 text-warning"></i>
                                أصناف موجبة vs صفر
                            </h5>
                            <div id="totalInventoryStatus" class="chart-total-pill"></div>
                            <canvas id="chartInventoryStatus"></canvas>
                            <div class="chart-legend" id="legendInventoryStatus"></div>
                        </div>
                    </div>
                </div>

                
                <div class="row g-4 mb-4">
                    <div class="col-lg-7">
                        <div class="chart-container" data-aos="fade-left">
                            <button class="chart-export-btn" onclick="exportChart('fuelDays')" title="تحميل PNG">
                                <i class="fas fa-download"></i>
                            </button>
                            <h5 class="fw-bold mb-2">
                                <i class="fas fa-gas-pump me-2 text-danger"></i>
                                أيام كفاية الوقود
                            </h5>
                            <div id="totalFuelDays" class="chart-total-pill"></div>
                            <canvas id="chartFuelDays"></canvas>
                            <div class="chart-legend" id="legendFuelDays"></div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="insight-card" data-aos="fade-right">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                                ملاحظات حرجة
                            </h5>
                            <ul class="list-unstyled mb-0" id="inventoryInsights">
                                <li class="text-muted">جاري التحميل...</li>
                            </ul>
                        </div>
                    </div>
                </div>

                
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="table-container" data-aos="fade-up">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-table me-2 text-primary"></i>
                                    ملخص المخزون
                                </h5>
                                <span class="small text-muted">مرتب حسب عدد الأصناف</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>الوحدة</th>
                                            <th class="text-center">الأصناف</th>
                                            <th class="text-center">موجبة</th>
                                            <th class="text-center">صفر</th>
                                            <th class="text-center">نسبة التغطية</th>
                                            <th class="text-center">أيام الكفاية</th>
                                            <th class="text-center">الحالة</th>
                                        </tr>
                                    </thead>
                                    <tbody id="inventorySummaryBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="table-container" data-aos="fade-up">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-gas-pump me-2 text-danger"></i>
                                    الوقود الحرج
                                </h5>
                                <span class="small text-muted">مرتب حسب أيام الكفاية</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>الصنف</th>
                                            <th class="text-center">الكمية</th>
                                            <th class="text-center">أيام</th>
                                            <th class="text-center">الوحدة</th>
                                            <th class="text-center">الجاهزية</th>
                                        </tr>
                                    </thead>
                                    <tbody id="fuelTableBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="tab-pane fade" id="tab-hr">
                <div class="row g-4 mb-4">
                    <div class="col-lg-7">
                        <div class="chart-container" data-aos="fade-left">
                            <button class="chart-export-btn" onclick="exportChart('hrEmployees')" title="تحميل PNG"><i class="fas fa-download"></i></button>
                            <h5 class="fw-bold mb-2"><i class="fas fa-users me-2 text-primary"></i> إجمالي العاملين (أعلى 8)</h5>
                            <div id="totalHREmployees" class="chart-total-pill"></div>
                            <canvas id="chartHREmployees"></canvas>
                            <div class="chart-legend" id="legendHREmployees"></div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="chart-container" data-aos="fade-right">
                            <button class="chart-export-btn" onclick="exportChart('hrEmergency')" title="تحميل PNG"><i class="fas fa-download"></i></button>
                            <h5 class="fw-bold mb-2"><i class="fas fa-user-shield me-2 text-warning"></i> المتاحون للطوارئ (أعلى 8)</h5>
                            <div id="totalHREmergency" class="chart-total-pill"></div>
                            <canvas id="chartHREmergency"></canvas>
                            <div class="chart-legend" id="legendHREmergency"></div>
                        </div>
                    </div>
                </div>
                <div class="table-container" data-aos="fade-up">
                    <h5 class="fw-bold mb-3"><i class="fas fa-table me-2 text-primary"></i> الموارد البشرية</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead><tr><th>الوحدة</th><th>التخصصات</th><th>إجمالي العاملين</th><th>متاحون للطوارئ</th></tr></thead>
                            <tbody id="hrTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="tab-pane fade" id="tab-vehicles">
                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <div class="chart-container" data-aos="fade-left">
                            <button class="chart-export-btn" onclick="exportChart('vehiclesCount')" title="تحميل PNG"><i class="fas fa-download"></i></button>
                            <h5 class="fw-bold mb-2"><i class="fas fa-truck me-2 text-primary"></i> عدد المركبات</h5>
                            <div id="totalVehiclesCount" class="chart-total-pill"></div>
                            <canvas id="chartVehiclesCount"></canvas>
                            <div class="chart-legend" id="legendVehiclesCount"></div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="chart-container" data-aos="fade-right">
                            <button class="chart-export-btn" onclick="exportChart('vehiclesStatus')" title="تحميل PNG"><i class="fas fa-download"></i></button>
                            <h5 class="fw-bold mb-2"><i class="fas fa-check-circle me-2 text-success"></i> حالة المركبات</h5>
                            <div id="totalVehiclesStatus" class="chart-total-pill"></div>
                            <canvas id="chartVehiclesStatus"></canvas>
                            <div class="chart-legend" id="legendVehiclesStatus"></div>
                        </div>
                    </div>
                </div>
                <div class="table-container" data-aos="fade-up">
                    <h5 class="fw-bold mb-3"><i class="fas fa-table me-2 text-primary"></i> المركبات حسب الوحدة</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead><tr><th>الوحدة</th><th>الإجمالي</th><th>صالح</th><th>عاطل</th><th>مكهين</th></tr></thead>
                            <tbody id="vehiclesTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <footer class="text-center mt-5 pt-4 border-top text-muted small">
            <p class="mb-0">© <?php echo e(date('Y')); ?> الهيئة العربية للتصنيع - تقرير تفاعلي</p>
        </footer>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
(function () {
    'use strict';

    const DATA_URL = <?php echo json_encode(route('admin.manufacturing-report.data'), 15, 512) ?>;

    let DATA = null;
    const charts = {};

    const fmt = (n) => (n === null || n === undefined) ? '-' : Number(n).toLocaleString('en-US');
    const sum = (arr, key) => (arr || []).reduce((s, x) => s + (Number(x[key]) || 0), 0);

    function tooltipWithTotal(total, unit = '') {
        return {
            callbacks: {
                label: function (ctx) {
                    const val = Number(ctx.raw) || 0;
                    const pct = total ? ((val / total) * 100).toFixed(1) : 0;
                    return ` ${ctx.label}: ${val.toLocaleString('en-US')}${unit} (${pct}%)`;
                }
            }
        };
    }

    function titleWithTotal(titleText, total, unit = '') {
        return {
            display: true,
            text: `${titleText} — الإجمالي: ${total.toLocaleString('en-US')}${unit}`,
            font: { size: 13, weight: 'bold' },
            color: '#0b2b5c',
            padding: { bottom: 12 }
        };
    }

    function setTotalPill(elementId, label, total, unit = '') {
        const el = document.getElementById(elementId);
        if (!el) return;
        el.innerHTML = `<i class="fas fa-calculator"></i> ${label}: <strong>${total.toLocaleString('en-US')}${unit}</strong>`;
    }

    function renderLegend(containerId, items) {
        const el = document.getElementById(containerId);
        if (!el) return;
        el.innerHTML = items.map(it => `
            <span class="legend-item">
                <span class="legend-dot" style="background:${it.color};"></span>
                <span>${it.label}</span>
            </span>
        `).join('');
    }

    async function loadData() {
        try {
            const res = await fetch(DATA_URL, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            DATA = await res.json();
            console.log('DATA loaded:', DATA);
            initAll();
        } catch (err) {
            console.error('Failed to load report data', err);
            alert('تعذر تحميل بيانات التقرير: ' + err.message);
        }
    }

    // ==================== TABLES ====================
    function renderOverviewTable(filter = '') {
        const tbody = document.getElementById('overviewTableBody');
        if (!tbody) return;
        const rows = (DATA.overview || []).filter(r => r.unit.includes(filter));
        tbody.innerHTML = rows.map(r => `
            <tr class="clickable-row">
                <td><strong>${r.unit}</strong></td>
                <td>${fmt(r.machines)}</td>
                <td>${r.validity ? r.validity + '%' : '-'}</td>
                <td>${r.lines}</td>
                <td>${r.workingLines ? r.workingLines + '%' : '-'}</td>
                <td>${r.avgLineUtil ? Number(r.avgLineUtil).toFixed(2) + '%' : '-'}</td>
                <td>${fmt(r.workers)}</td>
                <td>${r.vehicles}</td>
                <td>${r.validVehicles}</td>
                <td>${r.warehouses}</td>
                <td>${r.facilities}</td>
                <td>${r.products}</td>
            </tr>`).join('');
    }

    function renderMachinesTable() {
        const tbody = document.getElementById('machinesTableBody');
        if (!tbody) return;
        tbody.innerHTML = (DATA.machines || []).map(r => {
            const vc = r.validityPct >= 80 ? 'badge-good' : (r.validityPct >= 50 ? 'badge-warn' : 'badge-bad');
            const uc = r.utilPct >= 70 ? 'badge-good' : (r.utilPct >= 40 ? 'badge-warn' : 'badge-bad');
            return `<tr>
                <td><strong>${r.unit}</strong></td>
                <td>${r.total}</td><td>${r.valid}</td><td>${r.invalid}</td><td>${r.unknown}</td>
                <td>${fmt(r.availHours)}</td><td>${fmt(r.usedHours)}</td>
                <td>${r.loadRate !== null ? Number(r.loadRate).toFixed(4) : '-'}</td>
                <td><span class="badge-status ${vc}">${r.validityPct !== null ? r.validityPct + '%' : '-'}</span></td>
                <td><span class="badge-status ${uc}">${r.utilPct !== null ? r.utilPct + '%' : '-'}</span></td>
            </tr>`;
        }).join('');
    }

    function renderLinesTable() {
        const tbody = document.getElementById('linesTableBody');
        if (!tbody) return;
        tbody.innerHTML = (DATA.lines || []).map(r => {
            const c = r.workingPct >= 90 ? 'badge-good' : (r.workingPct >= 50 ? 'badge-warn' : 'badge-bad');
            return `<tr>
                <td><strong>${r.unit}</strong></td><td>${r.total}</td>
                <td>${r.working}</td><td>${r.notWorking}</td><td>${r.underPrep}</td>
                <td>${r.avgUtil !== null ? Number(r.avgUtil).toFixed(2) + '%' : '-'}</td>
                <td><span class="badge-status ${c}">${r.workingPct}%</span></td>
            </tr>`;
        }).join('');
    }

    // ✅ Inventory Tables — FLEXIBLE READING
    function renderInventoryTables() {
        const invSource = Array.isArray(DATA.inventory)
            ? DATA.inventory
            : (DATA.inventory?.summary || []);

        const invRows = [...invSource].sort((a, b) => b.items - a.items);

        const sTbody = document.getElementById('inventorySummaryBody');
        if (sTbody) {
            if (invRows.length === 0) {
                sTbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x d-block mb-2 opacity-50"></i>
                            لا توجد بيانات مخزون
                        </td>
                    </tr>`;
            } else {
                sTbody.innerHTML = invRows.map(r => {
                    const positivePct = r.items > 0 ? (r.positive / r.items) * 100 : 0;
                    const zeroPct = r.items > 0 ? (r.zero / r.items) * 100 : 0;

                    let statusBadge, statusClass;
                    if (zeroPct === 0) {
                        statusBadge = 'كامل'; statusClass = 'badge-good';
                    } else if (zeroPct < 20) {
                        statusBadge = 'جيد'; statusClass = 'badge-good';
                    } else if (zeroPct < 50) {
                        statusBadge = 'ناقص'; statusClass = 'badge-warn';
                    } else {
                        statusBadge = 'حرج'; statusClass = 'badge-bad';
                    }

                    const daysClass = r.avgDays < 15 ? 'badge-bad'
                                    : (r.avgDays < 30 ? 'badge-warn' : 'badge-good');

                    return `<tr>
                        <td><strong>${r.unit}</strong></td>
                        <td class="text-center">${fmt(r.items)}</td>
                        <td class="text-center"><span class="badge-status badge-good">${fmt(r.positive)}</span></td>
                        <td class="text-center"><span class="badge-status ${r.zero > 0 ? 'badge-bad' : 'badge-good'}">${fmt(r.zero)}</span></td>
                        <td class="text-center">
                            <div class="inventory-progress">
                                <div class="inventory-progress-fill" style="width: ${positivePct}%" title="${positivePct.toFixed(1)}%"></div>
                            </div>
                            <small class="text-muted">${positivePct.toFixed(0)}%</small>
                        </td>
                        <td class="text-center"><span class="badge-status ${daysClass}">${r.avgDays} يوم</span></td>
                        <td class="text-center"><span class="badge-status ${statusClass}">${statusBadge}</span></td>
                    </tr>`;
                }).join('');
            }
        }

        const fuelSource = Array.isArray(DATA.fuel) ? DATA.fuel : [];
        const fuelRows = [...fuelSource].sort((a, b) => a.days - b.days);

        const fTbody = document.getElementById('fuelTableBody');
        if (fTbody) {
            if (fuelRows.length === 0) {
                fTbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x d-block mb-2 opacity-50"></i>
                            لا توجد بيانات وقود
                        </td>
                    </tr>`;
            } else {
                fTbody.innerHTML = fuelRows.map(r => {
                    const daysClass = r.days < 7 ? 'badge-bad'
                                    : (r.days < 15 ? 'badge-warn' : 'badge-good');

                    const readinessClass = r.readiness === 'جاهز' ? 'badge-good'
                                         : (r.readiness === 'مرتفع' ? 'badge-warn' : 'badge-bad');

                    const fuelIcons = {
                        'بنزين': 'fa-gas-pump',
                        'سولار': 'fa-oil-can',
                        'jet-A1': 'fa-plane',
                    };
                    const icon = fuelIcons[r.item] || 'fa-gas-pump';

                    return `<tr>
                        <td><i class="fas ${icon} me-1 text-muted"></i><strong>${r.item}</strong></td>
                        <td class="text-center">${fmt(r.qty)} <small class="text-muted">${r.unit}</small></td>
                        <td class="text-center"><span class="badge-status ${daysClass}">${r.days < 7 ? '🔴 ' : r.days < 15 ? '🟡 ' : '🟢 '}${r.days} يوم</span></td>
                        <td class="text-center small text-muted">${r.org}</td>
                        <td class="text-center"><span class="badge-status ${readinessClass}">${r.readiness}</span></td>
                    </tr>`;
                }).join('');
            }
        }

        renderInventoryInsights(invRows, fuelRows);
    }

    function renderInventoryInsights(invRows, fuelRows) {
        const wrap = document.getElementById('inventoryInsights');
        if (!wrap) return;

        const insights = [];

        const topUnit = invRows[0];
        if (topUnit) {
            insights.push({
                icon: 'fa-warehouse',
                color: 'primary',
                text: `<strong>${topUnit.unit}</strong> الأكبر: ${fmt(topUnit.items)} صنف`
            });
        }

        const worstUnit = [...invRows]
            .filter(r => r.zero > 0)
            .sort((a, b) => (b.zero / b.items) - (a.zero / a.items))[0];
        if (worstUnit) {
            const pct = ((worstUnit.zero / worstUnit.items) * 100).toFixed(0);
            insights.push({
                icon: 'fa-times-circle',
                color: 'danger',
                text: `<strong>${worstUnit.unit}</strong>: ${worstUnit.zero} صنف صفر (${pct}%)`
            });
        }

        const criticalFuel = fuelRows.filter(r => r.days < 7);
        if (criticalFuel.length > 0) {
            const names = criticalFuel.map(f => `${f.item} (${f.days}ي)`).join('، ');
            insights.push({
                icon: 'fa-gas-pump',
                color: 'danger',
                text: `وقود حرج: <strong>${names}</strong>`
            });
        }

        const watchFuel = fuelRows.filter(r => r.days >= 7 && r.days < 15);
        if (watchFuel.length > 0) {
            const names = watchFuel.map(f => `${f.item} (${f.days}ي)`).join('، ');
            insights.push({
                icon: 'fa-exclamation-triangle',
                color: 'warning',
                text: `تحت المراقبة: <strong>${names}</strong>`
            });
        }

        const goodFuel = fuelRows.filter(r => r.days >= 30);
        if (goodFuel.length > 0) {
            const names = goodFuel.map(f => `${f.item} (${f.days}ي)`).join('، ');
            insights.push({
                icon: 'fa-check-circle',
                color: 'success',
                text: `كفاية جيدة: <strong>${names}</strong>`
            });
        }

        if (invRows.length > 0) {
            const avgDays = (invRows.reduce((s, r) => s + r.avgDays, 0) / invRows.length).toFixed(0);
            insights.push({
                icon: 'fa-calendar-check',
                color: 'info',
                text: `متوسط أيام الكفاية العامة: <strong>${avgDays} يوم</strong>`
            });
        }

        wrap.innerHTML = insights.map(i => `
            <li class="mb-2">
                <i class="fas ${i.icon} text-${i.color} me-2"></i>
                ${i.text}
            </li>
        `).join('');
    }

    function renderHRTable() {
        const tbody = document.getElementById('hrTableBody');
        if (!tbody) return;
        tbody.innerHTML = (DATA.hr || []).map(r => `
            <tr>
                <td><strong>${r.unit}</strong></td>
                <td>${r.specialties}</td>
                <td>${fmt(r.totalWorkers)}</td>
                <td>${fmt(r.emergency)}</td>
            </tr>`).join('');
    }

    function renderVehiclesTable() {
        const tbody = document.getElementById('vehiclesTableBody');
        if (!tbody) return;
        tbody.innerHTML = (DATA.vehicles || []).map(r => {
            const validPct = r.total ? (r.valid / r.total) * 100 : 0;
            const c = validPct >= 90 ? 'badge-good' : (validPct >= 70 ? 'badge-warn' : 'badge-bad');
            return `<tr>
                <td><strong>${r.unit}</strong></td><td>${r.total}</td>
                <td><span class="badge-status ${c}">${r.valid}</span></td>
                <td>${r.broken}</td><td>${r.scrapped}</td>
            </tr>`;
        }).join('');
    }

    // ==================== CHARTS ====================
    function destroyChart(key) { if (charts[key]) { charts[key].destroy(); charts[key] = null; } }

    function buildCharts() {
        const d = DATA;

        // 1. MACHINES COUNT
        destroyChart('machines');
        const machinesTotal = sum(d.machines, 'total');
        const machinesPalette = ['#E87524', '#5B9BD5', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4'];
        setTotalPill('totalMachines', 'إجمالي الماكينات المسجلة', machinesTotal, ' ماكينة');
        renderLegend('legendMachines',
            (d.machines || []).map((x, i) => ({ label: `${x.unit} (${x.total})`, color: machinesPalette[i % machinesPalette.length] }))
        );
        charts.machines = new Chart(document.getElementById('chartMachines'), {
            type: 'bar',
            data: {
                labels: (d.machines || []).map(x => x.unit),
                datasets: [{
                    label: 'عدد الماكينات',
                    data: (d.machines || []).map(x => x.total),
                    backgroundColor: machinesPalette,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    title: titleWithTotal('عدد الماكينات حسب الوحدة', machinesTotal, ' ماكينة'),
                    tooltip: tooltipWithTotal(machinesTotal, ' ماكينة')
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#e9ecef' }, title: { display: true, text: 'عدد الماكينات' } }
                }
            }
        });

        // 2. VALIDITY
        destroyChart('validity');
        const validityRows = (d.machines || []).filter(x => x.validityPct !== null && x.validityPct > 0);
        const validityAvg = validityRows.length ? (validityRows.reduce((s, x) => s + x.validityPct, 0) / validityRows.length) : 0;
        const validityPalette = ['#10b981', '#5B9BD5', '#f59e0b', '#ef4444', '#8b5cf6'];
        setTotalPill('totalValidity', 'متوسط نسبة الصلاحية العامة', validityAvg.toFixed(1), '%');
        renderLegend('legendValidity',
            validityRows.map((x, i) => ({ label: `${x.unit} (${x.validityPct}%)`, color: validityPalette[i % validityPalette.length] }))
        );
        charts.validity = new Chart(document.getElementById('chartValidity'), {
            type: 'doughnut',
            data: {
                labels: validityRows.map(x => `${x.unit} (${x.validityPct}%)`),
                datasets: [{
                    data: validityRows.map(x => x.validityPct),
                    backgroundColor: validityPalette,
                    borderWidth: 2, borderColor: '#fff'
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: `نسبة صلاحية الماكينات — متوسط: ${validityAvg.toFixed(1)}%`,
                        font: { size: 13, weight: 'bold' }, color: '#0b2b5c', padding: { bottom: 12 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                const val = Number(ctx.raw) || 0;
                                return ` ${ctx.label}: ${val}%`;
                            }
                        }
                    }
                },
                cutout: '55%'
            }
        });

        // 3. EMPLOYEES
        destroyChart('employees');
        const hrTop = [...(d.hr || [])].sort((a, b) => b.totalWorkers - a.totalWorkers).slice(0, 8);
        const empTotal = sum(d.hr, 'totalWorkers');
        setTotalPill('totalEmployees', 'إجمالي العاملين المسجلين', empTotal, ' عامل');
        renderLegend('legendEmployees', hrTop.map((x) => ({
            label: `${x.unit} (${x.totalWorkers})`,
            color: '#E87524'
        })));
        charts.employees = new Chart(document.getElementById('chartEmployees'), {
            type: 'bar',
            data: {
                labels: hrTop.map(x => x.unit),
                datasets: [{ label: 'العاملون', data: hrTop.map(x => x.totalWorkers), backgroundColor: '#E87524', borderRadius: 8 }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    title: titleWithTotal('توزيع العاملين (أعلى 8 وحدات)', empTotal, ' عامل'),
                    tooltip: tooltipWithTotal(empTotal, ' عامل')
                },
                scales: { y: { beginAtZero: true, grid: { color: '#e9ecef' }, title: { display: true, text: 'عدد العاملين' } } }
            }
        });

        // 4. MACHINE STATUS
        destroyChart('machineStatus');
        const statusTotal = (d.machines || []).reduce((s, x) => s + x.total, 0);
        const totalValid   = sum(d.machines, 'valid');
        const totalInvalid = sum(d.machines, 'invalid');
        const totalUnknown = sum(d.machines, 'unknown');
        setTotalPill('totalMachineStatus', 'إجمالي الماكينات', statusTotal, ' ماكينة');
        renderLegend('legendMachineStatus', [
            { label: `صالح (${totalValid.toLocaleString()})`, color: '#10b981' },
            { label: `غير صالح (${totalInvalid.toLocaleString()})`, color: '#ef4444' },
            { label: `غير محدد (${totalUnknown.toLocaleString()})`, color: '#f59e0b' }
        ]);
        charts.machineStatus = new Chart(document.getElementById('chartMachineStatus'), {
            type: 'bar',
            data: {
                labels: (d.machines || []).map(x => x.unit),
                datasets: [
                    { label: 'صالح', data: (d.machines || []).map(x => x.valid), backgroundColor: '#10b981', borderRadius: 6 },
                    { label: 'غير صالح', data: (d.machines || []).map(x => x.invalid), backgroundColor: '#ef4444', borderRadius: 6 },
                    { label: 'غير محدد', data: (d.machines || []).map(x => x.unknown), backgroundColor: '#f59e0b', borderRadius: 6 },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    title: titleWithTotal('حالة الماكينات حسب الوحدة', statusTotal, ' ماكينة'),
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                const val = Number(ctx.raw) || 0;
                                const pct = statusTotal ? ((val / statusTotal) * 100).toFixed(1) : 0;
                                return ` ${ctx.dataset.label}: ${val} (${pct}%)`;
                            }
                        }
                    }
                },
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true, title: { display: true, text: 'عدد الماكينات' } }
                }
            }
        });

        // 5. MACHINE UTILIZATION
        destroyChart('machineUtil');
        const utilRows = (d.machines || []).filter(x => x.utilPct !== null);
        const utilAvg = utilRows.length ? (utilRows.reduce((s, x) => s + x.utilPct, 0) / utilRows.length).toFixed(1) : 0;
        setTotalPill('totalMachineUtil', 'متوسط استغلال الماكينات', utilAvg, '%');
        renderLegend('legendMachineUtil', [
            { label: 'جيد (≥70%)', color: '#10b981' },
            { label: 'متوسط (40-69%)', color: '#f59e0b' },
            { label: 'ضعيف (<40%)', color: '#ef4444' }
        ]);
        charts.machineUtil = new Chart(document.getElementById('chartMachineUtil'), {
            type: 'bar',
            data: {
                labels: utilRows.map(x => x.unit),
                datasets: [{
                    label: 'نسبة الاستغلال %',
                    data: utilRows.map(x => x.utilPct),
                    backgroundColor: utilRows.map(x => x.utilPct >= 70 ? '#10b981' : x.utilPct >= 40 ? '#f59e0b' : '#ef4444'),
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: `نسبة استغلال الماكينات — متوسط عام: ${utilAvg}%`,
                        font: { size: 13, weight: 'bold' }, color: '#0b2b5c', padding: { bottom: 12 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                const v = Number(ctx.raw) || 0;
                                const status = v >= 70 ? '🟢 جيد' : v >= 40 ? '🟡 متوسط' : '🔴 ضعيف';
                                return ` ${ctx.label}: ${v}% — ${status}`;
                            }
                        }
                    }
                },
                scales: { y: { beginAtZero: true, max: 100, title: { display: true, text: 'نسبة الاستغلال %' } } }
            }
        });

        // 6. LINES COUNT
        destroyChart('linesCount');
        const linesTotal = sum(d.lines, 'total');
        setTotalPill('totalLinesCount', 'إجمالي خطوط الإنتاج', linesTotal, ' خط');
        renderLegend('legendLinesCount', (d.lines || []).map(x => ({ label: `${x.unit} (${x.total})`, color: '#E87524' })));
        charts.linesCount = new Chart(document.getElementById('chartLinesCount'), {
            type: 'bar',
            data: {
                labels: (d.lines || []).map(x => x.unit),
                datasets: [{ label: 'عدد الخطوط', data: (d.lines || []).map(x => x.total), backgroundColor: '#E87524', borderRadius: 6 }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    title: titleWithTotal('عدد خطوط الإنتاج حسب الوحدة', linesTotal, ' خط'),
                    tooltip: tooltipWithTotal(linesTotal, ' خط')
                },
                scales: { y: { beginAtZero: true, title: { display: true, text: 'عدد الخطوط' } } }
            }
        });

        // 7. LINES WORKING
        destroyChart('linesWorking');
        const linesAvgWorking = d.lines && d.lines.length ? (d.lines.reduce((s, x) => s + (x.workingPct || 0), 0) / d.lines.length).toFixed(1) : 0;
        setTotalPill('totalLinesWorking', 'متوسط نسبة الخطوط العاملة', linesAvgWorking, '%');
        renderLegend('legendLinesWorking', [
            { label: 'ممتاز (≥90%)', color: '#10b981' },
            { label: 'متوسط (50-89%)', color: '#f59e0b' },
            { label: 'ضعيف (<50%)', color: '#ef4444' }
        ]);
        charts.linesWorking = new Chart(document.getElementById('chartLinesWorking'), {
            type: 'bar',
            data: {
                labels: (d.lines || []).map(x => x.unit),
                datasets: [{
                    label: 'نسبة الخطوط العاملة %',
                    data: (d.lines || []).map(x => x.workingPct),
                    backgroundColor: (d.lines || []).map(x => x.workingPct >= 90 ? '#10b981' : x.workingPct >= 50 ? '#f59e0b' : '#ef4444'),
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'نسبة الخطوط العاملة %',
                        font: { size: 12, weight: 'bold' }, color: '#0b2b5c', padding: { bottom: 12 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                const v = Number(ctx.raw) || 0;
                                const status = v >= 90 ? '🟢 ممتاز' : v >= 50 ? '🟡 متوسط' : '🔴 ضعيف';
                                return ` ${ctx.label}: ${v}% — ${status}`;
                            }
                        }
                    }
                },
                scales: { y: { beginAtZero: true, max: 100, title: { display: true, text: 'النسبة %' } } }
            }
        });

        // 8. INVENTORY ITEMS
        destroyChart('inventoryItems');
        const invSource = Array.isArray(d.inventory) ? d.inventory : (d.inventory?.summary || []);
        const invRows = [...invSource].sort((a, b) => b.items - a.items);
        const invTotal = sum(invRows, 'items');
        const invPalette = ['#E87524', '#5B9BD5', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4'];

        setTotalPill('totalInventoryItems', 'إجمالي الأصناف', invTotal, ' صنف');
        renderLegend('legendInventoryItems', invRows.map((x, i) => ({
            label: `${x.unit} (${x.items.toLocaleString()})`,
            color: invPalette[i % invPalette.length]
        })));

        charts.inventoryItems = new Chart(document.getElementById('chartInventoryItems'), {
            type: 'bar',
            data: {
                labels: invRows.map(x => x.unit),
                datasets: [{
                    label: 'عدد الأصناف',
                    data: invRows.map(x => x.items),
                    backgroundColor: invRows.map((_, i) => invPalette[i % invPalette.length]),
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    title: titleWithTotal('عدد الأصناف حسب الوحدة', invTotal, ' صنف'),
                    tooltip: {
                        rtl: true,
                        callbacks: {
                            label: function (ctx) {
                                const val = Number(ctx.raw) || 0;
                                const pct = invTotal ? ((val / invTotal) * 100).toFixed(1) : 0;
                                return ` ${ctx.label}: ${fmt(val)} صنف (${pct}%)`;
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'عدد الأصناف' }, grid: { color: '#e9ecef' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // 8.b INVENTORY STATUS
        destroyChart('inventoryStatus');
        const invStatusTotal = sum(invRows, 'positive') + sum(invRows, 'zero');
        setTotalPill('totalInventoryStatus', 'إجمالي الأصناف (كل الحالات)', invStatusTotal, ' صنف');
        renderLegend('legendInventoryStatus', [
            { label: `موجبة (${sum(invRows, 'positive').toLocaleString()})`, color: '#10b981' },
            { label: `صفر (${sum(invRows, 'zero').toLocaleString()})`, color: '#ef4444' }
        ]);

        charts.inventoryStatus = new Chart(document.getElementById('chartInventoryStatus'), {
            type: 'bar',
            data: {
                labels: invRows.map(x => x.unit),
                datasets: [
                    { label: 'موجبة', data: invRows.map(x => x.positive), backgroundColor: '#10b981', borderRadius: 4, borderSkipped: false },
                    { label: 'صفر', data: invRows.map(x => x.zero), backgroundColor: '#ef4444', borderRadius: 4, borderSkipped: false }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    title: titleWithTotal('أصناف موجبة vs صفر', invStatusTotal, ' صنف'),
                    tooltip: {
                        rtl: true,
                        callbacks: {
                            label: function (ctx) {
                                const val = Number(ctx.raw) || 0;
                                const pct = invStatusTotal ? ((val / invStatusTotal) * 100).toFixed(1) : 0;
                                return ` ${ctx.dataset.label}: ${fmt(val)} (${pct}%)`;
                            }
                        }
                    }
                },
                scales: {
                    x: { stacked: true, grid: { display: false } },
                    y: { stacked: true, beginAtZero: true, grid: { color: '#e9ecef' } }
                }
            }
        });

        // 8.c FUEL DAYS
        destroyChart('fuelDays');
        const fuelSource = Array.isArray(d.fuel) ? d.fuel : [];
        const fuelRows = [...fuelSource].sort((a, b) => a.days - b.days);
        const fuelTotalDays = sum(fuelRows, 'days');

        setTotalPill('totalFuelDays', 'إجمالي أيام الكفاية', fuelTotalDays, ' يوم');
        renderLegend('legendFuelDays', [
            { label: 'حرج (<7)', color: '#ef4444' },
            { label: 'تحت المراقبة (7-15)', color: '#f59e0b' },
            { label: 'جيد (>=15)', color: '#10b981' }
        ]);

        charts.fuelDays = new Chart(document.getElementById('chartFuelDays'), {
            type: 'bar',
            data: {
                labels: fuelRows.map(x => `${x.item} — ${x.org}`),
                datasets: [{
                    label: 'أيام الكفاية',
                    data: fuelRows.map(x => x.days),
                    backgroundColor: fuelRows.map(x => x.days < 7 ? '#ef4444' : x.days < 15 ? '#f59e0b' : '#10b981'),
                    borderRadius: 8
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    title: titleWithTotal('أيام كفاية الوقود', fuelTotalDays, ' يوم'),
                    tooltip: {
                        rtl: true,
                        callbacks: {
                            label: function (ctx) {
                                const days = Number(ctx.raw) || 0;
                                const status = days < 7 ? '🔴 حرج' : days < 15 ? '🟡 تحت المراقبة' : '🟢 جيد';
                                return ` ${ctx.label}: ${days} يوم — ${status}`;
                            }
                        }
                    }
                },
                scales: {
                    x: { beginAtZero: true, title: { display: true, text: 'أيام الكفاية' }, grid: { color: '#e9ecef' } },
                    y: { grid: { display: false } }
                }
            }
        });

        // 9. HR EMPLOYEES
        destroyChart('hrEmployees');
        setTotalPill('totalHREmployees', 'إجمالي العاملين', empTotal, ' عامل');
        renderLegend('legendHREmployees', hrTop.map(x => ({ label: `${x.unit} (${x.totalWorkers})`, color: '#E87524' })));
        charts.hrEmployees = new Chart(document.getElementById('chartHREmployees'), {
            type: 'bar',
            data: {
                labels: hrTop.map(x => x.unit),
                datasets: [{ label: 'العاملون', data: hrTop.map(x => x.totalWorkers), backgroundColor: '#E87524', borderRadius: 8 }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    title: titleWithTotal('إجمالي العاملين (أعلى 8)', empTotal, ' عامل'),
                    tooltip: tooltipWithTotal(empTotal, ' عامل')
                },
                scales: { y: { beginAtZero: true, title: { display: true, text: 'عدد العاملين' } } }
            }
        });

        // 10. HR EMERGENCY
        destroyChart('hrEmergency');
        const emgTotal = sum(d.hr, 'emergency');
        setTotalPill('totalHREmergency', 'إجمالي المتاحين للطوارئ', emgTotal, ' شخص');
        renderLegend('legendHREmergency', hrTop.map(x => ({ label: `${x.unit} (${x.emergency})`, color: '#f59e0b' })));
        charts.hrEmergency = new Chart(document.getElementById('chartHREmergency'), {
            type: 'bar',
            data: {
                labels: hrTop.map(x => x.unit),
                datasets: [{ label: 'متاحون للطوارئ', data: hrTop.map(x => x.emergency), backgroundColor: '#f59e0b', borderRadius: 8 }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    title: titleWithTotal('المتاحون للطوارئ (أعلى 8)', emgTotal, ' شخص'),
                    tooltip: tooltipWithTotal(emgTotal, ' شخص')
                },
                scales: { y: { beginAtZero: true, title: { display: true, text: 'العدد' } } }
            }
        });

        // 11. VEHICLES COUNT
        destroyChart('vehiclesCount');
        const vehTotal = sum(d.vehicles, 'total');
        setTotalPill('totalVehiclesCount', 'إجمالي المركبات', vehTotal, ' مركبة');
        renderLegend('legendVehiclesCount', (d.vehicles || []).map(x => ({ label: `${x.unit} (${x.total})`, color: '#5B9BD5' })));
        charts.vehiclesCount = new Chart(document.getElementById('chartVehiclesCount'), {
            type: 'bar',
            data: {
                labels: (d.vehicles || []).map(x => x.unit),
                datasets: [{ label: 'المركبات', data: (d.vehicles || []).map(x => x.total), backgroundColor: '#5B9BD5', borderRadius: 6 }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    title: titleWithTotal('عدد المركبات حسب الوحدة', vehTotal, ' مركبة'),
                    tooltip: tooltipWithTotal(vehTotal, ' مركبة')
                },
                scales: { y: { beginAtZero: true, title: { display: true, text: 'عدد المركبات' } } }
            }
        });

        // 12. VEHICLES STATUS
        destroyChart('vehiclesStatus');
        const vehValid    = sum(d.vehicles, 'valid');
        const vehBroken   = sum(d.vehicles, 'broken');
        const vehScrapped = sum(d.vehicles, 'scrapped');
        const vehGrand    = vehValid + vehBroken + vehScrapped;
        setTotalPill('totalVehiclesStatus', 'إجمالي المركبات (كل الحالات)', vehGrand, ' مركبة');
        renderLegend('legendVehiclesStatus', [
            { label: `صالح (${vehValid.toLocaleString()})`, color: '#10b981' },
            { label: `عاطل (${vehBroken.toLocaleString()})`, color: '#ef4444' },
            { label: `مكهين (${vehScrapped.toLocaleString()})`, color: '#8b5cf6' }
        ]);
        charts.vehiclesStatus = new Chart(document.getElementById('chartVehiclesStatus'), {
            type: 'bar',
            data: {
                labels: (d.vehicles || []).map(x => x.unit),
                datasets: [
                    { label: 'صالح', data: (d.vehicles || []).map(x => x.valid), backgroundColor: '#10b981', borderRadius: 6 },
                    { label: 'عاطل', data: (d.vehicles || []).map(x => x.broken), backgroundColor: '#ef4444', borderRadius: 6 },
                    { label: 'مكهين', data: (d.vehicles || []).map(x => x.scrapped), backgroundColor: '#8b5cf6', borderRadius: 6 },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    title: titleWithTotal('حالة المركبات حسب الوحدة', vehGrand, ' مركبة'),
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                const val = Number(ctx.raw) || 0;
                                const pct = vehGrand ? ((val / vehGrand) * 100).toFixed(1) : 0;
                                return ` ${ctx.dataset.label}: ${val} (${pct}%)`;
                            }
                        }
                    }
                },
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true, title: { display: true, text: 'عدد المركبات' } }
                }
            }
        });
    }

    // ==================== EXPORT ====================
    window.exportChart = function (key) {
        const chart = charts[key];
        if (!chart) return;
        const link = document.createElement('a');
        link.download = `chart-${key}-${new Date().toISOString().slice(0, 10)}.png`;
        link.href = chart.toBase64Image();
        link.click();
    };

    // ==================== INIT ====================
    function initAll() {
        renderOverviewTable();
        renderMachinesTable();
        renderLinesTable();
        renderInventoryTables();
        renderHRTable();
        renderVehiclesTable();
        buildCharts();

        const search = document.getElementById('overviewSearch');
        if (search) {
            search.addEventListener('input', (e) => {
                renderOverviewTable(e.target.value);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof AOS !== 'undefined') AOS.init({ duration: 600, once: true, offset: 50 });
        loadData();
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/admin/manufacturing-report/index.blade.php ENDPATH**/ ?>