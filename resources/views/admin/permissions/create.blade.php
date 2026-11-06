@extends('layouts.admin')

@section('title', __('permissions.create'))
@section('page_title', __('permissions.create'))

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.permissions.store') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('permissions.name') }}</label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required autofocus
                       placeholder="e.g. reports.export">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">{{ __('permissions.name_hint') }}</div>
            </div>

            <div class="mb-3">
                <label for="group" class="form-label">{{ __('permissions.group') }}</label>
                <input type="text" name="group" id="group" list="group-options"
                       class="form-control @error('group') is-invalid @enderror"
                       value="{{ old('group') }}" required placeholder="e.g. reports">
                <datalist id="group-options">
                    @foreach ($groups as $g)<option value="{{ $g }}"></option>@endforeach
                </datalist>
                @error('group')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">{{ __('permissions.group_hint') }}</div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">{{ __('permissions.description') }}</label>
                <textarea name="description" id="description" rows="2"
                          class="form-control @error('description') is-invalid @enderror"
                          placeholder="{{ __('permissions.description_placeholder') }}">{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> {{ __('common.save') }}
                </button>
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
                    {{ __('common.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection