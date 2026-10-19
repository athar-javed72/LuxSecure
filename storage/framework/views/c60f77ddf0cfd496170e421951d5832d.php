<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'tone' => 'brand',
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
    'tone' => 'brand',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $tones = [
        'brand' => 'bg-[var(--color-ivory-50)] text-[var(--color-forest)] border border-[var(--color-border)]',
        'sale' => 'bg-[var(--color-forest)] text-[var(--color-ivory)] border border-transparent',
        'rent' => 'bg-[var(--color-gold)] text-[var(--color-forest)] border border-transparent',
        'stay' => 'bg-[var(--color-ivory-50)] text-[var(--color-forest)] border border-[var(--color-border)]',
        'muted' => 'bg-[var(--color-stone)] text-[var(--color-ink)] border border-transparent',
        'danger' => 'bg-fs-danger/10 text-fs-danger border border-transparent',
        'success' => 'bg-[var(--color-ivory-50)] text-[var(--color-forest)] border border-[var(--color-border)]',
        'warning' => 'bg-amber-100 text-amber-900 border border-transparent',
        'sample' => 'bg-[var(--color-ivory)] text-[var(--color-muted)] border border-[var(--color-border)]',
    ];
?>

<span <?php echo e($attributes->merge(['class' => 'inline-flex items-center px-2.5 h-6 rounded text-[11px] sm:text-xs font-semibold uppercase tracking-wide ' . ($tones[$tone] ?? $tones['brand'])])); ?>>
    <?php echo e($slot); ?>

</span>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/badge.blade.php ENDPATH**/ ?>