<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'items' => [],
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
    'items' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if(count($items) > 0): ?>
    <nav <?php echo e($attributes->merge(['class' => 'mb-4', 'aria-label' => 'Breadcrumb'])); ?>>
        <ol class="flex flex-wrap items-center gap-1 text-xs text-fs-muted">
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $label = $item['label'] ?? '';
                    $href = $item['href'] ?? null;
                    $isLast = $index === array_key_last($items);
                ?>
                <li class="inline-flex items-center gap-1 min-w-0">
                    <?php if(!$isLast && $href): ?>
                        <a href="<?php echo e($href); ?>" class="truncate text-fs-secondary hover:text-fs-brand transition-colors duration-150"><?php echo e($label); ?></a>
                        <span class="text-fs-muted" aria-hidden="true">/</span>
                    <?php else: ?>
                        <span class="truncate font-medium text-fs-primary" <?php if($isLast): ?> aria-current="page" <?php endif; ?>><?php echo e($label); ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ol>
    </nav>
<?php endif; ?>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/breadcrumbs.blade.php ENDPATH**/ ?>