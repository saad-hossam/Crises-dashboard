

<?php $__env->startSection('title', __('auth.login_title')); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/auth.css')); ?>?v=3">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<div class="auth-page" id="authPage">

    
    <div class="auth-background" id="authBackground">
        <div class="auth-orb auth-orb-one" data-parallax="0.03"></div>
        <div class="auth-orb auth-orb-two" data-parallax="-0.02"></div>
        <div class="auth-orb auth-orb-three" data-parallax="0.05"></div>
        <div class="auth-grid"></div>
        <div class="auth-noise"></div>
    </div>

    <div class="auth-shell">

        
        <aside class="auth-side">
            <div class="auth-side-inner">

                <div class="auth-side-brand">
                    <div class="auth-side-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <span><?php echo e(__('common.app_name')); ?></span>
                </div>

                <h2 class="auth-side-title">
                    <?php echo e(__('auth.side_title') ?? 'إدارة الأزمات، بذكاء.'); ?>

                </h2>

                <p class="auth-side-sub">
                    <?php echo e(__('auth.side_subtitle') ?? 'منصة موحّدة لمتابعة المؤشرات وتحليل البيانات واتخاذ القرار في الوقت المناسب.'); ?>

                </p>

                <ul class="auth-side-points">
                    <li><i class="bi bi-check2-circle"></i> <?php echo e(__('auth.side_point_1') ?? 'لوحات تحكم تفاعلية'); ?></li>
                    <li><i class="bi bi-check2-circle"></i> <?php echo e(__('auth.side_point_2') ?? 'تقارير فورية دقيقة'); ?></li>
                    <li><i class="bi bi-check2-circle"></i> <?php echo e(__('auth.side_point_3') ?? 'صلاحيات متعددة المستويات'); ?></li>
                </ul>

                <div class="auth-side-footer">
                    <div class="auth-avatars">
                        <span>م</span><span>س</span><span>ن</span><span>+</span>
                    </div>
                    <div class="auth-side-caption">
                        <strong><?php echo e(__('auth.side_users') ?? 'أكثر من 1,200 مستخدم'); ?></strong>
                        <small><?php echo e(__('auth.side_users_sub') ?? 'يثقون بالمنصة يومياً'); ?></small>
                    </div>
                </div>

            </div>
        </aside>

        
        <main class="auth-main">

            <div class="auth-container">

                
                <div class="auth-brand">
                    <div class="auth-brand-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <span><?php echo e(__('common.app_name')); ?></span>
                </div>

                <div class="auth-card">

                    <div class="auth-header">
                        <div class="auth-logo">
                            <i class="bi bi-person-lock"></i>
                        </div>
                        <span class="auth-eyebrow"><?php echo e(__('auth.login_title')); ?></span>
                        <h1 class="auth-title"><?php echo e(__('auth.welcome_back') ?? 'مرحباً بعودتك'); ?></h1>
                        <p class="auth-subtitle"><?php echo e(__('auth.login_subtitle')); ?></p>
                    </div>

                    
                    <?php if(session('error')): ?>
                        <div class="auth-alert" role="alert">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <span><?php echo e(session('error')); ?></span>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($errors->any()): ?>
                        <div class="auth-alert" role="alert">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <div>
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div><?php echo e($error); ?></div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('login')); ?>" id="loginForm" novalidate>
                        <?php echo csrf_field(); ?>

                        
                        <div class="auth-field" data-field="email">
                            <label for="email" class="auth-label"><?php echo e(__('auth.email')); ?></label>

                            <div class="auth-input-wrapper">
                                <i class="bi bi-envelope auth-input-icon"></i>
                                <input
                                    type="email"
                                    class="auth-input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="email"
                                    name="email"
                                    value="<?php echo e(old('email')); ?>"
                                    placeholder="<?php echo e(__('auth.email_placeholder')); ?>"
                                    required
                                    autofocus
                                    autocomplete="email"
                                    inputmode="email"
                                    spellcheck="false"
                                >
                                <span class="auth-input-status" data-status-for="email"></span>
                            </div>

                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="auth-field-error"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="auth-field" data-field="password">
                            <label for="password" class="auth-label"><?php echo e(__('auth.password')); ?></label>

                            <div class="auth-input-wrapper">
                                <i class="bi bi-lock auth-input-icon"></i>
                                <input
                                    type="password"
                                    class="auth-input <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="password"
                                    name="password"
                                    placeholder="<?php echo e(__('auth.password_placeholder')); ?>"
                                    required
                                    autocomplete="current-password"
                                >
                                <button
                                    type="button"
                                    class="auth-password-toggle"
                                    id="togglePassword"
                                    aria-label="<?php echo e(__('auth.show_password')); ?>"
                                    aria-pressed="false"
                                >
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                            </div>

                            
                            <div class="auth-hint d-none" id="capsHint">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <?php echo e(__('auth.caps_lock_on') ?? 'Caps Lock مفعّل'); ?>

                            </div>

                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="auth-field-error"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="auth-options">
                            <label class="auth-checkbox">
                                <input type="checkbox" name="remember" id="remember" value="1">
                                <span class="auth-checkmark"><i class="bi bi-check"></i></span>
                                <span><?php echo e(__('auth.remember_me')); ?></span>
                            </label>

                            <?php if(Route::has('password.request')): ?>
                                <a href="<?php echo e(route('password.request')); ?>" class="auth-forgot">
                                    <?php echo e(__('auth.forgot_password') ?? 'نسيت كلمة المرور؟'); ?>

                                </a>
                            <?php endif; ?>
                        </div>

                        
                        <button type="submit" class="auth-submit" id="loginBtn">
                            <span class="auth-submit-content" id="loginBtnText">
                                <?php echo e(__('auth.login_button')); ?>

                                <i class="bi bi-arrow-return-left"></i>
                            </span>
                            <span class="auth-submit-loading d-none" id="loginSpinner">
                                <span class="auth-spinner"></span>
                                <span><?php echo e(__('auth.signing_in')); ?></span>
                            </span>
                        </button>

                        
                        <div class="auth-kbd-hint">
                            <kbd>Enter</kbd> <?php echo e(__('auth.kbd_hint') ?? 'للدخول السريع'); ?>

                        </div>
                    </form>

                    
                    <div class="auth-language">
                        <span class="auth-language-divider"></span>
                        <?php if(app()->getLocale() === 'en'): ?>
                            <a href="<?php echo e(route('language.switch', 'ar')); ?>">
                                <i class="bi bi-translate"></i> العربية
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('language.switch', 'en')); ?>">
                                <i class="bi bi-translate"></i> English
                            </a>
                        <?php endif; ?>
                        <span class="auth-language-divider"></span>
                    </div>

                </div>

                <div class="auth-footer">
                    <i class="bi bi-shield-check"></i>
                    <span><?php echo e(__('common.app_name')); ?></span>
                </div>

            </div>
        </main>

    </div>

</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/auth.js')); ?>?v=3"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/auth/login.blade.php ENDPATH**/ ?>