<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'primary',
    'surface' => 'light', // light | dark
    'size' => 'default', // default | compact
    'type' => 'button',
    'href' => null,
    'tag' => null,
    'loading' => false,
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
    'variant' => 'primary',
    'surface' => 'light', // light | dark
    'size' => 'default', // default | compact
    'type' => 'button',
    'href' => null,
    'tag' => null,
    'loading' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $height = $size === 'compact' ? 'h-11' : 'h-12';
    $base = "inline-flex items-center justify-center gap-2 {$height} px-6 text-[14px] sm:text-[15px] font-semibold tracking-[0.01em] transition-colors duration-150 disabled:opacity-50 disabled:pointer-events-none";
    $base .= ' rounded-[var(--radius-button)] focus-visible:outline-2 focus-visible:outline-offset-2';

    if ($surface === 'dark') {
        $variants = [
            'primary' => 'bg-[var(--color-gold)] text-[var(--color-forest)] hover:brightness-95 focus-visible:outline-[var(--color-gold)]',
            'secondary' => 'bg-transparent text-[var(--color-ivory)] border border-[color-mix(in_srgb,var(--color-ivory)_60%,transparent)] hover:bg-[color-mix(in_srgb,var(--color-ivory)_10%,transparent)] focus-visible:outline-[var(--color-gold)]',
            'ghost' => 'bg-transparent text-[var(--color-ivory)] border border-[color-mix(in_srgb,var(--color-ivory)_40%,transparent)] hover:bg-[color-mix(in_srgb,var(--color-ivory)_10%,transparent)] focus-visible:outline-[var(--color-gold)]',
            'ink' => 'bg-[var(--color-forest-shadow)] text-[var(--color-ivory)] hover:bg-[var(--color-forest)] focus-visible:outline-[var(--color-gold)]',
            'danger' => 'bg-fs-danger text-white hover:bg-[#991b1b] focus-visible:outline-white',
        ];
    } else {
        $variants = [
            'primary' => 'bg-[var(--color-forest)] text-[var(--color-ivory)] hover:bg-[var(--color-forest-shadow)] focus-visible:outline-[var(--color-forest)]',
            'secondary' => 'bg-transparent text-[var(--color-forest)] border border-[color-mix(in_srgb,var(--color-forest)_40%,transparent)] hover:bg-[color-mix(in_srgb,var(--color-forest)_6%,transparent)] focus-visible:outline-[var(--color-forest)]',
            'ghost' => 'bg-transparent text-[var(--color-forest)] border border-[color-mix(in_srgb,var(--color-forest)_25%,transparent)] hover:bg-[color-mix(in_srgb,var(--color-forest)_6%,transparent)] focus-visible:outline-[var(--color-forest)]',
            'ink' => 'bg-[var(--color-forest)] text-[var(--color-ivory)] hover:bg-[var(--color-forest-shadow)] focus-visible:outline-[var(--color-forest)]',
            'danger' => 'bg-fs-danger text-white hover:bg-[#991b1b] focus-visible:outline-[var(--color-forest)]',
            // Gold-dark fill + white only when contrast is safe (~5:1)
            'gold' => 'bg-[var(--color-gold-dark)] text-white hover:brightness-95 focus-visible:outline-[var(--color-gold-dark)]',
        ];
    }

    $classes = $base.' '.($variants[$variant] ?? $variants['primary']);
    $element = $tag ?? ($href ? 'a' : 'button');
?>

<?php if($element === 'a'): ?>
    <a href="<?php echo e($href); ?>" <?php echo e($attributes->merge(['class' => $classes, 'aria-busy' => $loading ? 'true' : 'false'])); ?>>
        <?php if($loading): ?>
            <span class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-current border-r-transparent" aria-hidden="true"></span>
        <?php endif; ?>
        <?php echo e($slot); ?>

    </a>
<?php else: ?>
    <button type="<?php echo e($type); ?>" <?php if($loading): echo 'disabled'; endif; ?> <?php echo e($attributes->merge(['class' => $classes, 'aria-busy' => $loading ? 'true' : 'false'])); ?>>
        <?php if($loading): ?>
            <span class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-current border-r-transparent" aria-hidden="true"></span>
        <?php endif; ?>
        <?php echo e($slot); ?>

    </button>
<?php endif; ?>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/button.blade.php ENDPATH**/ ?>