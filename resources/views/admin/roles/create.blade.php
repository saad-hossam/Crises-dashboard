@extends('layouts.admin')

@section('title', __('roles.create'))
@section('page_title', __('roles.create'))

@section('content')
<div class="card">
    <div class="card-body">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf

            <div class="mb-4">
                <label for="name" class="form-label fw-semibold">{{ __('roles.name') }}</label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required autofocus
                       placeholder="{{ __('roles.name_placeholder') }}">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label for="description" class="form-label fw-semibold">{{ __('roles.description') }}</label>
                <textarea name="description" id="description" rows="2"
                          class="form-control @error('description') is-invalid @enderror"
                          placeholder="{{ __('roles.description_placeholder') }}">{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            @include('admin.roles._permission-groups', [
                'permissions' => $permissions,
                'selectedPermissions' => old('permissions', []),
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