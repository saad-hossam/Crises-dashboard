@extends('layouts.admin')

@section('title', __('users.show'))
@section('page_title', $user->name)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.users.index') }}">{{ __('users.title') }}</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
@endsection

@section('content')

<div class="row g-3">
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center py-4">
                <div class="user-avatar mx-auto mb-3"
                     style="width: 80px; height: 80px; font-size: 2rem;">
                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                </div>
                <h4 class="mb-1">{{ $user->name }}</h4>
                <p class="text-muted small mb-3">{{ $user->email }}</p>

                <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-secondary' }} mb-2">
                    {{ __('users.' . $user->status) }}
                </span>

                <hr>

                <dl class="row text-start small mb-0">
                    <dt class="col-5 text-muted">{{ __('users.phone') }}</dt>
                    <dd class="col-7">{{ $user->phone ?: '—' }}</dd>

                    <dt class="col-5 text-muted">{{ __('users.created_at') }}</dt>
                    <dd class="col-7">{{ $user->created_at->format('Y-m-d') }}</dd>

                    <dt class="col-5 text-muted">{{ __('users.last_login') }}</dt>
                    <dd class="col-7">{{ $user->last_login_at?->diffForHumans() ?? '—' }}</dd>
                </dl>
            </div>

            <div class="card-footer bg-transparent border-top d-flex gap-2">
                @can('users.edit')
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary flex-fill">
                        <i class="bi bi-pencil"></i> {{ __('common.edit') ?? 'Edit' }}
                    </a>
                @endcan
                @can('users.delete')
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="flex-fill"
                          onsubmit="return confirm('{{ __('users.delete_confirm', ['name' => $user->name]) }}')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash"></i> {{ __('common.delete') ?? 'Delete' }}
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="text-uppercase text-muted small mb-3">{{ __('users.assigned_roles') }}</h6>

                @forelse ($user->roles as $role)
                    <div class="border rounded-3 p-3 mb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-medium">
                                <i class="bi bi-shield-check text-primary me-1"></i>
                                {{ $role->name }}
                            </div>
                            <div class="text-muted small">
                                {{ $role->permissions->count() }} {{ __('users.permissions_count') }}
                            </div>
                        </div>
                        @can('roles.view')
                            <a href="{{ route('admin.roles.show', $role) }}"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                        @endcan
                    </div>
                @empty
                    <div class="alert alert-warning py-2 small mb-0">
                        {{ __('users.no_roles_assigned') }}
                    </div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="text-uppercase text-muted small mb-3">{{ __('users.effective_permissions') }}</h6>

                @php
                    $permissions = $user->getAllPermissions()->groupBy(
                        fn ($p) => explode('.', $p->name)[0]
                    );
                @endphp

                @forelse ($permissions as $module => $perms)
                    <div class="mb-3">
                        <div class="text-muted small fw-semibold text-uppercase mb-1">{{ $module }}</div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach ($perms as $permission)
                                <span class="badge bg-light text-dark border">
                                    {{ $permission->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-muted small">{{ __('users.no_permissions_assigned') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection