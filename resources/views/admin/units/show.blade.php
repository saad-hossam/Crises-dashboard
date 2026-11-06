@extends('layouts.admin')

@section('title', __('units.show'))
@section('page_title', __('units.show'))

@section('content')
<div class="card">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">{{ __('units.name') }}</dt>
            <dd class="col-sm-9">{{ $unit->name }}</dd>

            <dt class="col-sm-3">{{ __('units.created_at') }}</dt>
            <dd class="col-sm-9">{{ $unit->created_at->format('Y-m-d H:i') }}</dd>

            <dt class="col-sm-3">{{ __('units.updated_at') }}</dt>
            <dd class="col-sm-9">{{ $unit->updated_at->format('Y-m-d H:i') }}</dd>
        </dl>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('admin.units.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> {{ __('common.back') ?? 'Back' }}
    </a>
</div>
@endsection