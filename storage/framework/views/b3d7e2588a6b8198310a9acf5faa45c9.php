<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title',
    'subtitle' => null,
    'lead' => null,
    'eyebrow' => null,
    'breadcrumbs' => [],
    'band' => false, // true = inner-page ivory band under the header
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
    'lead' => null,
    'eyebrow' => null,
    'breadcrumbs' => [],
    'band' => false, // true = inner-page ivory band under the header
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
    $useBand = $band || $eyebrow || ! empty($breadcrumbs);
?>

<?php if($useBand): ?>
    <header <?php echo e($attributes->class(['w-full bg-[var(--color-ivory-50)] border-b border-[var(--color-border)]'])); ?>>
        <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => ['class' => 'py-[clamp(32px,5vw,56px)]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'py-[clamp(32px,5vw,56px)]']); ?>
            <?php if(! empty($breadcrumbs)): ?>
                <nav aria-label="Breadcrumb" class="mb-4">
                    <ol class="flex flex-wrap items-center gap-1.5 text-[13px] text-[var(--color-muted)]">
                        <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $crumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="inline-flex items-center gap-1.5">
                                <?php if($i > 0): ?>
                                    <span aria-hidden="true" class="text-[var(--color-border)]">/</span>
                                <?php endif; ?>
                                <?php if(! empty($crumb['href']) && ! $loop->last): ?>
                                    <a href="<?php echo e($crumb['href']); ?>" class="hover:text-[var(--color-forest)] hover:underline underline-offset-2"><?php echo e($crumb['label']); ?></a>
                                <?php else: ?>
                                    <span <?php if($loop->last): ?> aria-current="page" <?php endif; ?> class="<?php echo e($loop->last ? 'text-[var(--color-ink)] font-medium' : ''); ?>"><?php echo e($crumb['label']); ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ol>
                </nav>
            <?php endif; ?>

            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                <div class="min-w-0 max-w-[56ch]">
                    <?php if($eyebrow): ?>
                        <?php if (isset($component)) { $__componentOriginal2fd4850bfa2168af367e79900fb8c8ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2fd4850bfa2168af367e79900fb8c8ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.eyebrow','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eyebrow'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?><?php echo e($eyebrow); ?> <?php echo $__env->renderComponent(); ?>
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
                    <h1 class="mt-3 text-[clamp(36px,5vw,52px)] font-[family-name:var(--font-display)] font-semibold tracking-[-0.01em] text-[var(--color-ink)]" style="text-wrap: balance;">
                        <?php echo e($title); ?>

                    </h1>
                    <?php if($leadText): ?>
                        <p class="mt-3 text-[15px] leading-relaxed text-[var(--color-muted)] max-w-[56ch]" style="text-wrap: pretty;"><?php echo e($leadText); ?></p>
                    <?php endif; ?>
                </div>
                <?php if(isset($actions)): ?>
                    <div class="shrink-0 flex flex-wrap items-center gap-3 lg:justify-end">
                        <?php echo e($actions); ?>

                    </div>
                <?php endif; ?>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala766c2d312d6f7864fe218e2500d2bba)): ?>
<?php $attributes = $__attributesOriginala766c2d312d6f7864fe218e2500d2bba; ?>
<?php unset($__attributesOriginala766c2d312d6f7864fe218e2500d2bba); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala766c2d312d6f7864fe218e2500d2bba)): ?>
<?php $component = $__componentOriginala766c2d312d6f7864fe218e2500d2bba; ?>
<?php unset($__componentOriginala766c2d312d6f7864fe218e2500d2bba); ?>
<?php endif; ?>
    </header>
<?php else: ?>
    
    <div <?php echo e($attributes->merge(['class' => 'mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between'])); ?>>
        <div class="min-w-0">
            <h1 class="fs-display text-2xl md:text-3xl text-fs-primary"><?php echo e($title); ?></h1>
            <?php if($leadText): ?>
                <p class="mt-2 text-sm text-fs-secondary max-w-prose"><?php echo e($leadText); ?></p>
            <?php endif; ?>
        </div>
        <?php if(isset($actions)): ?>
            <div class="flex flex-wrap gap-2 shrink-0"><?php echo e($actions); ?></div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/page-header.blade.php ENDPATH**/ ?>