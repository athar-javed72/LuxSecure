<?php $__env->startSection('title', $listing->title); ?>

<?php $__env->startSection('content'); ?>
<?php use App\Support\Media; ?>
<?php if (isset($component)) { $__componentOriginal360d002b1b676b6f84d43220f22129e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal360d002b1b676b6f84d43220f22129e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumbs','data' => ['items' => [
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Listings', 'href' => route('admin.listings.index')],
    ['label' => $listing->title],
]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumbs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Listings', 'href' => route('admin.listings.index')],
    ['label' => $listing->title],
])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal360d002b1b676b6f84d43220f22129e2)): ?>
<?php $attributes = $__attributesOriginal360d002b1b676b6f84d43220f22129e2; ?>
<?php unset($__attributesOriginal360d002b1b676b6f84d43220f22129e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal360d002b1b676b6f84d43220f22129e2)): ?>
<?php $component = $__componentOriginal360d002b1b676b6f84d43220f22129e2; ?>
<?php unset($__componentOriginal360d002b1b676b6f84d43220f22129e2); ?>
<?php endif; ?>

<div class="flex flex-wrap justify-between items-start gap-4 mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Listing details</h1>
    <div class="flex flex-wrap gap-2">
        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'secondary','href' => ''.e(route('admin.listings.edit', $listing)).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','href' => ''.e(route('admin.listings.edit', $listing)).'']); ?>Edit <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'ghost','href' => ''.e(route('admin.listings.index')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'ghost','href' => ''.e(route('admin.listings.index')).'']); ?>Back <?php echo $__env->renderComponent(); ?>
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

<div class="rounded-md bg-white shadow-sm border border-black/5 p-6 w-full max-w-5xl">
    <h2 class="text-lg font-bold text-fs-primary mb-4"><?php echo e($listing->title); ?></h2>
    <?php if(filled($listing->primary_image_url)): ?>
        <div class="aspect-[4/3] rounded-md overflow-hidden bg-fs-surface mb-4">
            <img src="<?php echo e($listing->primary_image_url); ?>" alt="<?php echo e($listing->title); ?>" loading="lazy" width="800" height="600" class="w-full h-full object-cover">
        </div>
    <?php endif; ?>
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div><dt class="text-xs text-fs-muted">Location</dt><dd class="mt-1 text-fs-primary"><?php echo e($listing->location); ?></dd></div>
        <div><dt class="text-xs text-fs-muted">Type</dt><dd class="mt-1 text-fs-primary"><?php echo e($listing->listingType?->name); ?></dd></div>
        <div><dt class="text-xs text-fs-muted">Purpose</dt><dd class="mt-1 text-fs-primary"><?php echo e(ucfirst(str_replace('_', ' ', $listing->purpose))); ?></dd></div>
        <div><dt class="text-xs text-fs-muted">Price</dt><dd class="mt-1 text-fs-primary"><?php echo e($listing->formatted_price); ?></dd></div>
        <div><dt class="text-xs text-fs-muted">Status</dt><dd class="mt-1"><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['tone' => $listing->status === 'published' ? 'success' : 'muted']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tone' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($listing->status === 'published' ? 'success' : 'muted')]); ?><?php echo e(ucfirst($listing->status)); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?></dd></div>
        <div><dt class="text-xs text-fs-muted">City</dt><dd class="mt-1 text-fs-primary"><?php echo e($listing->city?->name ?? '—'); ?></dd></div>
    </dl>
    <?php if(!empty($listing->attributeValues())): ?>
        <div class="mt-6">
            <h3 class="text-sm font-semibold text-fs-primary mb-2">Attributes</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <?php $__currentLoopData = $listing->attributeValues(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <dt class="text-xs text-fs-muted"><?php echo e(ucfirst(str_replace('_', ' ', $key))); ?></dt>
                        <dd class="mt-1 text-fs-primary"><?php echo e(is_bool($value) ? ($value ? 'Yes' : 'No') : $value); ?></dd>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </dl>
        </div>
    <?php endif; ?>
    <?php if($listing->description): ?>
        <p class="mt-6 text-sm text-fs-secondary"><?php echo e($listing->description); ?></p>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/admin/listings/show.blade.php ENDPATH**/ ?>