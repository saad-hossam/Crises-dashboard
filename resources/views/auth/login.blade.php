@extends('layouts.guest')

@section('title', __('auth.login_title'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}?v=3">
@endpush

@section('content')

<div class="auth-page" id="authPage">

    {{-- Animated background --}}
    <div class="auth-background" id="authBackground">
        <div class="auth-orb auth-orb-one" data-parallax="0.03"></div>
        <div class="auth-orb auth-orb-two" data-parallax="-0.02"></div>
        <div class="auth-orb auth-orb-three" data-parallax="0.05"></div>
        <div class="auth-grid"></div>
        <div class="auth-noise"></div>
    </div>

    <div class="auth-shell">

        {{-- LEFT: Brand / marketing panel (hidden on small screens) --}}
        <aside class="auth-side">
            <div class="auth-side-inner">

                <div class="auth-side-brand">
                    <div class="auth-side-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <span>{{ __('common.app_name') }}</span>
                </div>

                <h2 class="auth-side-title">
                    {{ __('auth.side_title') ?? 'إدارة الأزمات، بذكاء.' }}
                </h2>

                <p class="auth-side-sub">
                    {{ __('auth.side_subtitle') ?? 'منصة موحّدة لمتابعة المؤشرات وتحليل البيانات واتخاذ القرار في الوقت المناسب.' }}
                </p>

                <ul class="auth-side-points">
                    <li><i class="bi bi-check2-circle"></i> {{ __('auth.side_point_1') ?? 'لوحات تحكم تفاعلية' }}</li>
                    <li><i class="bi bi-check2-circle"></i> {{ __('auth.side_point_2') ?? 'تقارير فورية دقيقة' }}</li>
                    <li><i class="bi bi-check2-circle"></i> {{ __('auth.side_point_3') ?? 'صلاحيات متعددة المستويات' }}</li>
                </ul>

                <div class="auth-side-footer">
                    <div class="auth-avatars">
                        <span>م</span><span>س</span><span>ن</span><span>+</span>
                    </div>
                    <div class="auth-side-caption">
                        <strong>{{ __('auth.side_users') ?? 'أكثر من 1,200 مستخدم' }}</strong>
                        <small>{{ __('auth.side_users_sub') ?? 'يثقون بالمنصة يومياً' }}</small>
                    </div>
                </div>

            </div>
        </aside>

        {{-- RIGHT: Login form --}}
        <main class="auth-main">

            <div class="auth-container">

                {{-- Mobile brand --}}
                <div class="auth-brand">
                    <div class="auth-brand-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <span>{{ __('common.app_name') }}</span>
                </div>

                <div class="auth-card">

                    <div class="auth-header">
                        <div class="auth-logo">
                            <i class="bi bi-person-lock"></i>
                        </div>
                        <span class="auth-eyebrow">{{ __('auth.login_title') }}</span>
                        <h1 class="auth-title">{{ __('auth.welcome_back') ?? 'مرحباً بعودتك' }}</h1>
                        <p class="auth-subtitle">{{ __('auth.login_subtitle') }}</p>
                    </div>

                    {{-- Session error --}}
                    @if (session('error'))
                        <div class="auth-alert" role="alert">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    {{-- Validation errors --}}
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

                    <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
                        @csrf

                        {{-- Email --}}
                        <div class="auth-field" data-field="email">
                            <label for="email" class="auth-label">{{ __('auth.email') }}</label>

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
                                    inputmode="email"
                                    spellcheck="false"
                                >
                                <span class="auth-input-status" data-status-for="email"></span>
                            </div>

                            @error('email')
                                <div class="auth-field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="auth-field" data-field="password">
                            <label for="password" class="auth-label">{{ __('auth.password') }}</label>

                            <div class="auth-input-wrapper">
                                <i class="bi bi-lock auth-input-icon"></i>
                                <input
                                    type="password"
                                    class="auth-input @error('password') is-invalid @enderror"
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

                            {{-- Caps lock hint --}}
                            <div class="auth-hint d-none" id="capsHint">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                {{ __('auth.caps_lock_on') ?? 'Caps Lock مفعّل' }}
                            </div>

                            @error('password')
                                <div class="auth-field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Options --}}
                        <div class="auth-options">
                            <label class="auth-checkbox">
                                <input type="checkbox" name="remember" id="remember" value="1">
                                <span class="auth-checkmark"><i class="bi bi-check"></i></span>
                                <span>{{ __('auth.remember_me') }}</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="auth-forgot">
                                    {{ __('auth.forgot_password') ?? 'نسيت كلمة المرور؟' }}
                                </a>
                            @endif
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="auth-submit" id="loginBtn">
                            <span class="auth-submit-content" id="loginBtnText">
                                {{ __('auth.login_button') }}
                                <i class="bi bi-arrow-return-left"></i>
                            </span>
                            <span class="auth-submit-loading d-none" id="loginSpinner">
                                <span class="auth-spinner"></span>
                                <span>{{ __('auth.signing_in') }}</span>
                            </span>
                        </button>

                        {{-- Keyboard hint --}}
                        <div class="auth-kbd-hint">
                            <kbd>Enter</kbd> {{ __('auth.kbd_hint') ?? 'للدخول السريع' }}
                        </div>
                    </form>

                    {{-- Language switch --}}
                    <div class="auth-language">
                        <span class="auth-language-divider"></span>
                        @if (app()->getLocale() === 'en')
                            <a href="{{ route('language.switch', 'ar') }}">
                                <i class="bi bi-translate"></i> العربية
                            </a>
                        @else
                            <a href="{{ route('language.switch', 'en') }}">
                                <i class="bi bi-translate"></i> English
                            </a>
                        @endif
                        <span class="auth-language-divider"></span>
                    </div>

                </div>

                <div class="auth-footer">
                    <i class="bi bi-shield-check"></i>
                    <span>{{ __('common.app_name') }}</span>
                </div>

            </div>
        </main>

    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/auth.js') }}?v=3"></script>
@endpush