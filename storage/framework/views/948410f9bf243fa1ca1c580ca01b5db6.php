<?php $__env->startSection('title', 'Properties'); ?>
<?php $__env->startSection('meta_description', 'Browse and filter '.($siteName ?? config('app.name')).' property listings by location, type, and budget.'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => ['class' => 'py-8','xData' => 'filtersPanel']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'py-8','x-data' => 'filtersPanel']); ?>
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
        <div>
            <?php if (isset($component)) { $__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.brand-logo','data' => ['layout' => 'stacked','surface' => 'light','division' => 'properties','class' => 'mb-3 [&_img]:h-16 [&_img]:min-w-[140px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('brand-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['layout' => 'stacked','surface' => 'light','division' => 'properties','class' => 'mb-3 [&_img]:h-16 [&_img]:min-w-[140px]']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3)): ?>
<?php $attributes = $__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3; ?>
<?php unset($__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3)): ?>
<?php $component = $__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3; ?>
<?php unset($__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3); ?>
<?php endif; ?>
            <h1 class="sr-only">Properties</h1>
            <p class="mt-1 fs-display text-base italic text-[#876A2F]"><?php echo e(\App\Support\BrandAssets::TAGLINE); ?></p>
            <p class="mt-2 text-sm text-fs-secondary"><?php echo e($properties->total()); ?> listings found</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button
                type="button"
                class="lg:hidden inline-flex items-center justify-center h-11 px-5 rounded-sm border border-slate-200 text-sm font-semibold text-fs-primary"
                @click="openFilters()"
            >
                Filters
            </button>
            <form method="GET" class="flex items-center gap-2">
                <?php $__currentLoopData = request()->except('sort', 'page'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <label for="sort" class="sr-only">Sort</label>
                <select id="sort" name="sort" @change="submitSort($el)" class="h-11 px-4 rounded-sm border border-slate-200 text-sm text-fs-primary bg-white">
                    <option value="">Newest</option>
                    <option value="price_asc" <?php if(request('sort') === 'price_asc'): echo 'selected'; endif; ?>>Price: Low to High</option>
                    <option value="price_desc" <?php if(request('sort') === 'price_desc'): echo 'selected'; endif; ?>>Price: High to Low</option>
                </select>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <aside class="hidden lg:block lg:col-span-3">
            <div class="rounded-md bg-white shadow-sm border border-slate-100 p-5 sticky top-24">
                <h2 class="text-base font-semibold text-fs-primary mb-4">Filters</h2>
                <?php echo $__env->make('include.property-filters', ['idPrefix' => 'desktop'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </aside>

        <div class="lg:col-span-9">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                <?php $__empty_1 = true; $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full rounded-md border border-slate-200 bg-slate-50 p-8 text-center">
                        <h2 class="text-base font-semibold text-fs-primary">No properties found</h2>
                        <p class="mt-2 text-sm text-fs-secondary">Try adjusting your filters.</p>
                        <div class="mt-6">
                            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','href' => ''.e(route('properties')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','href' => ''.e(route('properties')).'']); ?>Clear filters <?php echo $__env->renderComponent(); ?>
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
            </div>

            <?php if($properties->hasPages()): ?>
                <div class="mt-8">
                    <?php echo e($properties->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div x-cloak x-show="filtersOpen" class="lg:hidden fixed inset-0 z-[70]" role="dialog" aria-modal="true" aria-label="Filters">
        <div class="absolute inset-0 bg-fs-primary/40" @click="closeFilters()"></div>
        <div
            class="absolute inset-x-0 bottom-0 max-h-[85vh] overflow-y-auto rounded-t-md bg-white shadow-lg p-5"
            x-show="filtersOpen"
            x-transition:enter="transition transform duration-200"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition transform duration-150"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
        >
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-fs-primary">Filters</h2>
                <button type="button" class="w-11 h-11 rounded-sm border border-slate-200" @click="closeFilters()" aria-label="Close filters">✕</button>
            </div>
            <?php echo $__env->make('include.property-filters', ['idPrefix' => 'mobile'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('include.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/properties.blade.php ENDPATH**/ ?>