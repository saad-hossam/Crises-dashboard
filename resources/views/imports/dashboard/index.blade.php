
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Import Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Import Dashboard</h1>
            <p class="text-muted mb-0">Excel import performance overview</p>
        </div>

        <a href="{{ route('imports.history') }}" class="btn btn-outline-primary">
            Import History
        </a>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Batches</p>
                    <h2>{{ $statistics['total_batches'] }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Completed</p>
                    <h2 class="text-success">
                        {{ $statistics['completed_batches'] }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Failed</p>
                    <h2 class="text-danger">
                        {{ $statistics['failed_batches'] }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Processing</p>
                    <h2 class="text-warning">
                        {{ $statistics['processing_batches'] }}
                    </h2>
                </div>
            </div>
        </div>

    </div>

    {{-- Row Statistics --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Rows</p>
                    <h3>{{ number_format($statistics['total_rows']) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Imported Rows</p>
                    <h3 class="text-success">
                        {{ number_format($statistics['imported_rows']) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Failed Rows</p>
                    <h3 class="text-danger">
                        {{ number_format($statistics['failed_rows']) }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    {{-- Success Rate --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between mb-2">
                <h5 class="mb-0">Overall Import Success Rate</h5>

                <strong>
                    {{ $statistics['success_rate'] }}%
                </strong>
            </div>

            <div class="progress" style="height: 12px;">
                <div
                    class="progress-bar bg-success"
                    role="progressbar"
                    style="width: {{ min(100, $statistics['success_rate']) }}%"
                    aria-valuenow="{{ $statistics['success_rate'] }}"
                    aria-valuemin="0"
                    aria-valuemax="100"
                ></div>
            </div>

        </div>
    </div>

{{-- Charts --}}
<div class="row g-4 mb-4">

    {{-- Import Status Chart --}}
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">

            <div class="card-header bg-white">
                <h5 class="mb-0">Import Status Distribution</h5>
            </div>

            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="importStatusChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    {{-- Imported vs Failed Chart --}}
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">

            <div class="card-header bg-white">
                <h5 class="mb-0">Imported vs Failed Rows</h5>
            </div>

            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="rowComparisonChart"></canvas>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
    const statusDistribution = @json($statusDistribution);
    const rowComparison = @json($rowComparison);

    // Import Status Doughnut Chart
    new Chart(document.getElementById('importStatusChart'), {
        type: 'doughnut',

        data: {
            labels: Object.keys(statusDistribution),

            datasets: [{
                data: Object.values(statusDistribution),
                backgroundColor: [
                    '#198754',
                    '#dc3545',
                    '#ffc107',
                    '#6c757d'
                ],
                borderWidth: 2
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Imported vs Failed Bar Chart
    new Chart(document.getElementById('rowComparisonChart'), {
        type: 'bar',

        data: {
            labels: ['Rows'],

            datasets: [
                {
                    label: 'Imported Rows',
                    data: [rowComparison.imported_rows],
                    backgroundColor: '#198754'
                },
                {
                    label: 'Failed Rows',
                    data: [rowComparison.failed_rows],
                    backgroundColor: '#dc3545'
                }
            ]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },

            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>


    {{-- Recent Imports --}}
    <div class="card shadow-sm">

        <div class="card-header bg-white d-flex justify-content-between">
            <h5 class="mb-0">Recent Imports</h5>

            <a href="{{ route('imports.history') }}">
                View All
            </a>
        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th>Batch</th>
                        <th>Filename</th>
                        <th>Imported Rows</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($recentImports as $batch)
                        <tr>
                            <td>
                                <a href="{{ route('imports.history.show', $batch) }}">
                                    #{{ $batch->id }}
                                </a>
                            </td>

                            <td>{{ $batch->original_filename }}</td>

                            <td>{{ $batch->imported_rows }}</td>

                            <td>
                                <span class="badge bg-{{
                                    $batch->status === 'completed'
                                        ? 'success'
                                        : ($batch->status === 'failed'
                                            ? 'danger'
                                            : 'warning')
                                }}">
                                    {{ ucfirst($batch->status) }}
                                </span>
                            </td>

                            <td>
                                {{ $batch->created_at?->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                No imports found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>

</body>
</html>
