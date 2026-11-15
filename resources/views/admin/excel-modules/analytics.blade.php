{{-- resources/views/admin/excel-modules/analytics.blade.php --}}
@extends('layouts.admin')

@section('title', $moduleName . ' — التحليلات')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
/* ============================================================
   ANALYTICS PAGE — Modern Redesign
   ============================================================ */
.analytics-page {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    max-width: 1600px;
    margin: 0 auto;
}

/* ============ HERO ============ */
.an-hero {
    position: relative;
    border-radius: 1.35rem;
    padding: 1.75rem;
    margin-bottom: 1.5rem;
    background: linear-gradient(135deg, #E87524 0%, #CF5F15 55%, #17365D 100%);
    color: #fff;
    overflow: hidden;
    box-shadow: 0 20px 40px -12px rgba(232, 117, 36, 0.35);
}
.an-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 15% 25%, rgba(255, 255, 255, 0.15) 0%, transparent 40%),
        radial-gradient(circle at 85% 75%, rgba(255, 255, 255, 0.1) 0%, transparent 45%);
    pointer-events: none;
}
.an-hero-content { position: relative; z-index: 2; }
.an-hero-kicker {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    background: rgba(255, 255, 255, 0.18);
    padding: .3rem .75rem;
    border-radius: 50px;
    font-size: .72rem;
    font-weight: 600;
    margin-bottom: .65rem;
    border: 1px solid rgba(255, 255, 255, .2);
}
.an-hero-title {
    font-size: clamp(1.25rem, 3vw, 1.75rem);
    font-weight: 800;
    margin: 0 0 .35rem;
    line-height: 1.3;
    word-break: break-word;
}
.an-hero-sub {
    font-size: .88rem;
    color: rgba(255, 255, 255, .85);
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    gap: .35rem .85rem;
}
.an-hero-sub span {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    white-space: nowrap;
}
.an-hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: .4rem;
    margin-top: 1rem;
}
.an-hero-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    padding: .55rem .95rem;
    border-radius: .6rem;
    font-size: .8rem;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid rgba(255, 255, 255, .3);
    background: rgba(255, 255, 255, .15);
    color: #fff;
    transition: all .2s;
    min-height: 38px;
    white-space: nowrap;
    cursor: pointer;
}
.an-hero-btn:hover {
    background: rgba(255, 255, 255, .28);
    color: #fff;
    transform: translateY(-1px);
}
.an-hero-btn-primary {
    background: #fff;
    color: #E87524;
    border-color: #fff;
}
.an-hero-btn-primary:hover {
    background: #f8fafc;
    color: #E87524;
}

/* ============ KPIs ============ */
.an-kpis {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: .85rem;
    margin-bottom: 1.5rem;
}
.an-kpi {
    background: #fff;
    border-radius: 1rem;
    padding: 1.15rem 1.25rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
    position: relative;
    overflow: hidden;
    transition: all .25s ease;
}
.an-kpi::before {
    content: '';
    position: absolute;
    top: 0;
    inset-inline-start: 0;
    width: 4px;
    height: 100%;
    background: var(--kpi-color, #E87524);
    border-radius: 0 4px 4px 0;
}
.an-kpi:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.1);
    border-color: var(--kpi-color, #E87524);
}
.an-kpi-label {
    font-size: .72rem;
    color: #64748b;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    margin-bottom: .35rem;
}
.an-kpi-value {
    font-size: 1.65rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
    letter-spacing: -.5px;
}
.an-kpi-unit {
    font-size: .75rem;
    color: #94a3b8;
    font-weight: 600;
    margin-top: .15rem;
}

/* KPI Loading */
.an-kpi-skeleton {
    height: 90px;
    background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
    background-size: 1000px 100%;
    animation: anShimmer 1.5s infinite linear;
    border-radius: 1rem;
    border: 1px solid #e2e8f0;
}
@keyframes anShimmer {
    0%   { background-position: -500px 0; }
    100% { background-position: 500px 0; }
}

/* ============ CHARTS ============ */
.an-charts {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.an-chart-card {
    grid-column: span 6;
    background: #fff;
    border-radius: 1.15rem;
    padding: 1.35rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
    position: relative;
    display: flex;
    flex-direction: column;
}
.an-chart-card.is-full { grid-column: span 12; }
.an-chart-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding-bottom: .85rem;
    border-bottom: 1px solid #f1f5f9;
    gap: .5rem;
}
.an-chart-title {
    font-size: .95rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    display: inline-flex;
    align-items: center;
    gap: .4rem;
}
.an-chart-title i { color: #E87524; }
.an-chart-total {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    background: rgba(232, 117, 36, 0.1);
    color: #E87524;
    padding: .25rem .65rem;
    border-radius: 50px;
    font-size: .72rem;
    font-weight: 700;
    flex-shrink: 0;
}
.an-chart-box {
    position: relative;
    height: 280px;
    flex: 1;
}
.an-chart-box.tall { height: 340px; }
.an-chart-export {
    position: absolute;
    top: 1rem;
    inset-inline-start: 1.35rem;
    width: 30px;
    height: 30px;
    border-radius: .5rem;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .15s;
    z-index: 5;
    font-size: .8rem;
}
.an-chart-export:hover {
    background: #E87524;
    color: #fff;
    border-color: #E87524;
}

/* Chart Loading */
.an-chart-skeleton {
    height: 280px;
    background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
    background-size: 1000px 100%;
    animation: anShimmer 1.5s infinite linear;
    border-radius: .75rem;
}

/* ============ MAP ============ */
.an-map-card {
    background: #fff;
    border-radius: 1.15rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
    overflow: hidden;
    margin-bottom: 1.5rem;
    display: none;
}
.an-map-card.show { display: block; }
.an-map-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.35rem;
    border-bottom: 1px solid #f1f5f9;
    gap: .5rem;
}
.an-map-title {
    font-size: .95rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    display: inline-flex;
    align-items: center;
    gap: .4rem;
}
.an-map-title i { color: #E87524; }
.an-map-count {
    background: rgba(232, 117, 36, 0.1);
    color: #E87524;
    padding: .2rem .65rem;
    border-radius: 50px;
    font-size: .72rem;
    font-weight: 700;
}
#map {
    height: 450px;
    background: #f8fafc;
}

/* ============ EMPTY / ERROR ============ */
.an-empty {
    text-align: center;
    padding: 3rem 1rem;
    color: #94a3b8;
}
.an-empty i {
    font-size: 2.5rem;
    opacity: .4;
    margin-bottom: .75rem;
    display: block;
}
.an-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    border-radius: .85rem;
    padding: 1rem 1.15rem;
    display: flex;
    gap: .65rem;
    align-items: flex-start;
    margin-top: 1rem;
}
.an-error i {
    font-size: 1.2rem;
    flex-shrink: 0;
}

/* ============ RESPONSIVE ============ */
@media (max-width: 991.98px) {
    .an-chart-card { grid-column: span 12; }
    .an-kpis { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 767.98px) {
    .an-hero { padding: 1.25rem; border-radius: 1rem; }
    .an-hero-title { font-size: 1.15rem; }
    .an-hero-actions { display: grid; grid-template-columns: 1fr 1fr; width: 100%; }
    .an-hero-btn { min-height: 44px; }
    .an-kpis { grid-template-columns: 1fr 1fr; gap: .5rem; }
    .an-kpi { padding: .9rem 1rem; }
    .an-kpi-value { font-size: 1.3rem; }
    .an-chart-box { height: 240px; }
    .an-chart-box.tall { height: 280px; }
    #map { height: 350px; }
}
@media (max-width: 575.98px) {
    .an-kpis { grid-template-columns: 1fr; }
    .an-hero-actions { grid-template-columns: 1fr; }
    .an-chart-box { height: 220px; }
}

/* ============ DARK MODE ============ */
.admin-wrapper.dark-mode .an-kpi,
.admin-wrapper.dark-mode .an-chart-card,
.admin-wrapper.dark-mode .an-map-card {
    background: #1e293b;
    border-color: #334155;
}
.admin-wrapper.dark-mode .an-kpi-value,
.admin-wrapper.dark-mode .an-chart-title,
.admin-wrapper.dark-mode .an-map-title {
    color: #f1f5f9;
}
.admin-wrapper.dark-mode .an-kpi-label,
.admin-wrapper.dark-mode .an-kpi-unit {
    color: #94a3b8;
}
.admin-wrapper.dark-mode .an-chart-head,
.admin-wrapper.dark-mode .an-map-head {
    border-color: #334155;
}
.admin-wrapper.dark-mode #map {
    background: #0f172a;
}
</style>
@endpush

@section('content')
<div class="analytics-page" dir="rtl">

    {{-- ============================================================
         HERO
         ============================================================ --}}
    <div class="an-hero">
        <div class="an-hero-content">
            <span class="an-hero-kicker">
                <i class="bi bi-bar-chart-line-fill"></i>
                التحليلات
            </span>
            <h1 class="an-hero-title">{{ $moduleName }}</h1>
            <p class="an-hero-sub">
                <span><i class="bi bi-upc-scan"></i> {{ $moduleCode }}</span>
                <span><i class="bi bi-graph-up"></i> الرسوم البيانية والتحليلات</span>
            </p>
            <div class="an-hero-actions">
                <a href="{{ route('admin.excel-modules.analytics.index') }}" class="an-hero-btn">
                    <i class="bi bi-grid"></i>
                    كل الموديولات
                </a>
                <button type="button" class="an-hero-btn an-hero-btn-primary" onclick="reloadAnalytics()">
                    <i class="bi bi-arrow-clockwise"></i>
                    تحديث
                </button>
            </div>
        </div>
    </div>

    {{-- ============================================================
         KPIs (Skeleton Loading)
         ============================================================ --}}
    <div class="an-kpis" id="kpisWrap">
        <div class="an-kpi-skeleton"></div>
        <div class="an-kpi-skeleton"></div>
        <div class="an-kpi-skeleton"></div>
        <div class="an-kpi-skeleton"></div>
    </div>

    {{-- ============================================================
         CHARTS (Skeleton Loading)
         ============================================================ --}}
    <div class="an-charts" id="chartsWrap">
        <div class="an-chart-card">
            <div class="an-chart-head">
                <h5 class="an-chart-title"><i class="bi bi-bar-chart-fill"></i> جاري التحميل...</h5>
            </div>
            <div class="an-chart-skeleton"></div>
        </div>
        <div class="an-chart-card">
            <div class="an-chart-head">
                <h5 class="an-chart-title"><i class="bi bi-pie-chart-fill"></i> جاري التحميل...</h5>
            </div>
            <div class="an-chart-skeleton"></div>
        </div>
    </div>

    {{-- ============================================================
         MAP
         ============================================================ --}}
    <div class="an-map-card" id="mapCard">
        <div class="an-map-head">
            <h5 class="an-map-title">
                <i class="bi bi-geo-alt-fill"></i>
                الخريطة
            </h5>
            <span class="an-map-count" id="mapCount">0 نقطة</span>
        </div>
        <div id="map"></div>
    </div>

    {{-- ============================================================
         ERROR
         ============================================================ --}}
    <div class="an-error" id="errorBox" style="display:none;">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div>
            <strong class="d-block mb-1">حدث خطأ</strong>
            <span id="errorMessage"></span>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
(function () {
    'use strict';

    // ==================== CONFIG ====================
    const moduleCode = @json($moduleCode);
    const analyticsUrl = `/admin/excel-modules/${moduleCode}/analytics`;

    // ✅ Palette جديدة — ألوان فاتحة وحيوية بدون أسود/رمادي غامق
    const palette = [
        '#E87524', '#FF9A56', '#5B9BD5', '#7EC8E3', '#10b981',
        '#34D399', '#f59e0b', '#FBBF24', '#A78BFA', '#EC4899',
        '#06b6d4', '#22D3EE', '#84cc16', '#F97316', '#F43F5E',
    ];

    const charts = {};
    let mapInstance = null;

    // ==================== HELPERS ====================
    const fmt = n => (typeof n === 'number' ? n.toLocaleString('en-US') : n);
    const sum = (arr) => (arr || []).reduce((s, x) => s + (Number(x) || 0), 0);

    function showError(msg) {
        const box = document.getElementById('errorBox');
        const el = document.getElementById('errorMessage');
        if (box && el) {
            box.style.display = 'flex';
            el.textContent = msg;
        }
        console.error(msg);
    }

    function destroyChart(key) {
        if (charts[key]) {
            charts[key].destroy();
            charts[key] = null;
        }
    }

    // ==================== EXPORT ====================
    window.exportChart = function (key) {
        const chart = charts[key];
        if (!chart) return;
        const link = document.createElement('a');
        link.download = `chart-${moduleCode}-${key}-${new Date().toISOString().slice(0, 10)}.png`;
        link.href = chart.toBase64Image();
        link.click();
    };

    // ==================== RELOAD ====================
    window.reloadAnalytics = function () {
        loadAnalytics();
    };

    // ==================== RENDER KPIs ====================
    function renderKpis(kpis) {
        const wrap = document.getElementById('kpisWrap');
        wrap.innerHTML = '';
        if (!kpis || kpis.length === 0) {
            wrap.innerHTML = '<div class="col-12 text-muted text-center py-3">لا توجد مؤشرات</div>';
            return;
        }
        const kpiColors = ['#E87524', '#17365D', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4'];
        kpis.forEach((k, i) => {
            const color = kpiColors[i % kpiColors.length];
            const col = document.createElement('div');
            col.className = 'an-kpi';
            col.style.setProperty('--kpi-color', color);
            col.innerHTML = `
                <div class="an-kpi-label">${k.title}</div>
                <div class="an-kpi-value">${fmt(k.value)}</div>
                ${k.unit ? `<div class="an-kpi-unit">${k.unit}</div>` : ''}
            `;
            wrap.appendChild(col);
        });
    }

    // ==================== RENDER CHART ====================
    function renderChart(chart, index) {
        const isHorizontal = chart.type === 'horizontalBar';
        const isDoughnut   = chart.type === 'doughnut' || chart.type === 'pie';
        const isLine       = chart.type === 'line';

        const col = document.createElement('div');
        col.className = 'an-chart-card' + (isHorizontal ? ' is-full' : '');

        const canvasId = `chart_${index}`;
        const chartKey = `chart_${index}`;
        const chartHeight = isHorizontal ? 340 : 280;
        const total = sum(chart.data);
        const unitSuffix = chart.unit ? ` ${chart.unit}` : '';

        col.innerHTML = `
            <button type="button" class="an-chart-export"
                    onclick="exportChart('${chartKey}')"
                    title="تحميل PNG">
                <i class="bi bi-download"></i>
            </button>
            <div class="an-chart-head">
                <h5 class="an-chart-title">
                    <i class="bi ${isDoughnut ? 'bi-pie-chart-fill' : (isLine ? 'bi-graph-up' : 'bi-bar-chart-fill')}"></i>
                    ${chart.title}
                </h5>
                <span class="an-chart-total">
                    <i class="bi bi-calculator"></i>
                    ${fmt(total)}${unitSuffix}
                </span>
            </div>
            <div class="an-chart-box ${isHorizontal ? 'tall' : ''}" style="height:${chartHeight}px">
                <canvas id="${canvasId}"></canvas>
            </div>
        `;
        document.getElementById('chartsWrap').appendChild(col);

        // Colors
        let bgColors;
        if (isDoughnut) {
            bgColors = chart.labels.map((_, i) => palette[i % palette.length]);
        } else if (isLine) {
            bgColors = palette[0];
        } else {
            bgColors = chart.labels.length <= 8
                ? chart.labels.map((_, i) => palette[i % palette.length])
                : palette[0];
        }

        destroyChart(chartKey);
        const chartType = isHorizontal ? 'bar' : chart.type;

        charts[chartKey] = new Chart(document.getElementById(canvasId), {
            type: chartType,
            data: {
                labels: chart.labels,
                datasets: [{
                    label: chart.title,
                    data: chart.data,
                    backgroundColor: bgColors,
                    borderColor: isLine ? palette[0] : (isDoughnut ? '#fff' : 'transparent'),
                    borderWidth: isDoughnut ? 2 : (isLine ? 2 : 0),
                    borderRadius: (isDoughnut || isLine) ? 0 : 8,
                    tension: isLine ? 0.35 : 0,
                    fill: isLine ? false : undefined,
                }]
            },
            options: {
                indexAxis: isHorizontal ? 'y' : 'x',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: isDoughnut,
                        position: 'bottom',
                        rtl: true,
                        labels: {
                            boxWidth: 10,
                            boxHeight: 10,
                            padding: 10,
                            font: { size: 11 },
                            color: '#475569',
                            usePointStyle: true,
                        }
                    },
                    tooltip: {
                        rtl: true,
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
                            label: function (ctx) {
                                const val = Number(ctx.parsed.y ?? ctx.parsed ?? ctx.raw) || 0;
                                const pct = total ? ((val / total) * 100).toFixed(1) : 0;
                                return ` ${ctx.label}: ${fmt(val)}${unitSuffix} (${pct}%)`;
                            }
                        }
                    }
                },
                scales: isDoughnut ? {} : {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#64748b',
                            font: { size: 10 },
                        },
                        grid: { color: 'rgba(15,23,42,.06)' },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#64748b',
                            font: { size: 10 },
                        },
                        grid: { color: 'rgba(15,23,42,.06)' },
                    }
                }
            }
        });
    }

    // ==================== RENDER CHARTS ====================
    function renderCharts(chartsData) {
        const wrap = document.getElementById('chartsWrap');
        wrap.innerHTML = '';
        if (!chartsData || chartsData.length === 0) {
            wrap.innerHTML = `
                <div class="col-12 an-empty">
                    <i class="bi bi-inbox"></i>
                    <p>لا توجد رسوم بيانية</p>
                </div>`;
            return;
        }
        chartsData.forEach((c, i) => renderChart(c, i));
    }

    // ==================== RENDER MAP ====================
    function renderMap(mapData) {
        const card = document.getElementById('mapCard');
        if (!mapData || !mapData.points || mapData.points.length === 0) {
            card.classList.remove('show');
            return;
        }
        card.classList.add('show');
        document.getElementById('mapCount').textContent = `${mapData.points.length} نقطة`;

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
            operational:    '#10b981',
            broken:         '#ef4444',
            written_off:    '#94a3b8',
            waiting_repair: '#f59e0b',
            repair_planned: '#FBBF24',
            new_stock:      '#5B9BD5',
            unknown:        '#A78BFA',
            other:          '#06b6d4',
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

    // ==================== LOAD ====================
    async function loadAnalytics() {
        const errorBox = document.getElementById('errorBox');
        errorBox.style.display = 'none';

        try {
            const res = await fetch(analyticsUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
            });
            if (!res.ok) {
                const text = await res.text();
                throw new Error(`HTTP ${res.status}: ${text.substring(0, 200)}`);
            }
            const data = await res.json();
            renderKpis(data.kpis);
            renderCharts(data.charts);
            renderMap(data.map);
        } catch (err) {
            showError('فشل تحميل التحليلات: ' + err.message);
        }
    }

    document.addEventListener('DOMContentLoaded', loadAnalytics);
})();
</script>
@endpush