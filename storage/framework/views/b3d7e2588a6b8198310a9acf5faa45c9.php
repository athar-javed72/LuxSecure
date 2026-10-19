<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title',
    'subtitle' => null,
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
    'title',
    'subtitle' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div <?php echo e($attributes->merge(['class' => 'mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between'])); ?>>
    <div class="min-w-0">
        <h1 class="fs-display text-2xl md:text-3xl text-fs-primary"><?php echo e($title); ?></h1>
        <?php if($subtitle): ?>
            <p class="mt-2 text-sm text-fs-secondary max-w-prose"><?php echo e($subtitle); ?></p>
        <?php endif; ?>
    </div>
    <?php if(isset($actions)): ?>
        <div class="flex flex-wrap gap-2 shrink-0"><?php echo e($actions); ?></div>
    <?php endif; ?>
</div>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/page-header.blade.php ENDPATH**/ ?>