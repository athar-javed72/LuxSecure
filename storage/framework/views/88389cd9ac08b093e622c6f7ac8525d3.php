<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'eyebrow' => null,
    'title',
    'lead' => null,
    'subtitle' => null,
    'headingId' => null,
    'actionHref' => null,
    'actionLabel' => 'View all',
    'onDark' => false,
    'controls' => null,
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
    'actionLabel' => 'View all',
    'onDark' => false,
    'controls' => null,
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
    $actionClass = $onDark ? 'fs-view-all !text-[var(--color-gold)]' : 'fs-view-all';
?>

<div <?php echo e($attributes->class(['w-full'])); ?> data-reveal>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between sm:gap-6">
        <div class="min-w-0 text-left flex-1">
            <?php if($eyebrow): ?>
                <?php if (isset($component)) { $__componentOriginal2fd4850bfa2168af367e79900fb8c8ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2fd4850bfa2168af367e79900fb8c8ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.eyebrow','data' => ['onDark' => $onDark]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eyebrow'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['on-dark' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($onDark)]); ?><?php echo e($eyebrow); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2fd4850bfa2168af367e79900fb8c8ff)): ?>
<?php $attributes = $__attributesOriginal2fd4850bfa2168af367e79900fb8c8ff; ?>
<?php unset($__attributesOriginal2fd4850bfa2168af367e79900fb8c8ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2fd4850bfa2168af367e79900fb8c8ff)): ?>
<?php $component = $__componentOriginal2fd4850bfa2168af367e79900fb8c8ff; ?>
<?php unset($__componentOriginal2fd4850bfa2168af367e79900fb8c8ff); ?>
<?php endif; ?>
            <?php endif; ?>
            <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2 <?php echo e($eyebrow ? 'mt-4' : ''); ?>">
                <h2
                    <?php if($headingId): ?> id="<?php echo e($headingId); ?>" <?php endif; ?>
                    class="fs-display <?php echo e($titleClass); ?>"
                >
                    <?php echo e($title); ?>

                </h2>
                <div class="flex items-center gap-2 shrink-0">
                    <?php echo e($controls); ?>

                    <?php if($actionHref): ?>
                        <a
                            href="<?php echo e($actionHref); ?>"
                            class="<?php echo e($actionClass); ?> focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-forest)]"
                        >
                            <?php echo e($actionLabel); ?>

                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php if($leadText): ?>
                <p class="mt-3 fs-lead <?php echo e($leadClass); ?>"><?php echo e($leadText); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/section-heading.blade.php ENDPATH**/ ?>