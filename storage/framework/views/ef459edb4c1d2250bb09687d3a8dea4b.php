<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'reverse' => false,
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
    'reverse' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div <?php echo e($attributes->class([
    'grid lg:grid-cols-2 gap-10 lg:gap-16 items-start',
])); ?>>
    <div class="<?php echo e($reverse ? 'order-2 lg:order-2' : 'order-2 lg:order-1'); ?>">
        <?php echo e($media ?? ''); ?>

    </div>
    <div class="<?php echo e($reverse ? 'order-1 lg:order-1' : 'order-1 lg:order-2'); ?>">
        <?php echo e($slot); ?>

    </div>
</div>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/split-section.blade.php ENDPATH**/ ?>