@extends('layouts.admin')

@section('title', __('roles.edit'))
@section('page_title', __('roles.edit'))

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.roles.update', $role) }}">
            @csrf @method('PUT')

            <div class="mb-4">
                <label for="name" class="form-label fw-semibold">{{ __('roles.name') }}</label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $role->name) }}" required autofocus
                       @if ($role->name === 'Super Admin') readonly @endif>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if ($role->name === 'Super Admin')
                    <div class="form-text text-warning-emphasis">
                        <i class="bi bi-shield-fill"></i> {{ __('roles.name_locked') }}
                    </div>
                @endif
            </div>

            @include('admin.roles._permission-groups', [
                'selectedPermissions' => old('permissions', $role->permissions->pluck('name')->toArray())
            ])

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> {{ __('common.save') }}
                </button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                    {{ __('common.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection