@extends('layouts.admin')

@section('title', __('units.create'))
@section('page_title', __('units.create'))
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('navigation.units') }}</li>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.units.store') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('units.name') }}</label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required autofocus>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> {{ __('common.save') ?? 'Save' }}
                </button>
                <a href="{{ route('admin.units.index') }}" class="btn btn-outline-secondary">
                    {{ __('common.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection