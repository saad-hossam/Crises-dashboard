@extends('layouts.admin')
@section('title', __('navigation.settings'))
@section('page_title', __('navigation.settings'))
@section('content')
<div class="mb-4">
    <h4 class="fw-bold"><i class="bi bi-gear-fill text-primary me-2"></i>{{ __('navigation.settings') }}</h4>
    <p class="text-muted small">{{ __('settings.subtitle') }}</p>
</div>
@if(session('success'))
<div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
@endif
<div class="row g-4">
    <div class="col-lg-6">
        <div class="panel-card h-100">
            <div class="panel-head"><i class="bi bi-person-circle me-2"></i>{{ __('settings.profile') }}</div>
            <div class="panel-body">
                <form method="POST" action="{{ route('admin.settings.profile') }}">@csrf @method('PUT')
                    <div class="mb-3"><label class="form-label">{{ __('settings.name') }}</label>
                        <input name="name" class="form-control" value="{{ old('name',$user->name) }}" required></div>
                    <div class="mb-3"><label class="form-label">{{ __('settings.email') }}</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email',$user->email) }}" required></div>
                    <div class="mb-3"><label class="form-label">{{ __('settings.phone') }}</label>
                        <input name="phone" class="form-control" value="{{ old('phone',$user->phone) }}"></div>
                    <button class="btn btn-primary">{{ __('common.save') }}</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel-card h-100">
            <div class="panel-head"><i class="bi bi-shield-lock me-2"></i>{{ __('settings.password') }}</div>
            <div class="panel-body">
                <form method="POST" action="{{ route('admin.settings.password') }}">@csrf @method('PUT')
                    <div class="mb-3"><label class="form-label">{{ __('settings.current_password') }}</label>
                        <input type="password" name="current_password" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">{{ __('settings.new_password') }}</label>
                        <input type="password" name="password" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">{{ __('settings.confirm_password') }}</label>
                        <input type="password" name="password_confirmation" class="form-control" required></div>
                    <button class="btn btn-warning">{{ __('settings.change_password') }}</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="panel-card">
            <div class="panel-head"><i class="bi bi-sliders me-2"></i>{{ __('settings.preferences') }}</div>
            <div class="panel-body">
                <form method="POST" action="{{ route('admin.settings.preferences') }}" class="row g-3 align-items-end" id="prefsForm">
                    @csrf @method('PUT')
                    <div class="col-md-3">
                        <label class="form-label">{{ __('common.language') }}</label>
                        <select name="locale" class="form-select">
                            <option value="ar" @selected(app()->getLocale()==='ar')>{{ __('common.arabic') ?? 'العربية' }}</option>
                            <option value="en" @selected(app()->getLocale()==='en')>English</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('settings.theme') }}</label>
                        <select name="theme" class="form-select" id="prefTheme">
                            <option value="light" @selected(session('theme', request()->cookie('aoi_theme','light'))==='light')>Light</option>
                            <option value="dark" @selected(session('theme', request()->cookie('aoi_theme'))==='dark')>Dark</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('settings.palette') }}</label>
                        <select name="palette" class="form-select" id="prefPalette">
                            @foreach(['navy'=>'Navy','graphite'=>'Graphite','forest'=>'Forest','steel'=>'Steel','wine'=>'Wine'] as $k=>$v)
                                <option value="{{ $k }}" @selected(session('palette', request()->cookie('aoi_palette','navy'))===$k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" type="submit">{{ __('common.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@if(session('flash_theme') || session('flash_palette'))
@push('scripts')
<script>
localStorage.setItem('admin-theme', @json(session('flash_theme', 'light')));
localStorage.setItem('aoi-palette', @json(session('flash_palette', 'indigo')));
document.documentElement.setAttribute('data-palette', @json(session('flash_palette', 'indigo')));
const w = document.getElementById('adminWrapper');
if (w) w.classList.toggle('dark-mode', @json(session('flash_theme')==='dark'));
document.body.classList.toggle('dark-mode', @json(session('flash_theme')==='dark'));
</script>
@endpush
@endif
@endsection
