<?php $__env->startSection('header_mode', 'light'); ?>
<?php $__env->startSection('title', 'Properties'); ?>
<?php $__env->startSection('meta_description', 'Browse and filter '.($siteName ?? config('app.name')).' property listings by location, type, and budget.'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $total = $properties->total();
    $from = $properties->firstItem() ?? 0;
    $to = $properties->lastItem() ?? 0;
?>

<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['eyebrow' => ''.e($divisionProperties ?? 'Properties').'','title' => 'Properties','lead' => 'Homes, apartments, plots and stays, curated with care.','breadcrumbs' => [
        ['label' => 'Home', 'href' => route('home')],
        ['label' => 'Properties'],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['eyebrow' => ''.e($divisionProperties ?? 'Properties').'','title' => 'Properties','lead' => 'Homes, apartments, plots and stays, curated with care.','breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['label' => 'Home', 'href' => route('home')],
        ['label' => 'Properties'],
    ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>

<?php if (isset($component)) { $__componentOriginale7499b9c21946d607fb6b6fe77adcd57 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale7499b9c21946d607fb6b6fe77adcd57 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.properties-filters','data' => ['listingTypes' => $listingTypes,'cities' => $cities,'countries' => $filterCountries ?? collect(),'total' => $total]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('properties-filters'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['listing-types' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($listingTypes),'cities' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($cities),'countries' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filterCountries ?? collect()),'total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($total)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale7499b9c21946d607fb6b6fe77adcd57)): ?>
<?php $attributes = $__attributesOriginale7499b9c21946d607fb6b6fe77adcd57; ?>
<?php unset($__attributesOriginale7499b9c21946d607fb6b6fe77adcd57); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale7499b9c21946d607fb6b6fe77adcd57)): ?>
<?php $component = $__componentOriginale7499b9c21946d607fb6b6fe77adcd57; ?>
<?php unset($__componentOriginale7499b9c21946d607fb6b6fe77adcd57); ?>
<?php endif; ?>

<section class="pb-[var(--section-y)]" aria-labelledby="results-heading">
    <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => ['class' => 'pt-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'pt-6']); ?>
        <div class="flex flex-wrap items-end justify-between gap-3 mb-6">
            <div>
                <h2 id="results-heading" class="sr-only">Search results</h2>
                <p class="text-[14px] sm:text-[15px] text-[var(--color-muted)] fs-numeral" aria-live="polite">
                    <?php if($total === 0): ?>
                        0 listings
                    <?php else: ?>
                        <?php echo e(number_format($total)); ?> <?php echo e(\Illuminate\Support\Str::plural('listing', $total)); ?>

                        <?php if($properties->hasPages()): ?>
                            <span class="text-[var(--color-muted)]"> · Showing <?php echo e($from); ?>–<?php echo e($to); ?> of <?php echo e(number_format($total)); ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <?php if($properties->count() > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" data-reveal data-reveal-stagger>
                <?php $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if (isset($component)) { $__componentOriginal58439408c0a13a2b2db6109a4cd58e23 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal58439408c0a13a2b2db6109a4cd58e23 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.property-card','data' => ['property' => $property]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('property-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['property' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($property)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal58439408c0a13a2b2db6109a4cd58e23)): ?>
<?php $attributes = $__attributesOriginal58439408c0a13a2b2db6109a4cd58e23; ?>
<?php unset($__attributesOriginal58439408c0a13a2b2db6109a4cd58e23); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal58439408c0a13a2b2db6109a4cd58e23)): ?>
<?php $component = $__componentOriginal58439408c0a13a2b2db6109a4cd58e23; ?>
<?php unset($__componentOriginal58439408c0a13a2b2db6109a4cd58e23); ?>
<?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <?php if($properties->hasPages()): ?>
                <div class="mt-10 fs-pagination flex flex-col items-center gap-3">
                    <p class="text-sm text-[var(--color-muted)] fs-numeral">Showing <?php echo e($from); ?>–<?php echo e($to); ?> of <?php echo e(number_format($total)); ?></p>
                    <?php echo e($properties->onEachSide(1)->links()); ?>

                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="relative rounded-[var(--radius-card)] border border-[var(--color-border)] bg-white px-6 py-16 text-center" data-reveal>
                <img
                    src="<?php echo e(\App\Support\BrandAssets::logo('monogram-nostar', 'light')); ?>"
                    alt=""
                    aria-hidden="true"
                    class="mx-auto w-24 h-24 object-contain opacity-10 mb-6"
                >
                <h2 class="text-[clamp(24px,3vw,32px)] font-[family-name:var(--font-display)] font-semibold text-[var(--color-ink)]">No listings match these filters</h2>
                <p class="mt-3 text-[15px] text-[var(--color-muted)] max-w-[42ch] mx-auto" style="text-wrap: pretty;">
                    Try widening your budget or removing a filter.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','href' => ''.e(route('properties')).'','surface' => 'light']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','href' => ''.e(route('properties')).'','surface' => 'light']); ?>Clear all filters <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'secondary','href' => ''.e(route('contact', ['type' => 'property'])).'','surface' => 'light']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','href' => ''.e(route('contact', ['type' => 'property'])).'','surface' => 'light']); ?>Tell us what you are looking for <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
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
</section>

<?php if (isset($component)) { $__componentOriginalb9eda212fea7921b4a03feb1b01f47d7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb9eda212fea7921b4a03feb1b01f47d7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cta-band','data' => ['title' => 'Can\'t find the right fit?','lead' => 'Tell us what you are looking for and our team will help.','primaryLabel' => 'Start a Conversation','primaryHref' => route('contact', ['type' => 'property']),'secondaryLabel' => 'Browse hotels','secondaryHref' => route('divisions.hospitality'),'class' => 'pb-24']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cta-band'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Can\'t find the right fit?','lead' => 'Tell us what you are looking for and our team will help.','primary-label' => 'Start a Conversation','primary-href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('contact', ['type' => 'property'])),'secondary-label' => 'Browse hotels','secondary-href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('divisions.hospitality')),'class' => 'pb-24']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb9eda212fea7921b4a03feb1b01f47d7)): ?>
<?php $attributes = $__attributesOriginalb9eda212fea7921b4a03feb1b01f47d7; ?>
<?php unset($__attributesOriginalb9eda212fea7921b4a03feb1b01f47d7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb9eda212fea7921b4a03feb1b01f47d7)): ?>
<?php $component = $__componentOriginalb9eda212fea7921b4a03feb1b01f47d7; ?>
<?php unset($__componentOriginalb9eda212fea7921b4a03feb1b01f47d7); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('include.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/properties.blade.php ENDPATH**/ ?>