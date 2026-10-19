<?php
    use Illuminate\Support\Facades\Auth;

    $variant = $variant ?? (request()->routeIs('admin.*') && Auth::check() && method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin() ? 'admin' : 'public');
    $isAuthenticated = Auth::check();
    $isAdmin = $isAuthenticated && method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin();

    if ($variant === 'admin') {
        $menuConfig = config('navigation.admin', []);
    } elseif ($isAuthenticated && !$isAdmin) {
        $menuConfig = config('navigation.user', []);
    } else {
        $menuConfig = config('navigation.public', []);
    }

    $resolveHref = function (array $item) {
        if (!empty($item['href'])) {
            return $item['href'];
        }
        try {
            $url = !empty($item['route']) ? route($item['route']) : url('/');
        } catch (\Throwable $e) {
            $url = url('/');
        }
        if (!empty($item['anchor'])) {
            $url .= $item['anchor'];
        }
        return $url;
    };

    $isActive = function (array $patterns) {
        foreach ($patterns as $pattern) {
            if (request()->routeIs($pattern)) {
                return true;
            }
        }
        return false;
    };
?>

<header
    class="fixed top-0 left-0 w-full z-50 h-[72px] border-b backdrop-blur-xl transition-colors duration-200"
    :class="scrolled
        ? 'border-black/10 bg-[#F5F0E6]/95 shadow-sm'
        : 'border-white/10 bg-[#142A25]/97 shadow-lg'"
    x-data="navBar"
    @keydown.escape.window="closeOverlays()"
>
    <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => ['class' => 'h-full flex items-center justify-between gap-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-full flex items-center justify-between gap-4']); ?>
        <a href="<?php echo e($variant === 'admin' ? route('admin.dashboard') : route('home')); ?>"
           class="flex items-center gap-3 shrink-0 rounded-sm focus-visible:outline-2 focus-visible:outline-offset-2"
           :class="scrolled ? 'focus-visible:outline-[#142A25]' : 'focus-visible:outline-white'">
            <?php if (isset($component)) { $__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.brand-logo','data' => ['layout' => 'horizontal','surface' => 'dual','class' => 'shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('brand-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['layout' => 'horizontal','surface' => 'dual','class' => 'shrink-0']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3)): ?>
<?php $attributes = $__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3; ?>
<?php unset($__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3)): ?>
<?php $component = $__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3; ?>
<?php unset($__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3); ?>
<?php endif; ?>
            <?php if($variant === 'admin'): ?>
                <span class="hidden sm:inline text-[10px] tracking-[0.18em] uppercase transition-colors duration-200"
                      :class="scrolled ? 'text-[#142A25]/70' : 'text-slate-300/80'">Admin Control</span>
            <?php endif; ?>
        </a>

        <nav class="hidden lg:flex items-center gap-1" aria-label="Main navigation">
            <?php $__currentLoopData = $menuConfig; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $active = $isActive($item['active'] ?? []); ?>
                <a href="<?php echo e($resolveHref($item)); ?>"
                   class="inline-flex items-center h-11 px-4 rounded-full text-sm font-medium transition-colors duration-150"
                   :class="scrolled
                       ? (<?php echo e($active ? 'true' : 'false'); ?> ? 'bg-[#142A25]/10 text-[#142A25]' : 'text-[#142A25]/80 hover:text-[#142A25] hover:bg-[#142A25]/8')
                       : (<?php echo e($active ? 'true' : 'false'); ?> ? 'bg-white/15 text-white' : 'text-slate-200/90 hover:text-white hover:bg-white/10')">
                    <?php echo e($item['label']); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>

        <div class="hidden lg:flex items-center gap-2">
            <?php if(($countries ?? collect())->isNotEmpty() && $variant !== 'admin'): ?>
                <div class="relative" @click.outside="closeLocale()">
                    <button type="button"
                            class="inline-flex items-center h-11 px-3 rounded-full text-xs font-medium transition-colors duration-150"
                            :class="scrolled ? 'bg-[#142A25]/10 text-[#142A25] hover:bg-[#142A25]/15' : 'bg-white/10 text-white hover:bg-white/15'"
                            @click="toggleLocale()"
                            :aria-expanded="localeExpanded()">
                        <?php echo e(optional($activeCountry)->iso2 ?? 'PK'); ?> · <?php echo e(session('currency', optional($activeCountry)->currency_code ?? 'PKR')); ?>

                    </button>
                    <div x-cloak x-show="localeOpen" class="absolute right-0 top-full mt-2 w-64 rounded-md bg-white shadow-lg border border-slate-100 p-3 z-50 text-fs-primary">
                        <form method="POST" action="<?php echo e(route('locale.update')); ?>" x-ref="localeForm" class="space-y-2">
                            <?php echo csrf_field(); ?>
                            <label class="block text-xs font-semibold text-fs-muted">Country
                                <select name="country_id" class="mt-1 w-full rounded-sm border border-black/10 px-2 py-2 text-sm" @change="submitLocaleForm()">
                                    <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($country->id); ?>" <?php if((int) session('country_id', optional($activeCountry)->id) === (int) $country->id): echo 'selected'; endif; ?>><?php echo e($country->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </label>
                            <label class="block text-xs font-semibold text-fs-muted">Currency
                                <select name="currency" class="mt-1 w-full rounded-sm border border-black/10 px-2 py-2 text-sm" @change="submitLocaleForm()">
                                    <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($currency->code); ?>" <?php if(session('currency', optional($activeCountry)->currency_code) === $currency->code): echo 'selected'; endif; ?>><?php echo e($currency->code); ?> (<?php echo e($currency->symbol); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </label>
                            <label class="block text-xs font-semibold text-fs-muted">Language
                                <select name="locale" class="mt-1 w-full rounded-sm border border-black/10 px-2 py-2 text-sm" @change="submitLocaleForm()">
                                    <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($language->code); ?>" <?php if(app()->getLocale() === $language->code): echo 'selected'; endif; ?>><?php echo e($language->native_name ?: $language->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </label>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
            <?php if($variant === 'admin'): ?>
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'ghost','href' => ''.e(route('home')).'','class' => '!border-white/20 !text-white hover:!bg-white/10']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'ghost','href' => ''.e(route('home')).'','class' => '!border-white/20 !text-white hover:!bg-white/10']); ?>View site <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'secondary','type' => 'submit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','type' => 'submit']); ?>Logout <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                </form>
            <?php elseif(!$isAuthenticated): ?>
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'ghost','href' => ''.e(route('login.form')).'','class' => '!border-white/20 !text-white hover:!bg-white/10']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'ghost','href' => ''.e(route('login.form')).'','class' => '!border-white/20 !text-white hover:!bg-white/10']); ?>Login <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'secondary','href' => ''.e(route('register.form')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','href' => ''.e(route('register.form')).'']); ?>Register <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','href' => ''.e(route('properties')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','href' => ''.e(route('properties')).'']); ?>Explore <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            <?php else: ?>
                <?php if($isAdmin): ?>
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'secondary','href' => ''.e(route('admin.dashboard')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','href' => ''.e(route('admin.dashboard')).'']); ?>Admin <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                <?php endif; ?>
                <?php
                    $notifConfig = [
                        'count' => Auth::user()->unreadNotifications()->count(),
                        'url' => route('notifications.unread-count'),
                    ];
                ?>
                <a href="<?php echo e(route('notifications.index')); ?>" class="relative inline-flex items-center justify-center w-11 h-11 rounded-full bg-white/10 text-white hover:bg-white/15 transition-colors duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-fs-brand" aria-label="Notifications"
                   x-data="notifCount"
                   data-alpine-config="<?php echo e(json_encode($notifConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)); ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 11-6 0"/></svg>
                    <span x-show="hasCount" x-text="count" class="absolute -top-1 -right-1 min-w-[1.25rem] h-5 px-1 rounded-full bg-fs-brand text-[10px] font-bold flex items-center justify-center"></span>
                </a>
                <div class="relative" @click.outside="closeProfile()">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 h-11 px-3 rounded-full bg-white/10 text-white text-sm font-medium hover:bg-white/15 transition-colors duration-150"
                        @click="toggleProfile()"
                        :aria-expanded="profileExpanded()"
                    >
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-fs-brand-light text-fs-brand text-xs font-bold uppercase">
                            <?php echo e(mb_substr(Auth::user()->name, 0, 1, 'UTF-8')); ?>

                        </span>
                        <span class="max-w-[120px] truncate"><?php echo e(Auth::user()->name); ?></span>
                    </button>
                    <div x-cloak x-show="profileOpen" class="absolute right-0 top-full mt-2 w-56 rounded-md bg-white shadow-lg border border-slate-100 py-2 z-50">
                        <a href="<?php echo e(route('profile')); ?>" class="block px-4 py-2.5 text-sm text-fs-secondary hover:bg-slate-50 hover:text-fs-primary">My Profile</a>
                        <a href="<?php echo e(route('notifications.index')); ?>" class="block px-4 py-2.5 text-sm text-fs-secondary hover:bg-slate-50 hover:text-fs-primary">Notifications</a>
                        <a href="<?php echo e(route('phone.verify')); ?>" class="block px-4 py-2.5 text-sm text-fs-secondary hover:bg-slate-50 hover:text-fs-primary">Verify phone</a>
                        <a href="<?php echo e(route('kyc.index')); ?>" class="block px-4 py-2.5 text-sm text-fs-secondary hover:bg-slate-50 hover:text-fs-primary">KYC</a>
                        <a href="<?php echo e(route('profile')); ?>#favorites" class="block px-4 py-2.5 text-sm text-fs-secondary hover:bg-slate-50 hover:text-fs-primary">Saved Properties</a>
                        <form method="POST" action="<?php echo e(route('logout')); ?>" class="border-t border-slate-100 mt-1 pt-1">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-fs-danger hover:bg-red-50">Logout</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="flex items-center gap-2 lg:hidden">
            <?php if(auth()->guard()->guest()): ?>
                <a href="<?php echo e(route('login.form')); ?>"
                   class="inline-flex items-center h-11 px-3 rounded-full text-xs font-semibold border transition-colors duration-150"
                   :class="scrolled ? 'text-[#142A25] border-[#142A25]/25' : 'text-white border-white/20'">Login</a>
            <?php endif; ?>
            <button
                type="button"
                class="inline-flex items-center justify-center w-11 h-11 rounded-full border transition-colors duration-150"
                :class="scrolled ? 'border-[#142A25]/25 text-[#142A25] hover:bg-[#142A25]/8' : 'border-white/20 text-white hover:bg-white/10'"
                @click="openNav()"
                aria-label="Open menu"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala766c2d312d6f7864fe218e2500d2bba)): ?>
<?php $attributes = $__attributesOriginala766c2d312d6f7864fe218e2500d2bba; ?>
<?php unset($__attributesOriginala766c2d312d6f7864fe218e2500d2bba); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala766c2d312d6f7864fe218e2500d2bba)): ?>
<?php $component = $__componentOriginala766c2d312d6f7864fe218e2500d2bba; ?>
<?php unset($__componentOriginala766c2d312d6f7864fe218e2500d2bba); ?>
<?php endif; ?>

    <div x-cloak x-show="navOpen" class="lg:hidden fixed inset-0 z-[60]" role="dialog" aria-modal="true" aria-label="Mobile navigation">
        <div class="absolute inset-0 bg-fs-primary/50" @click="closeNav()"></div>
        <div
            class="absolute inset-y-0 right-0 w-full max-w-sm bg-fs-primary text-white shadow-lg flex flex-col"
            x-show="navOpen"
            x-transition:enter="transition transform duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition transform duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
        >
            <div class="h-[72px] px-4 flex items-center justify-between border-b border-white/10">
                <?php if (isset($component)) { $__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.brand-logo','data' => ['layout' => 'monogram-nostar','surface' => 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('brand-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['layout' => 'monogram-nostar','surface' => 'dark']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3)): ?>
<?php $attributes = $__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3; ?>
<?php unset($__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3)): ?>
<?php $component = $__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3; ?>
<?php unset($__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3); ?>
<?php endif; ?>
                <button type="button" class="inline-flex items-center justify-center w-11 h-11 rounded-sm border border-white/20" @click="closeNav()" aria-label="Close menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                <?php $__currentLoopData = $menuConfig; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($resolveHref($item)); ?>" class="flex items-center min-h-11 px-4 rounded-sm text-sm font-medium text-slate-100 hover:bg-white/10" @click="closeNav()"><?php echo e($item['label']); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </nav>
            <div class="p-4 border-t border-white/10 space-y-2">
                <?php if(auth()->guard()->guest()): ?>
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'ghost','href' => ''.e(route('login.form')).'','class' => 'w-full !border-white/20 !text-white hover:!bg-white/10']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'ghost','href' => ''.e(route('login.form')).'','class' => 'w-full !border-white/20 !text-white hover:!bg-white/10']); ?>Login <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'secondary','href' => ''.e(route('register.form')).'','class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','href' => ''.e(route('register.form')).'','class' => 'w-full']); ?>Register <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','href' => ''.e(route('properties')).'','class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','href' => ''.e(route('properties')).'','class' => 'w-full']); ?>Explore Properties <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                <?php else: ?>
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'ghost','href' => ''.e(route('profile')).'','class' => 'w-full !border-white/20 !text-white hover:!bg-white/10']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'ghost','href' => ''.e(route('profile')).'','class' => 'w-full !border-white/20 !text-white hover:!bg-white/10']); ?>Account <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'secondary','type' => 'submit','class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','type' => 'submit','class' => 'w-full']); ?>Logout <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/include/navbar.blade.php ENDPATH**/ ?>