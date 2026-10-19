<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title',
    'excerpt',
    'image',
    'date',
    'category' => 'Insights',
    'href' => '#blog',
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
    'excerpt',
    'image',
    'date',
    'category' => 'Insights',
    'href' => '#blog',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<article <?php echo e($attributes->merge(['class' => 'bg-white rounded-md shadow-sm hover:shadow-lg transition-shadow duration-150 overflow-hidden h-full flex flex-col'])); ?>>
    <a href="<?php echo e($href); ?>" class="flex flex-col h-full focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-fs-brand">
        <div class="aspect-[4/3] bg-slate-100 overflow-hidden">
            <img
                src="<?php echo e($image); ?>"
                alt="<?php echo e($title); ?>"
                loading="lazy"
                width="640"
                height="480"
                class="w-full h-full object-cover"
            >
        </div>
        <div class="p-4 flex flex-col flex-1">
            <div class="flex items-center gap-2 mb-2">
                <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['tone' => 'muted']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tone' => 'muted']); ?><?php echo e($category); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
                <time class="text-xs text-fs-muted" datetime="<?php echo e($date); ?>"><?php echo e(\Illuminate\Support\Carbon::parse($date)->format('M j, Y')); ?></time>
            </div>
            <h3 class="text-base font-semibold text-fs-primary fs-line-clamp-2"><?php echo e($title); ?></h3>
            <p class="mt-2 text-sm text-fs-secondary fs-line-clamp-2 flex-1"><?php echo e($excerpt); ?></p>
        </div>
    </a>
</article>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/blog-card.blade.php ENDPATH**/ ?>