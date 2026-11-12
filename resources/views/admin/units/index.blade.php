@extends('layouts.admin')

@section('title', __('units.title'))
@section('page_title', __('units.title'))
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('navigation.units') }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>{{ __('units.title') }}</h2>
        <a href="{{ route('admin.units.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> {{ __('units.create') }}
        </a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control"
               placeholder="{{ __('units.search_placeholder') }}"
               value="{{ request('search') }}">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-outline-secondary w-100">
            <i class="bi bi-search"></i> {{ __('common.search') ?? 'Search' }}
        </button>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('units.name') }}</th>
                    <th>{{ __('units.created_at') }}</th>
                    <th>{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($units as $unit)
                    <tr>
                        <td>{{ $unit->id }}</td>
                        <td>{{ $unit->name }}</td>
                        <td>{{ $unit->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                                <a href="{{ route('admin.units.edit', $unit) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.units.destroy', $unit) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('{{ __('units.delete_confirm') }}')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">{{ __('common.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $units->links() }}</div>
@endsection