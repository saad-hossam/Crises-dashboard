@extends('layouts.admin')

@section('title', __('roles.title'))
@section('page_title', __('roles.title'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>{{ __('roles.title') }}</h2>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> {{ __('roles.create') }}
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('roles.name') }}</th>
                    <th>{{ __('roles.users_count') }}</th>
                    <th>{{ __('roles.permissions_count') }}</th>
                    <th>{{ __('roles.created_at') }}</th>
                    <th>{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>
                            <strong>{{ $role->name }}</strong>
                            @if ($role->is_protected)
                                <span class="badge bg-warning-subtle text-warning-emphasis ms-1">
                                    <i class="bi bi-shield-fill"></i> {{ __('roles.protected') }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info-subtle text-info-emphasis">
                                {{ $role->users_count }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                {{ $role->permissions_count ?? '—' }}
                            </span>
                        </td>
                        <td>{{ $role->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('admin.roles.show', $role) }}"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.roles.edit', $role) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if ($role->is_protected)
                                <button class="btn btn-sm btn-outline-danger" disabled
                                        title="{{ __('roles.protected_hint') }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @elseif ($role->users_count > 0)
                                <button class="btn btn-sm btn-outline-danger" disabled
                                        title="{{ __('roles.has_users_hint') }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @else
                                <form action="{{ route('admin.roles.destroy', $role) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('{{ __('roles.delete_confirm') }}')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">{{ __('common.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $roles->links() }}</div>
@endsection