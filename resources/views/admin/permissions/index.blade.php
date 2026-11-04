@extends('layouts.admin')

@section('title', __('permissions.title'))
@section('page_title', __('permissions.title'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>{{ __('permissions.title') }}</h2>
    <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> {{ __('permissions.create') }}
    </a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control"
               placeholder="{{ __('permissions.search_placeholder') }}"
               value="{{ request('search') }}">
    </div>
    <div class="col-md-3">
        <select name="group" class="form-select">
            <option value="">{{ __('permissions.all_groups') }}</option>
            @foreach ($groups as $g)
                <option value="{{ $g }}" @selected(request('group') === $g)>{{ $g }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-outline-secondary w-100">
            <i class="bi bi-search"></i> {{ __('common.search') }}
        </button>
    </div>
</form>

@forelse ($permissions as $group => $groupPermissions)
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>{{ $group ?: __('permissions.ungrouped') }}</strong>
            <span class="badge bg-secondary">{{ $groupPermissions->count() }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 35%">{{ __('permissions.name') }}</th>
                        <th>{{ __('permissions.description') }}</th>
                        <th>{{ __('permissions.roles_count') }}</th>
                        <th>{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groupPermissions as $permission)
                        <tr>
                            <td><code>{{ $permission->name }}</code></td>
                            <td class="text-muted small">{{ $permission->description ?? '—' }}</td>
                            <td>
                                <span class="badge bg-info-subtle text-info-emphasis">
                                    {{ $permission->roles_count }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.permissions.show', $permission) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.permissions.edit', $permission) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if ($permission->roles_count > 0)
                                    <button class="btn btn-sm btn-outline-danger" disabled
                                            title="{{ __('permissions.has_roles_hint') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @else
                                    <form action="{{ route('admin.permissions.destroy', $permission) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('{{ __('permissions.delete_confirm') }}')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@empty
    <div class="alert alert-info">{{ __('common.no_data') }}</div>
@endforelse
@endsection