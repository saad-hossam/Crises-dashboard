@extends('layouts.admin')

@section('title', __('roles.show'))
@section('page_title', __('roles.show'))

@section('content')
@php
    $groupLabels = [
        'dashboard'     => __('permissions.groups.dashboard'),
        'users'         => __('permissions.groups.users'),
        'roles'         => __('permissions.groups.roles'),
        'permissions'   => __('permissions.groups.permissions'),
        'imports'       => __('permissions.groups.imports'),
        'data'          => __('permissions.groups.data'),
        'reports'       => __('permissions.groups.reports'),
        'settings'      => __('permissions.groups.settings'),
        'activity_logs' => __('permissions.groups.activity_logs'),
        'units'         => __('permissions.groups.units') ?? 'Units',
    ];
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">{{ __('roles.details') }}</h5>
                <dl class="row mb-0">
                    <dt class="col-sm-5">{{ __('roles.name') }}</dt>
                    <dd class="col-sm-7">
                        <strong>{{ $role->name }}</strong>
                        @if ($role->is_protected)
                            <span class="badge bg-warning-subtle text-warning-emphasis ms-2">
                                <i class="bi bi-shield-fill"></i> {{ __('roles.protected') ?? 'Protected' }}
                            </span>
                        @endif
                    </dd>

                    <dt class="col-sm-5">{{ __('roles.description') }}</dt>
                    <dd class="col-sm-7">{{ $role->description ?? '—' }}</dd>

                    <dt class="col-sm-5">{{ __('roles.users_count') }}</dt>
                    <dd class="col-sm-7">{{ $role->users->count() }}</dd>

                    <dt class="col-sm-5">{{ __('roles.permissions_count') }}</dt>
                    <dd class="col-sm-7">{{ $role->permissions->count() }}</dd>

                    <dt class="col-sm-5">{{ __('roles.created_at') }}</dt>
                    <dd class="col-sm-7">{{ $role->created_at?->format('Y-m-d H:i') ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">{{ __('roles.permissions') }}</h5>
                @php $grouped = $role->permissions->groupBy('group'); @endphp
                @if ($grouped->isEmpty())
                    <p class="text-muted mb-0">{{ __('roles.no_permissions') }}</p>
                @else
                    @foreach ($grouped as $group => $perms)
                        <div class="mb-2">
                            <strong class="d-block small text-uppercase text-muted mb-1">
                                {{ $groupLabels[$group] ?? ucfirst($group ?: '—') }}
                            </strong>
                            @foreach ($perms as $p)
                                <span class="badge bg-primary-subtle text-primary-emphasis me-1 mb-1">
                                    {{ $p->name }}
                                </span>
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">{{ __('roles.assigned_users') }}</h5>
                @if ($role->users->isEmpty())
                    <p class="text-muted mb-0">{{ __('roles.no_users') }}</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('users.name') }}</th>
                                    <th>{{ __('users.email') }}</th>
                                    <th>{{ __('users.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($role->users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ __('users.' . $user->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> {{ __('common.back') }}
    </a>
</div>
@endsection