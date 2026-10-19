<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'eyebrow' => null,
    'title',
    'lead' => null,
    'subtitle' => null,
    'headingId' => null,
    'actionHref' => null,
    'actionLabel' => null,
    'onDark' => false,
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
    'eyebrow' => null,
    'title',
    'lead' => null,
    'subtitle' => null,
    'headingId' => null,
    'actionHref' => null,
    'actionLabel' => null,
    'onDark' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $leadText = $lead ?? $subtitle;
    $titleClass = $onDark ? 'text-[var(--color-ivory)]' : 'text-[var(--color-ink)]';
    $leadClass = $onDark ? 'text-[var(--color-sage)]' : 'text-[var(--color-muted)]';
    $actionClass = $onDark
        ? 'text-[var(--color-gold)] hover:underline'
        : 'text-[var(--color-gold-dark)] hover:underline';
?>

<div <?php echo e($attributes->class(['w-full'])); ?>>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between sm:gap-6">
        <div class="min-w-0 text-left">
            <?php if($eyebrow): ?>
                <p class="<?php echo e($onDark ? 'fs-eyebrow-on-dark' : 'fs-eyebrow'); ?>"><?php echo e($eyebrow); ?></p>
            <?php endif; ?>
            <h2
                <?php if($headingId): ?> id="<?php echo e($headingId); ?>" <?php endif; ?>
                class="fs-display <?php echo e($titleClass); ?> <?php echo e($eyebrow ? 'mt-4' : ''); ?>"
            >
                <?php echo e($title); ?>

            </h2>
            <?php if($leadText): ?>
                <p class="mt-3 fs-lead <?php echo e($leadClass); ?>"><?php echo e($leadText); ?></p>
            <?php endif; ?>
        </div>
        <?php if($actionHref && $actionLabel): ?>
            <a
                href="<?php echo e($actionHref); ?>"
                class="shrink-0 inline-flex items-center min-h-11 text-sm font-semibold tracking-wide <?php echo e($actionClass); ?> focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-forest)]"
            >
                <?php echo e($actionLabel); ?>

            </a>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/section-heading.blade.php ENDPATH**/ ?>