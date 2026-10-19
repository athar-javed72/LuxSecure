<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'layout' => 'horizontal', // horizontal | compact | stacked | monogram | monogram-nostar | auto
    'surface' => 'dark', // dark | light | dual (header scroll swap)
    'division' => null,
    'showTagline' => false,
    'alt' => null,
    'width' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'layout' => 'horizontal', // horizontal | compact | stacked | monogram | monogram-nostar | auto
    'surface' => 'dark', // dark | light | dual (header scroll swap)
    'division' => null,
    'showTagline' => false,
    'alt' => null,
    'width' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    use App\Support\BrandAssets;

    $resolvedLayout = $layout === 'auto' ? 'compact' : $layout;

    $minWidth = match ($resolvedLayout) {
        'stacked' => 180,
        'compact' => 180,
        'monogram', 'monogram-nostar' => 36,
        default => 160,
    };

    $styleWidth = $width ? (int) $width : $minWidth;
    $heightPx = match ($resolvedLayout) {
        'stacked' => 72,
        'compact' => 40,
        'monogram', 'monogram-nostar' => 36,
        default => 40,
    };

    $darkSrc = BrandAssets::logo($resolvedLayout, 'dark', $division);
    $lightSrc = BrandAssets::logo($resolvedLayout, 'light', $division);
    $mobileDark = BrandAssets::logo('monogram-nostar', 'dark');
    $mobileLight = BrandAssets::logo('monogram-nostar', 'light');
    $alt = $alt ?? ($siteName ?? config('app.name'));
?>

<span <?php echo e($attributes->class(['inline-flex flex-col items-start gap-1'])); ?>>
    <?php if($surface === 'dual'): ?>
        <span class="relative hidden lg:block" style="width: <?php echo e(max($styleWidth, $minWidth)); ?>px; height: <?php echo e($heightPx); ?>px;">
            <img
                src="<?php echo e($darkSrc); ?>"
                alt="<?php echo e($alt); ?>"
                width="<?php echo e(max($styleWidth, $minWidth)); ?>"
                height="<?php echo e($heightPx); ?>"
                class="absolute inset-0 h-10 w-auto max-w-none object-contain object-left transition-opacity duration-200"
                :class="scrolled ? 'opacity-0' : 'opacity-100'"
                decoding="async"
            >
            <img
                src="<?php echo e($lightSrc); ?>"
                alt=""
                aria-hidden="true"
                width="<?php echo e(max($styleWidth, $minWidth)); ?>"
                height="<?php echo e($heightPx); ?>"
                class="absolute inset-0 h-10 w-auto max-w-none object-contain object-left transition-opacity duration-200"
                :class="scrolled ? 'opacity-100' : 'opacity-0'"
                decoding="async"
            >
        </span>
        <span class="relative block lg:hidden" style="width: 36px; height: 36px;">
            <img
                src="<?php echo e($mobileDark); ?>"
                alt="<?php echo e($alt); ?>"
                width="36"
                height="36"
                class="absolute inset-0 h-9 w-9 object-contain transition-opacity duration-200"
                :class="scrolled ? 'opacity-0' : 'opacity-100'"
                decoding="async"
            >
            <img
                src="<?php echo e($mobileLight); ?>"
                alt=""
                aria-hidden="true"
                width="36"
                height="36"
                class="absolute inset-0 h-9 w-9 object-contain transition-opacity duration-200"
                :class="scrolled ? 'opacity-100' : 'opacity-0'"
                decoding="async"
            >
        </span>
    <?php else: ?>
        <?php
            $src = BrandAssets::logo($resolvedLayout, $surface, $division);
            $mobileSrc = BrandAssets::logo('monogram-nostar', $surface);
        ?>
        <?php if(in_array($resolvedLayout, ['horizontal', 'compact', 'auto'], true)): ?>
            <img
                src="<?php echo e($src); ?>"
                alt="<?php echo e($alt); ?>"
                width="<?php echo e(max($styleWidth, $minWidth)); ?>"
                height="<?php echo e($heightPx); ?>"
                class="hidden lg:block h-10 w-auto max-w-[min(100%,200px)] object-contain object-left"
                style="min-width: <?php echo e($minWidth); ?>px;"
                decoding="async"
            >
            <img
                src="<?php echo e($mobileSrc); ?>"
                alt="<?php echo e($alt); ?>"
                width="36"
                height="36"
                class="block lg:hidden h-9 w-9 object-contain"
                decoding="async"
            >
        <?php else: ?>
            <img
                src="<?php echo e($src); ?>"
                alt="<?php echo e($alt); ?>"
                width="<?php echo e(max($styleWidth, $minWidth)); ?>"
                height="<?php echo e($heightPx); ?>"
                class="w-auto object-contain object-left"
                style="min-width: <?php echo e($minWidth); ?>px; height: <?php echo e($heightPx); ?>px;"
                decoding="async"
            >
        <?php endif; ?>
    <?php endif; ?>

    <?php if($showTagline): ?>
        <span class="font-[family-name:var(--font-display)] text-sm italic font-normal text-current/80"><?php echo e(BrandAssets::TAGLINE); ?></span>
    <?php endif; ?>
</span>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/brand-logo.blade.php ENDPATH**/ ?>