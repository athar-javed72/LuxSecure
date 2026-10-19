<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'tag' => null,
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
    'type' => 'button',
    'href' => null,
    'tag' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $base = 'inline-flex items-center justify-center gap-2 h-11 px-5 rounded-sm text-sm font-semibold tracking-wide transition-colors duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-fs-brand disabled:opacity-50 disabled:pointer-events-none';

    $variants = [
        'primary' => 'bg-fs-brand text-white hover:bg-fs-brand-dark',
        'secondary' => 'bg-fs-brand-light text-fs-primary hover:bg-[#e5d8c3]',
        'ghost' => 'bg-transparent text-fs-primary hover:bg-black/5 border border-black/10',
        'ink' => 'bg-fs-primary text-white hover:bg-[#152238]',
        'danger' => 'bg-fs-danger text-white hover:bg-[#991b1b]',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
    $element = $tag ?? ($href ? 'a' : 'button');
?>

<?php if($element === 'a'): ?>
    <a href="<?php echo e($href); ?>" <?php echo e($attributes->merge(['class' => $classes])); ?>>
        <?php echo e($slot); ?>

    </a>
<?php else: ?>
    <button type="<?php echo e($type); ?>" <?php echo e($attributes->merge(['class' => $classes])); ?>>
        <?php echo e($slot); ?>

    </button>
<?php endif; ?>
<?php /**PATH D:\Projects\Laravel Projects\LuxSecure-main\resources\views/components/button.blade.php ENDPATH**/ ?>