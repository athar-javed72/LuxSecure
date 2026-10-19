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
        'brand' => 'bg-fs-brand-light text-fs-primary',
        'sale' => 'bg-fs-brand text-white',
        'rent' => 'bg-fs-accent text-white',
        'muted' => 'bg-black/5 text-fs-secondary',
        'danger' => 'bg-fs-danger/10 text-fs-danger',
        'success' => 'bg-fs-brand-light text-fs-primary',
        'warning' => 'bg-amber-100 text-amber-900',
    ];
?>

<span <?php echo e($attributes->merge(['class' => 'inline-flex items-center px-3 h-7 rounded-sm text-xs font-semibold tracking-wide ' . ($tones[$tone] ?? $tones['brand'])])); ?>>
    <?php echo e($slot); ?>

</span>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/badge.blade.php ENDPATH**/ ?>