
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Import Batch #{{ $batch->id }}</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Import Batch #{{ $batch->id }}</h1>
            <p class="text-muted mb-0">Import operation details</p>
        </div>

        <a href="{{ route('imports.history') }}" class="btn btn-outline-secondary">
            Back to History
        </a>
    </div>

    @php
        $badgeClass = match($batch->status) {
            'completed' => 'success',
            'failed' => 'danger',
            'processing' => 'warning',
            default => 'secondary',
        };
    @endphp

    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">Import Status</h5>
                <span class="text-muted">Current operation status</span>
            </div>

            <span class="badge fs-6 bg-{{ $badgeClass }}">
                {{ ucfirst($batch->status) }}
            </span>
        </div>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Rows</p>
                    <h2>{{ $batch->total_rows }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Imported Rows</p>
                    <h2 class="text-success">{{ $batch->imported_rows }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Failed Rows</p>
                    <h2 class="text-danger">{{ $batch->failed_rows }}</h2>
                </div>
            </div>
        </div>

    </div>

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white">
            <h5 class="mb-0">File Information</h5>
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4 text-muted">Original Filename</div>
                <div class="col-md-8 fw-semibold">
                    {{ $batch->original_filename }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 text-muted">Stored Filename</div>
                <div class="col-md-8">
                    {{ $batch->stored_filename }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 text-muted">File Extension</div>
                <div class="col-md-8">
                    {{ strtoupper($batch->file_extension) }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 text-muted">File Size</div>
                <div class="col-md-8">
                    {{ $batch->formatted_file_size }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 text-muted">SHA-256</div>
                <div class="col-md-8">
                    <code class="text-break">{{ $batch->file_hash }}</code>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 text-muted">Stored Path</div>
                <div class="col-md-8 text-break">
                    {{ $batch->stored_path }}
                </div>
            </div>

        </div>
    </div>

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white">
            <h5 class="mb-0">Import Information</h5>
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4 text-muted">Target Table</div>
                <div class="col-md-8">
                    {{ $batch->target_table }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 text-muted">Worksheet</div>
                <div class="col-md-8">
                    {{ $batch->selected_sheet ?? '-' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 text-muted">Organizational Unit</div>
                <div class="col-md-8">
                    {{ $batch->organizational_unit ?? '-' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 text-muted">Started At</div>
                <div class="col-md-8">
                    {{ $batch->started_at?->format('Y-m-d H:i:s') ?? '-' }}
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 text-muted">Completed At</div>
                <div class="col-md-8">
                    {{ $batch->completed_at?->format('Y-m-d H:i:s') ?? '-' }}
                </div>
            </div>

        </div>
    </div>

    @if($batch->error_message)
        <div class="alert alert-danger">
            <h5>Error Details</h5>
            <p class="mb-0">{{ $batch->error_message }}</p>
        </div>
    @endif

</div>

</body>
</html>
