
@extends('layouts.guest')

@section('title', __('auth.login_title'))

@section('content')

<div class="auth-container">
    <div class="auth-card">

        {{-- Header --}}
        <div class="auth-header">

            <div class="auth-logo">
                <i class="bi bi-shield-exclamation"></i>
            </div>

            <h1 class="auth-title">
                {{ __('auth.login_title') }}
            </h1>

            <p class="auth-subtitle">
                {{ __('auth.login_subtitle') }}
            </p>

        </div>

        {{-- Session Errors --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            {{-- Email --}}
            <div class="mb-3">

                <label for="email" class="form-label">
                    {{ __('auth.email') }}
                </label>

                <input
                    type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="{{ __('auth.email_placeholder') }}"
                    required
                    autofocus
                    autocomplete="email"
                >

                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            {{-- Password --}}
            <div class="mb-3">

                <label for="password" class="form-label">
                    {{ __('auth.password') }}
                </label>

                <div class="input-group">

                    <input
                        type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        placeholder="{{ __('auth.password_placeholder') }}"
                        required
                        autocomplete="current-password"
                    >

                    <button
                        class="btn btn-outline-secondary"
                        type="button"
                        id="togglePassword"
                        aria-label="{{ __('auth.show_password') }}"
                    >
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>

                </div>

                @error('password')
                    <div class="text-danger small mt-1">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            {{-- Remember Me --}}
            <div class="mb-4 form-check">

                <input
                    type="checkbox"
                    class="form-check-input"
                    id="remember"
                    name="remember"
                    value="1"
                >

                <label class="form-check-label" for="remember">
                    {{ __('auth.remember_me') }}
                </label>

            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="btn btn-primary w-100"
                id="loginBtn"
            >

                <span
                    class="spinner-border spinner-border-sm d-none"
                    id="loginSpinner"
                    role="status"
                    aria-hidden="true"
                ></span>

                <span
                    id="loginBtnText"
                    data-loading="{{ __('auth.signing_in') }}"
                >
                    {{ __('auth.login_button') }}
                </span>

            </button>

        </form>

        {{-- Language Switch --}}
        <div class="auth-language mt-4 text-center">

            @if (app()->getLocale() === 'en')

                <a href="{{ route('language.switch', 'ar') }}">
                    <i class="bi bi-translate"></i>
                    {{ __('common.arabic') }}
                </a>

            @else

                <a href="{{ route('language.switch', 'en') }}">
                    <i class="bi bi-translate"></i>
                    {{ __('common.english') }}
                </a>

            @endif

        </div>

    </div>
</div>

@endsection
