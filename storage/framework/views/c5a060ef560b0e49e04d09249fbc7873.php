<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label',
    'value',
    'href' => null,
    'hint' => null,
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
    'label',
    'value',
    'href' => null,
    'hint' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $classes = 'block rounded-md bg-white shadow-sm border border-black/5 p-6 transition-shadow duration-150';
    if ($href) {
        $classes .= ' hover:shadow-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-fs-brand';
    }
?>

<?php if($href): ?>
    <a href="<?php echo e($href); ?>" <?php echo e($attributes->merge(['class' => $classes])); ?>>
        <p class="text-xs font-semibold uppercase tracking-wide text-fs-muted"><?php echo e($label); ?></p>
        <p class="fs-display mt-2 text-2xl font-semibold text-fs-primary"><?php echo e($value); ?></p>
        <?php if($hint): ?><p class="mt-1 text-xs text-fs-secondary"><?php echo e($hint); ?></p><?php endif; ?>
        <?php echo e($slot); ?>

    </a>
<?php else: ?>
    <div <?php echo e($attributes->merge(['class' => $classes])); ?>>
        <p class="text-xs font-semibold uppercase tracking-wide text-fs-muted"><?php echo e($label); ?></p>
        <p class="fs-display mt-2 text-2xl font-semibold text-fs-primary"><?php echo e($value); ?></p>
        <?php if($hint): ?><p class="mt-1 text-xs text-fs-secondary"><?php echo e($hint); ?></p><?php endif; ?>
        <?php echo e($slot); ?>

    </div>
<?php endif; ?>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/stat-card.blade.php ENDPATH**/ ?>