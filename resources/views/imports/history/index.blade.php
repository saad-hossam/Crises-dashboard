
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Import History</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<div class="container py-5">

    <h1 class="mb-4">Import History</h1>

    <form method="GET" action="{{ route('imports.history') }}" class="card card-body mb-4">

        <div class="row g-3">

            <div class="col-md-5">
                <label class="form-label">Search</label>

                <input
                    type="text"
                    name="search"
                    class="form-control"
                    value="{{ request('search') }}"
                    placeholder="Search filename or organizational unit"
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">Status</label>

                <select name="status" class="form-select">
                    <option value="">All statuses</option>

                    @foreach(['pending', 'processing', 'completed', 'failed'] as $status)
                        <option
                            value="{{ $status }}"
                            @selected(request('status') === $status)
                        >
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">
                    Filter
                </button>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ route('imports.history') }}" class="btn btn-outline-secondary w-100">
                    Reset
                </a>
            </div>

        </div>
    </form>

    <div class="card shadow-sm">

        <div class="table-responsive">

            <table class="table table-striped table-hover align-middle mb-0">

                <thead class="table-dark">
                    <tr>
                        <th>Batch ID</th>
                        <th>Filename</th>
                        <th>Target Table</th>
                        <th>Organizational Unit</th>
                        <th>Total Rows</th>
                        <th>Imported</th>
                        <th>Failed</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($batches as $batch)
                        <tr>
                            <td>
    <a href="{{ route('imports.history.show', $batch) }}"
       class="text-decoration-none fw-bold">
        #{{ $batch->id }}
    </a>
</td>

                            <td>{{ $batch->original_filename }}</td>

                            <td>{{ $batch->target_table }}</td>

                            <td>{{ $batch->organizational_unit ?? '-' }}</td>

                            <td>{{ $batch->total_rows }}</td>

                            <td>{{ $batch->imported_rows }}</td>

                            <td>{{ $batch->failed_rows }}</td>

                            <td>
                                @php
                                    $badgeClass = match($batch->status) {
                                        'completed' => 'success',
                                        'failed' => 'danger',
                                        'processing' => 'warning',
                                        default => 'secondary',
                                    };
                                @endphp

                                <span class="badge bg-{{ $badgeClass }}">
                                    {{ ucfirst($batch->status) }}
                                </span>
                            </td>

                            <td>
                                {{ $batch->created_at?->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                No import history found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>

        <div class="card-footer">
            {{ $batches->links() }}
        </div>

    </div>

</div>

</body>
</html>
