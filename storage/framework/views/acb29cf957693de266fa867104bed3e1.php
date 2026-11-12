<?php $__env->startSection('title', $moduleName . ' — التحليلات'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid" dir="rtl">

    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1"><?php echo e($moduleName); ?></h3>
            <small class="text-muted"><?php echo e($moduleCode); ?> — التحليلات والرسوم البيانية</small>
        </div>
        <div>
            <a href="<?php echo e(route('admin.excel-modules.analytics.index')); ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-grid"></i> كل الموديولات
            </a>
            <a href="<?php echo e(url()->current()); ?>" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-arrow-clockwise"></i> تحديث
            </a>
        </div>
    </div>

    
    <div class="row mb-4" id="kpisWrap">
        <div class="col-12 text-center text-muted py-3">جاري التحميل...</div>
    </div>

    
    <div class="row" id="chartsWrap"></div>

    
    <div class="card mt-4 shadow-sm" id="mapCard" style="display:none">
        <div class="card-header">
            <strong>الخريطة</strong>
            <span class="text-muted small" id="mapCount"></span>
        </div>
        <div class="card-body p-0">
            <div id="map" style="height: 450px;"></div>
        </div>
    </div>

    
    <div class="alert alert-danger mt-4" id="errorBox" style="display:none"></div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
(function () {
    const moduleCode = <?php echo json_encode($moduleCode, 15, 512) ?>;
    const analyticsUrl = `/admin/excel-modules/${moduleCode}/analytics`;

    // ألوان Chart.js
    const palette = [
        '#4e79a7','#f28e2b','#e15759','#76b7b2','#59a14f',
        '#edc948','#b07aa1','#ff9da7','#9c755f','#bab0ac',
        '#86bcb6','#d37295','#a0cbe8','#ffbe7d','#8cd17d',
    ];

    const fmt = n => (typeof n === 'number' ? n.toLocaleString('ar-EG') : n);

    function showError(msg) {
        const box = document.getElementById('errorBox');
        box.style.display = 'block';
        box.textContent = msg;
        console.error(msg);
    }

    function renderKpis(kpis) {
        const wrap = document.getElementById('kpisWrap');
        wrap.innerHTML = '';
        if (!kpis || kpis.length === 0) {
            wrap.innerHTML = '<div class="col-12 text-muted">لا توجد مؤشرات</div>';
            return;
        }
        kpis.forEach(k => {
            const col = document.createElement('div');
            col.className = 'col-sm-6 col-md-3 mb-3';
            col.innerHTML = `
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="text-muted small mb-1">${k.title}</div>
                        <div class="fs-3 fw-bold text-primary">${fmt(k.value)}</div>
                        ${k.unit ? `<div class="text-muted small">${k.unit}</div>` : ''}
                    </div>
                </div>
            `;
            wrap.appendChild(col);
        });
    }

    function renderChart(chart, index) {
        const isHorizontal = chart.type === 'horizontalBar';
        const col = document.createElement('div');
        col.className = isHorizontal ? 'col-12 mb-4' : 'col-md-6 mb-4';

        const canvasId = `chart_${index}`;
        col.innerHTML = `
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h6 class="card-title mb-3">${chart.title}</h6>
                    <div style="position:relative;height:${isHorizontal ? 280 : 260}px">
                        <canvas id="${canvasId}"></canvas>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('chartsWrap').appendChild(col);

        const type = isHorizontal ? 'bar' : chart.type;
        const colors = chart.type === 'doughnut' || chart.type === 'pie'
            ? palette.slice(0, chart.labels.length)
            : palette[0];

        new Chart(document.getElementById(canvasId), {
            type: type,
            data: {
                labels: chart.labels,
                datasets: [{
                    label: chart.title,
                    data: chart.data,
                    backgroundColor: colors,
                    borderColor: '#fff',
                    borderWidth: chart.type === 'doughnut' ? 2 : 1,
                }]
            },
            options: {
                indexAxis: isHorizontal ? 'y' : 'x',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: chart.type === 'doughnut' || chart.type === 'pie',
                        position: 'bottom',
                        labels: { font: { size: 12 }, boxWidth: 12 }
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const v = ctx.parsed.y ?? ctx.parsed;
                                const total = ctx.dataset.data.reduce((a, b) => a + (Number(b) || 0), 0);
                                const pct = total ? ((v / total) * 100).toFixed(1) + '%' : '';
                                return `${ctx.label}: ${fmt(v)} ${pct}`;
                            }
                        }
                    }
                },
                scales: (chart.type === 'doughnut' || chart.type === 'pie') ? {} : {
                    x: { beginAtZero: true, ticks: { precision: 0 } },
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    function renderCharts(charts) {
        const wrap = document.getElementById('chartsWrap');
        wrap.innerHTML = '';
        if (!charts || charts.length === 0) {
            wrap.innerHTML = '<div class="col-12 text-muted">لا توجد رسوم بيانية</div>';
            return;
        }
        charts.forEach((c, i) => renderChart(c, i));
    }

    let mapInstance = null;

    function renderMap(mapData) {
        if (!mapData || !mapData.points || mapData.points.length === 0) {
            return;
        }
        document.getElementById('mapCard').style.display = 'block';
        document.getElementById('mapCount').textContent = `(${mapData.points.length} نقطة)`;

        // لو الخريطة موجودة قبل، نزيلها عشان نعيد الرسم
        if (mapInstance) {
            mapInstance.remove();
            mapInstance = null;
        }

        mapInstance = L.map('map').setView([26.8, 30.8], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
            maxZoom: 18
        }).addTo(mapInstance);

        const colors = {
            operational: '#59a14f',
            broken: '#e15759',
            written_off: '#bab0ac',
            waiting_repair: '#f28e2b',
            repair_planned: '#edc948',
            new_stock: '#4e79a7',
            unknown: '#9c755f',
            other: '#76b7b2',
        };

        const bounds = [];
        mapData.points.forEach(p => {
            const color = colors[p.status] || colors.other;
            const marker = L.circleMarker([p.lat, p.lon], {
                radius: 6,
                color: '#fff',
                weight: 1,
                fillColor: color,
                fillOpacity: 0.9,
            }).addTo(mapInstance);
            marker.bindPopup(`<div dir="rtl"><strong>${p.label || ''}</strong><br>الحالة: ${p.status}</div>`);
            bounds.push([p.lat, p.lon]);
        });

        if (bounds.length > 1) {
            mapInstance.fitBounds(bounds, { padding: [20, 20], maxZoom: 10 });
        } else if (bounds.length === 1) {
            mapInstance.setView(bounds[0], 12);
        }
    }

    function load() {
        fetch(analyticsUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
        })
        .then(async r => {
            if (!r.ok) {
                const text = await r.text();
                throw new Error(`HTTP ${r.status}: ${text.substring(0, 200)}`);
            }
            return r.json();
        })
        .then(data => {
            renderKpis(data.kpis);
            renderCharts(data.charts);
            renderMap(data.map);
        })
        .catch(err => showError('فشل تحميل التحليلات: ' + err.message));
    }

    document.addEventListener('DOMContentLoaded', load);
})();
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/admin/excel-modules/analytics.blade.php ENDPATH**/ ?>