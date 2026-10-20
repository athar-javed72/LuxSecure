<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" <?php if(!empty($isRtl)): ?> dir="rtl" <?php endif; ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', ($siteName ?? config('app.name')).' — '.\App\Support\BrandAssets::TAGLINE); ?>">
    <title><?php echo $__env->yieldContent('title', 'Home'); ?> | <?php echo e($siteName ?? config('app.name')); ?></title>
    <link rel="icon" href="<?php echo e($siteFavicon ?? \App\Support\BrandAssets::favicon()); ?>" type="image/svg+xml">
    <link rel="icon" href="/favicon-32x32.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" sizes="180x180">
    <link rel="icon" href="/android-chrome-192x192.png" type="image/png" sizes="192x192">
    <link rel="icon" href="/android-chrome-512x512.png" type="image/png" sizes="512x512">
    <meta property="og:title" content="<?php echo $__env->yieldContent('title', 'Home'); ?> | <?php echo e($siteName ?? config('app.name')); ?>">
    <meta property="og:description" content="<?php echo $__env->yieldContent('meta_description', \App\Support\BrandAssets::TAGLINE); ?>">
    <meta property="og:image" content="<?php echo e(url(\App\Support\BrandAssets::ogImage())); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/css/motion.css', 'resources/js/app.js']); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<?php
    $isHome = request()->routeIs('home');
    $headerMode = 'light';
    if ($__env->hasSection('header_mode')) {
        $headerMode = trim($__env->yieldContent('header_mode')) ?: 'light';
    } elseif ($isHome) {
        $headerMode = 'overlay';
    }
?>
<body class="min-h-screen flex flex-col bg-[var(--color-ivory)] text-[var(--color-ink)] <?php echo e(!empty($isRtl) ? 'rtl' : ''); ?>">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[100] focus:px-4 focus:py-3 focus:bg-[var(--color-forest)] focus:text-[var(--color-ivory)] focus:rounded-[var(--radius-button)]">
        Skip to main content
    </a>

    <?php echo $__env->make('include.navbar', ['headerMode' => $headerMode], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main id="main-content" class="flex-1 <?php echo e($headerMode === 'overlay' ? 'pt-0' : 'pt-[var(--header-h)]'); ?>">
        <?php echo $__env->yieldContent('home_content'); ?>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php echo $__env->make('include.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/include/master.blade.php ENDPATH**/ ?>