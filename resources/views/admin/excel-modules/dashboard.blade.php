@extends('layouts.admin')

@section('title', 'لوحة بيانات Excel')
@section('page_title', 'لوحة بيانات Excel')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">لوحة بيانات Excel</li>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">عدد الموديولات</div>
                    <div class="fs-3 fw-bold text-primary">{{ number_format($totalModules) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">إجمالي الصفوف</div>
                    <div class="fs-3 fw-bold text-success">{{ number_format($totalRecords) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">آخر استيراد</div>
                    <div class="fw-semibold">
                        @if($lastImport)
                            {{ $lastImport->created_at->diffForHumans() }}
                        @else
                            —
                        @endif
                    </div>
                    @if($lastImport?->module)
                        <div class="small text-muted text-truncate">{{ $lastImport->module->name_ar }}</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <a href="{{ route('admin.excel-modules.create') }}" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i> رفع ملف جديد
                    </a>
                    <a href="{{ route('admin.excel-modules.index') }}" class="btn btn-outline-secondary btn-sm mt-2">
                        كل الموديولات
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Chart --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    توزيع الصفوف حسب الموديول
                </div>
                <div class="card-body">
                    @if(count($chartLabels) > 0)
                        <canvas id="modulesChart" height="260"></canvas>
                    @else
                        <div class="text-center text-muted py-5">
                            لا توجد بيانات بعد
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Recent batches --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    آخر عمليات الاستيراد
                </div>
                <div class="list-group list-group-flush">
                    @forelse($recentBatches as $batch)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div class="text-truncate pe-2">
                                    <div class="fw-semibold small">
                                        {{ $batch->module?->name_ar ?? '—' }}
                                    </div>
                                    <div class="text-muted small text-truncate">
                                        {{ $batch->original_filename }}
                                    </div>
                                </div>
                                <div class="text-end small text-nowrap">
                                    <div class="badge bg-success">{{ number_format($batch->imported_rows) }} صف</div>
                                    <div class="text-muted">{{ $batch->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted py-4">
                            لا توجد عمليات استيراد بعد
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Modules table --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">كل الموديولات</span>
            <a href="{{ route('admin.excel-modules.index') }}" class="btn btn-sm btn-outline-primary">عرض كروت</a>
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
                    @forelse($modules as $module)
                        <tr>
                            <td class="fw-semibold">{{ $module->name_ar }}</td>
                            <td><code>{{ $module->code }}</code></td>
                            <td class="text-end">{{ number_format($module->records_count) }}</td>
                            <td class="small text-muted">
                                {{ $module->last_imported_at?->format('Y-m-d H:i') ?? '—' }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.excel-modules.show', $module) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    عرض
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">لا توجد موديولات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(count($chartLabels) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('modulesChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'عدد الصفوف',
                data: @json($chartData),
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
@endif
@endpush
