@extends('layouts.admin')

@section('title', __('users.create'))
@section('page_title', __('users.create'))

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.users.index') }}">{{ __('users.title') }}</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('users.create') }}</li>
@endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-xl-9">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-0">{{ __('users.create') }}</h2>
                <p class="text-muted small mb-0">{{ __('users.create_subtitle') }}</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> {{ __('common.back') ?? 'Back' }}
            </a>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" novalidate>
            @csrf

            <div class="card mb-3">
                <div class="card-body">

                    <h6 class="text-uppercase text-muted small mb-3">{{ __('users.basic_info') }}</h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">{{ __('users.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required autofocus>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">{{ __('users.email') }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">{{ __('users.phone') }}</label>
                            <input type="text" name="phone" id="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone') }}" placeholder="+20 …">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label">{{ __('users.status') }} <span class="text-danger">*</span></label>
                            <select name="status" id="status"
                                    class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active"   @selected(old('status', 'active') === 'active')>{{ __('users.active') }}</option>
                                <option value="inactive" @selected(old('status') === 'inactive')>{{ __('users.inactive') }}</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">

                    <h6 class="text-uppercase text-muted small mb-3">{{ __('users.credentials') }}</h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">{{ __('users.password') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="password"
                                       class="form-control @error('password') is-invalid @enderror" required>
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword('password', this)" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <small class="text-muted">{{ __('users.password_hint') }}</small>
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">{{ __('users.password_confirm') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="form-control" required>
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword('password_confirmation', this)" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">

                    <h6 class="text-uppercase text-muted small mb-3">{{ __('users.roles') }}</h6>

                    @error('roles')<div class="alert alert-danger py-2 small">{{ $message }}</div>@enderror

                    <div class="row g-2">
                        @forelse ($roles as $role)
                            <div class="col-sm-6 col-lg-4">
                                <label class="role-check card border h-100">
                                    <div class="card-body p-3 d-flex align-items-start gap-2">
                                        <input class="form-check-input mt-1" type="checkbox"
                                               name="roles[]" value="{{ $role->name }}"
                                               @checked(in_array($role->name, old('roles', []), true))>
                                        <div class="flex-grow-1">
                                            <div class="fw-medium">{{ $role->name }}</div>
                                            <div class="text-muted small">
                                                {{ $role->permissions->count() }} {{ __('users.permissions_count') }}
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @empty
                            <div class="col-12 text-muted small">{{ __('users.no_roles_defined') }}</div>
                        @endforelse
                    </div>

                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> {{ __('common.save') ?? 'Save' }}
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    {{ __('common.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    if (!input) return;
    const isPwd = input.type === 'password';
    input.type = isPwd ? 'text' : 'password';
    btn.innerHTML = isPwd ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
}
</script>
@endpush

@push('styles')
<style>
.role-check { cursor: pointer; transition: border-color .15s, box-shadow .15s, transform .15s; }
.role-check:hover { border-color: var(--admin-accent) !important; transform: translateY(-1px); box-shadow: var(--admin-shadow); }
.role-check:has(input:checked) { border-color: var(--admin-accent) !important; background: rgba(79,156,249,.04); }
</style>
@endpush