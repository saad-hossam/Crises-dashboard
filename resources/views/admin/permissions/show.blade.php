@extends('layouts.admin')

@section('title', __('permissions.show'))
@section('page_title', __('permissions.show'))

@section('content')
<div class="row g-3">
    <div class="col-md-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">{{ __('permissions.details') }}</h5>
                <dl class="row mb-0">
                    <dt class="col-sm-4">{{ __('permissions.name') }}</dt>
                    <dd class="col-sm-8"><code>{{ $permission->name }}</code></dd>

                    <dt class="col-sm-4">{{ __('permissions.group') }}</dt>
                    <dd class="col-sm-8">{{ $permission->group ?? '—' }}</dd>

                    <dt class="col-sm-4">{{ __('permissions.description') }}</dt>
                    <dd class="col-sm-8">{{ $permission->description ?? '—' }}</dd>

                    <dt class="col-sm-4">{{ __('permissions.created_at') }}</dt>
                    <dd class="col-sm-8">{{ $permission->created_at?->format('Y-m-d H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">{{ __('permissions.roles_with_permission') }}</h5>
                @if ($permission->roles->isEmpty())
                    <p class="text-muted mb-0">{{ __('permissions.no_roles') }}</p>
                @else
                    @foreach ($permission->roles as $role)
                        <span class="badge bg-primary-subtle text-primary-emphasis me-1 mb-1">
                            {{ $role->name }}
                        </span>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> {{ __('common.back') }}
    </a>
</div>
@endsection