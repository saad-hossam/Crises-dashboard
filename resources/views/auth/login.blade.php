
@extends('layouts.guest')

@section('title', __('auth.login_title'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}?v=3">
@endpush

@section('content')

<div class="auth-page">

    {{-- Animated Background --}}
    <div class="auth-background">
        <div class="auth-orb auth-orb-one"></div>
        <div class="auth-orb auth-orb-two"></div>
        <div class="auth-orb auth-orb-three"></div>
        <div class="auth-grid"></div>
    </div>

    <div class="auth-container">

        {{-- Brand --}}
        <div class="auth-brand">
            <div class="auth-brand-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>

            <span>{{ __('common.app_name') }}</span>
        </div>

        {{-- Login Card --}}
        <div class="auth-card">

            <div class="auth-header">

                <div class="auth-logo">
                    <i class="bi bi-person-lock"></i>
                </div>

                <span class="auth-eyebrow">
                    {{ __('auth.login_title') }}
                </span>

                <h1 class="auth-title">
                    {{ __('auth.login_title') }}
                </h1>

                <p class="auth-subtitle">
                    {{ __('auth.login_subtitle') }}
                </p>

            </div>

            {{-- Session Error --}}
            @if (session('error'))
                <div class="auth-alert" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="auth-alert" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('login') }}"
                id="loginForm"
            >
                @csrf

                {{-- Email --}}
                <div class="auth-field">

                    <label for="email" class="auth-label">
                        {{ __('auth.email') }}
                    </label>

                    <div class="auth-input-wrapper">
                        <i class="bi bi-envelope auth-input-icon"></i>

                        <input
                            type="email"
                            class="auth-input @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="{{ __('auth.email_placeholder') }}"
                            required
                            autofocus
                            autocomplete="email"
                        >
                    </div>

                    @error('email')
                        <div class="auth-field-error">{{ $message }}</div>
                    @enderror

                </div>

                {{-- Password --}}
                <div class="auth-field">

                    <label for="password" class="auth-label">
                        {{ __('auth.password') }}
                    </label>

                    <div class="auth-input-wrapper">
                        <i class="bi bi-lock auth-input-icon"></i>

                        <input
                            type="password"
                            class="auth-input"
                            id="password"
                            name="password"
                            placeholder="{{ __('auth.password_placeholder') }}"
                            required
                            autocomplete="current-password"
                        >

                        <button
                            type="button"
                            class="auth-password-toggle"
                            id="togglePassword"
                            aria-label="{{ __('auth.show_password') }}"
                            aria-pressed="false"
                        >
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>

                    @error('password')
                        <div class="auth-field-error">{{ $message }}</div>
                    @enderror

                </div>

                {{-- Remember Me --}}
                <div class="auth-options">

                    <label class="auth-checkbox">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            value="1"
                        >

                        <span class="auth-checkmark">
                            <i class="bi bi-check"></i>
                        </span>

                        <span>{{ __('auth.remember_me') }}</span>
                    </label>

                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="auth-submit"
                    id="loginBtn"
                >
                    <span class="auth-submit-content" id="loginBtnText">
                        {{ __('auth.login_button') }}
                        <i class="bi bi-arrow-left-right"></i>
                    </span>

                    <span class="auth-submit-loading d-none" id="loginSpinner">
                        <span class="auth-spinner"></span>
                        <span>{{ __('auth.signing_in') }}</span>
                    </span>
                </button>

            </form>

            {{-- Language Switch --}}
            <div class="auth-language">
                <span class="auth-language-divider"></span>

                @if (app()->getLocale() === 'en')
                    <a href="{{ route('language.switch', 'ar') }}">
                        <i class="bi bi-translate"></i>
                        العربية
                    </a>
                @else
                    <a href="{{ route('language.switch', 'en') }}">
                        <i class="bi bi-translate"></i>
                        English
                    </a>
                @endif

                <span class="auth-language-divider"></span>
            </div>

        </div>

        {{-- Footer --}}
        <div class="auth-footer">
            <i class="bi bi-shield-check"></i>
            <span>{{ __('common.app_name') }}</span>
        </div>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/auth.js') }}?v=3"></script>
@endpush
