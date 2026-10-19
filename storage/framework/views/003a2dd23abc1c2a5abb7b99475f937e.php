<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'role' => 'Real Estate Agent',
    'image',
    'phone' => null,
    'listings' => null,
    'href' => '#agents',
    'verified' => false,
    'rating' => null,
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
    'name',
    'role' => 'Real Estate Agent',
    'image',
    'phone' => null,
    'listings' => null,
    'href' => '#agents',
    'verified' => false,
    'rating' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<article <?php echo e($attributes->merge(['class' => 'bg-white rounded-md shadow-sm hover:shadow-lg transition-shadow duration-150 overflow-hidden h-full'])); ?>>
    <a href="<?php echo e($href); ?>" class="block focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-fs-brand">
        <div class="aspect-[4/3] bg-slate-100 overflow-hidden">
            <img
                src="<?php echo e($image); ?>"
                alt="<?php echo e($name); ?>"
                loading="lazy"
                width="480"
                height="360"
                class="w-full h-full object-cover"
            >
        </div>
        <div class="p-4">
            <h3 class="text-base font-semibold text-fs-primary flex items-center gap-2">
                <?php echo e($name); ?>

                <?php if($verified): ?>
                    <span class="text-[10px] uppercase tracking-wide text-fs-brand">Verified</span>
                <?php endif; ?>
            </h3>
            <p class="mt-1 text-sm text-fs-secondary"><?php echo e($role); ?></p>
            <?php if($rating !== null): ?>
                <p class="mt-1 text-xs text-fs-muted"><?php echo e(number_format((float) $rating, 1)); ?>/5 avg</p>
            <?php endif; ?>
            <?php if($listings !== null): ?>
                <p class="mt-2 text-xs text-fs-muted"><?php echo e($listings); ?> listings</p>
            <?php endif; ?>
            <?php if($phone): ?>
                <p class="mt-2 text-sm text-fs-brand font-medium"><?php echo e($phone); ?></p>
            <?php endif; ?>
        </div>
    </a>
</article>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/agent-card.blade.php ENDPATH**/ ?>