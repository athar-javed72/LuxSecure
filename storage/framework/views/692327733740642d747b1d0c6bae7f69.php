<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'property' => null,
    'title' => null,
    'location' => null,
    'price' => null,
    'image' => null,
    'url' => null,
    'type' => null,
    'bedrooms' => null,
    'bathrooms' => null,
    'area' => null,
    'listing' => null,
    'isSample' => false,
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
    'property' => null,
    'title' => null,
    'location' => null,
    'price' => null,
    'image' => null,
    'url' => null,
    'type' => null,
    'bedrooms' => null,
    'bathrooms' => null,
    'area' => null,
    'listing' => null,
    'isSample' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    if ($property) {
        $title = $property->title;
        $location = $property->location;
        $price = $property->formatted_price;
        if ($property->purpose === 'short_stay' && $property->price_unit === 'per_night') {
            $price = 'from '.$property->formatted_price;
        }
        $image = $property->primary_image_url;
        $url = route('properties.show', $property);
        $type = $property->type_name;
        $bedrooms = $property->bedrooms;
        $bathrooms = $property->bathrooms;
        $area = $area ?? ($property->area_size
            ? rtrim(rtrim(number_format((float) $property->area_size, 2), '0'), '.').' '.str_replace('_', ' ', (string) $property->area_unit)
            : null);
        $listing = $listing ?? $property->purpose;
        $isFav = auth()->check() && auth()->user()->relationLoaded('favorites')
            ? auth()->user()->favorites->contains($property)
            : (auth()->check() ? auth()->user()->favorites()->where('listing_id', $property->id)->exists() : false);
        $isSample = $isSample || (app()->environment(['local', 'testing']) && str_contains((string) $property->description, 'sample listing'));
    } else {
        $listing = $listing ?? 'sale';
        $isFav = false;
    }

    $hasRealImage = filled($image);
    $badgeTone = match ($listing) {
        'rent' => 'rent',
        'short_stay' => 'stay',
        default => 'sale',
    };
    $badgeLabel = match ($listing) {
        'rent' => 'For Rent',
        'short_stay' => 'Short Stay',
        default => 'For Sale',
    };

    $meta = [];
    if ($bedrooms !== null && $bedrooms !== '' && $bedrooms !== '—') {
        $meta[] = ['label' => $bedrooms.' Beds', 'icon' => 'beds'];
    }
    if ($bathrooms !== null && $bathrooms !== '' && $bathrooms !== '—') {
        $meta[] = ['label' => $bathrooms.' Baths', 'icon' => 'baths'];
    }
    if ($area !== null && $area !== '' && $area !== '—') {
        $meta[] = ['label' => $area, 'icon' => 'area'];
    }

    $monogram = \App\Support\BrandAssets::logo('monogram-nostar', 'light');
?>

<article <?php echo e($attributes->merge(['class' => 'group relative fs-card overflow-hidden h-full flex flex-col'])); ?>>
    <a href="<?php echo e($url); ?>" class="absolute inset-0 z-10 rounded-[var(--radius-card)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-forest)]" aria-label="View <?php echo e($title); ?>">
        <span class="sr-only"><?php echo e($title); ?></span>
    </a>

    <div class="relative aspect-[4/3] overflow-hidden rounded-t-[var(--radius-card)] fs-photo-placeholder">
        <?php if($hasRealImage): ?>
            <img
                src="<?php echo e($image); ?>"
                alt="<?php echo e($title); ?>"
                loading="lazy"
                decoding="async"
                width="640"
                height="480"
                data-img-fallback
                class="w-full h-full object-cover"
            >
            <div data-img-placeholder class="hidden absolute inset-0 flex flex-col items-center justify-center gap-2 fs-photo-placeholder px-4 text-center">
                <img src="<?php echo e($monogram); ?>" alt="" aria-hidden="true" class="h-12 w-12 opacity-[0.14]" width="48" height="48">
                <span class="text-sm text-[var(--color-muted)]">Photo coming soon</span>
            </div>
        <?php else: ?>
            <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 px-4 text-center" aria-hidden="true">
                <img src="<?php echo e($monogram); ?>" alt="" class="h-12 w-12 opacity-[0.14]" width="48" height="48">
                <span class="text-sm text-[var(--color-muted)]">Photo coming soon</span>
            </div>
            <span class="sr-only"><?php echo e($title); ?> — photo coming soon</span>
        <?php endif; ?>

        <div class="absolute top-3 left-3 z-20 flex flex-wrap gap-2">
            <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['tone' => $badgeTone]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tone' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($badgeTone)]); ?><?php echo e($badgeLabel); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
            <?php if($isSample && ! app()->isProduction()): ?>
                <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['tone' => 'sample']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tone' => 'sample']); ?>Sample <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
            <?php endif; ?>
        </div>

        <?php if(auth()->guard()->check()): ?>
            <?php if($property): ?>
                <form action="<?php echo e(route('favorites.toggle', $property)); ?>" method="POST" class="absolute top-3 right-3 z-20">
                    <?php echo csrf_field(); ?>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-[var(--radius-button)] bg-white/95 text-[var(--color-muted)] shadow-[var(--shadow-card)] hover:text-fs-danger transition-colors duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-forest)]"
                        aria-label="<?php echo e($isFav ? 'Remove from wishlist' : 'Add to wishlist'); ?>"
                        aria-pressed="<?php echo e($isFav ? 'true' : 'false'); ?>"
                    >
                        <svg class="w-5 h-5 <?php echo e($isFav ? 'fill-fs-danger text-fs-danger' : 'fill-none'); ?>" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z"/>
                        </svg>
                    </button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="p-5 flex flex-col flex-1">
        <p class="font-[family-name:var(--font-display)] text-[26px] font-semibold leading-tight text-[var(--color-ink)]"><?php echo e($price); ?></p>
        <h3 class="mt-1 text-base font-semibold text-[var(--color-ink)] fs-line-clamp-1"><?php echo e($title); ?></h3>
        <?php if($location): ?>
            <p class="mt-2 text-sm text-[var(--color-muted)] flex items-start gap-1.5">
                <svg class="w-4 h-4 mt-0.5 shrink-0 text-[var(--color-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span><?php echo e($location); ?></span>
            </p>
        <?php endif; ?>

        <?php if(count($meta) > 0): ?>
            <div class="mt-4 pt-4 border-t border-[var(--color-border)] flex flex-wrap gap-x-4 gap-y-2 text-sm text-[var(--color-muted)]">
                <?php $__currentLoopData = $meta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center gap-1.5 min-h-6">
                        <?php if($item['icon'] === 'beds'): ?>
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7v11a1 1 0 001 1h16a1 1 0 001-1V7M3 7l9-4 9 4M8 21V11h8v10"/></svg>
                        <?php elseif($item['icon'] === 'baths'): ?>
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 12h16M6 12V8a2 2 0 012-2h8a2 2 0 012 2v4M7 16h.01M17 16h.01M5 21h14"/></svg>
                        <?php else: ?>
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4"/></svg>
                        <?php endif; ?>
                        <span><?php echo e($item['label']); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</article>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/property-card.blade.php ENDPATH**/ ?>