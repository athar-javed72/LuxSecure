<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'tone' => 'info',
    'dismissible' => false,
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
    'tone' => 'info',
    'dismissible' => false,
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
        'success' => 'bg-fs-brand-light text-fs-primary border-fs-brand/30',
        'error' => 'bg-fs-danger/10 text-fs-danger border-fs-danger/20',
        'info' => 'bg-white text-fs-primary border-black/10',
        'warning' => 'bg-fs-brand-light/80 text-fs-primary border-fs-brand/40',
    ];
    $classes = 'relative flex gap-3 items-start p-4 rounded-sm border text-sm '.($tones[$tone] ?? $tones['info']);
?>

<div
    <?php echo e($attributes->merge(['class' => $classes, 'role' => $tone === 'error' ? 'alert' : 'status'])); ?>

    <?php if($dismissible): ?> x-data="dismissible" x-show="show" x-cloak <?php endif; ?>
>
    <div class="flex-1 min-w-0"><?php echo e($slot); ?></div>
    <?php if($dismissible): ?>
        <button type="button" class="shrink-0 text-current/70 hover:text-current" @click="dismiss()" aria-label="Dismiss">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    <?php endif; ?>
</div>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/alert.blade.php ENDPATH**/ ?>