<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Admin'); ?> | <?php echo e($siteName ?? config('app.name')); ?></title>
    <link rel="icon" href="<?php echo e($siteFavicon ?? \App\Support\BrandAssets::favicon()); ?>" type="image/svg+xml">
    <link rel="icon" href="/favicon-32x32.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" sizes="180x180">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body
    class="min-h-screen bg-fs-surface text-fs-primary"
    x-data="adminShell"
>
    <aside
        class="fixed inset-y-0 left-0 z-40 w-64 bg-[#142A25] text-white transform transition-transform duration-200 lg:translate-x-0"
        :class="sidebarClass()"
        :inert="sidebarHidden"
        :aria-hidden="ariaHidden()"
        aria-label="Admin sidebar"
    >
        <div class="h-[72px] px-4 flex items-center justify-between gap-2 border-b border-white/10">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center gap-2 min-w-0 rounded-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
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
                <span class="font-bold truncate text-sm">Admin</span>
            </a>
            <button
                type="button"
                class="lg:hidden inline-flex items-center justify-center w-11 h-11 rounded-sm border border-white/20 shrink-0"
                @click="closeSidebar()"
                aria-label="Close sidebar"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-72px)]">
            <?php $__currentLoopData = config('navigation.admin', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $perms = $item['permission'] ?? null;
                    $allowed = $perms === null;
                    if (is_string($perms)) {
                        $allowed = auth()->user()?->can($perms) ?? false;
                    } elseif (is_array($perms)) {
                        $allowed = collect($perms)->contains(fn ($p) => auth()->user()?->can($p));
                    }
                    $active = false;
                    foreach (($item['active'] ?? []) as $pattern) {
                        if (request()->routeIs($pattern)) { $active = true; break; }
                    }
                    try { $href = route($item['route']); } catch (\Throwable $e) { $href = '#'; }
                ?>
                <?php if($allowed): ?>
                    <a href="<?php echo e($href); ?>"
                       class="flex items-center min-h-11 px-4 rounded-sm text-sm font-medium transition-colors duration-150 <?php echo e($active ? 'bg-white/15 text-white' : 'text-fs-muted hover:bg-white/10 hover:text-white'); ?>"
                       @click="closeSidebar()">
                        <?php echo e($item['label']); ?>

                    </a>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('home')); ?>" class="flex items-center min-h-11 px-4 rounded-sm text-sm font-medium text-fs-muted hover:bg-white/10 hover:text-white transition-colors duration-150">View site</a>
        </nav>
    </aside>

    <div class="min-h-screen flex flex-col lg:pl-64">
        <header class="sticky top-0 z-30 h-[72px] bg-white border-b border-black/10 flex items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <button
                type="button"
                class="lg:hidden inline-flex items-center justify-center w-11 h-11 rounded-sm border border-black/10 shrink-0"
                @click="openSidebar()"
                aria-label="Open sidebar"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-fs-primary truncate"><?php echo $__env->yieldContent('title', 'Admin'); ?></p>
                <p class="text-xs text-fs-muted truncate">Signed in as <?php echo e(auth()->user()->name); ?></p>
            </div>
            <form method="POST" action="<?php echo e(route('logout')); ?>" class="shrink-0">
                <?php echo csrf_field(); ?>
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit','variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'ghost']); ?>Logout <?php echo $__env->renderComponent(); ?>
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
        </header>

        <div
            x-cloak
            x-show="showOverlay"
            class="fixed inset-0 z-30 bg-fs-primary/40 lg:hidden"
            @click="closeSidebar()"
        ></div>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            <div class="w-full max-w-[1600px]">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </main>

        <footer class="border-t border-black/10 bg-white px-4 sm:px-6 lg:px-8 py-4 text-xs text-fs-muted flex flex-col sm:flex-row sm:justify-between gap-2">
            <span>© <?php echo e(date('Y')); ?> <?php echo e($siteName ?? config('app.name')); ?> Admin</span>
            <?php echo $__env->make('include.branding', ['brandingClass' => 'text-fs-muted', 'strongClass' => 'text-fs-brand font-medium'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </footer>
    </div>

    <?php if (isset($component)) { $__componentOriginal17dbdda2e5e455c7135d48ee27e4f262 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal17dbdda2e5e455c7135d48ee27e4f262 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toast-host','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toast-host'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal17dbdda2e5e455c7135d48ee27e4f262)): ?>
<?php $attributes = $__attributesOriginal17dbdda2e5e455c7135d48ee27e4f262; ?>
<?php unset($__attributesOriginal17dbdda2e5e455c7135d48ee27e4f262); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal17dbdda2e5e455c7135d48ee27e4f262)): ?>
<?php $component = $__componentOriginal17dbdda2e5e455c7135d48ee27e4f262; ?>
<?php unset($__componentOriginal17dbdda2e5e455c7135d48ee27e4f262); ?>
<?php endif; ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/admin/layout.blade.php ENDPATH**/ ?>