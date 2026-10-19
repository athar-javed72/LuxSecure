<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'layout' => 'horizontal', // horizontal | stacked | monogram | monogram-nostar | auto
    'surface' => 'dark', // dark | light | dual (header scroll swap)
    'division' => null,
    'showTagline' => false,
    'alt' => 'Maskanoor Group',
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
    'layout' => 'horizontal', // horizontal | stacked | monogram | monogram-nostar | auto
    'surface' => 'dark', // dark | light | dual (header scroll swap)
    'division' => null,
    'showTagline' => false,
    'alt' => 'Maskanoor Group',
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

    $resolvedLayout = $layout;
    if ($layout === 'auto') {
        $resolvedLayout = 'horizontal';
    }

    $minWidth = match ($resolvedLayout) {
        'stacked' => 140,
        'monogram', 'monogram-nostar' => 24,
        default => 120,
    };

    $styleWidth = $width ? (int) $width : $minWidth;
    $heightClass = match ($resolvedLayout) {
        'stacked' => 'h-14 sm:h-16',
        'monogram', 'monogram-nostar' => 'h-8 w-8 sm:h-9 sm:w-9',
        default => 'h-9 sm:h-10',
    };

    $darkSrc = BrandAssets::logo(
        $resolvedLayout === 'horizontal' ? 'horizontal' : $resolvedLayout,
        'dark',
        $division
    );
    $lightSrc = BrandAssets::logo(
        $resolvedLayout === 'horizontal' ? 'horizontal' : $resolvedLayout,
        'light',
        $division
    );
    $mobileDark = BrandAssets::logo('monogram-nostar', 'dark');
    $mobileLight = BrandAssets::logo('monogram-nostar', 'light');
?>

<span <?php echo e($attributes->class(['inline-flex flex-col items-start gap-1'])); ?>>
    <?php if($surface === 'dual'): ?>
        
        <span class="relative hidden min-[480px]:block" style="width: <?php echo e(max($styleWidth, $minWidth)); ?>px; height: 40px;">
            <img
                src="<?php echo e($darkSrc); ?>"
                alt="<?php echo e($alt); ?>"
                width="<?php echo e(max($styleWidth, $minWidth)); ?>"
                height="40"
                class="absolute inset-0 h-10 w-auto max-w-none object-contain object-left transition-opacity duration-200"
                :class="scrolled ? 'opacity-0' : 'opacity-100'"
                decoding="async"
            >
            <img
                src="<?php echo e($lightSrc); ?>"
                alt=""
                aria-hidden="true"
                width="<?php echo e(max($styleWidth, $minWidth)); ?>"
                height="40"
                class="absolute inset-0 h-10 w-auto max-w-none object-contain object-left transition-opacity duration-200"
                :class="scrolled ? 'opacity-100' : 'opacity-0'"
                decoding="async"
            >
        </span>
        
        <span class="relative block min-[480px]:hidden" style="width: 36px; height: 36px;">
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
        <?php if(in_array($resolvedLayout, ['horizontal', 'auto'], true)): ?>
            <img
                src="<?php echo e($src); ?>"
                alt="<?php echo e($alt); ?>"
                width="<?php echo e(max($styleWidth, $minWidth)); ?>"
                height="40"
                class="hidden min-[480px]:block <?php echo e($heightClass); ?> w-auto max-w-[min(100%,220px)] object-contain object-left"
                style="min-width: <?php echo e($minWidth); ?>px;"
                decoding="async"
            >
            <img
                src="<?php echo e($mobileSrc); ?>"
                alt="<?php echo e($alt); ?>"
                width="36"
                height="36"
                class="block min-[480px]:hidden h-9 w-9 object-contain"
                decoding="async"
            >
        <?php else: ?>
            <img
                src="<?php echo e($src); ?>"
                alt="<?php echo e($alt); ?>"
                class="<?php echo e($heightClass); ?> w-auto object-contain object-left"
                style="min-width: <?php echo e($minWidth); ?>px;"
                decoding="async"
            >
        <?php endif; ?>
    <?php endif; ?>

    <?php if($showTagline): ?>
        <span class="fs-display text-sm italic text-current/80"><?php echo e(BrandAssets::TAGLINE); ?></span>
    <?php endif; ?>
</span>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/brand-logo.blade.php ENDPATH**/ ?>