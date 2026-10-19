<?php $__env->startSection('header_mode', 'overlay'); ?>
<?php $__env->startSection('title', 'Elevating Spaces. Enriching Stays.'); ?>
<?php $__env->startSection('meta_description'); ?>
<?php echo e(($siteName ?? config('app.name')).' — Hotels, residences and thoughtfully selected properties, presented with care.'); ?>

<?php $__env->stopSection(); ?>

<?php
    $img = config('site-images');
    $slides = $img['hero_slides'] ?? [];
    $featuredSale = $houses->where('purpose', '!=', 'short_stay')->take(8);
    $featuredStays = $houses->where('purpose', 'short_stay')->take(8);
    if (app()->isProduction()) {
        $featuredStays = $featuredStays->reject(fn ($l) => str_contains((string) $l->description, 'sample') || str_contains((string) $l->description, 'Seeded'));
    }
    $typeIcons = config('search.type_icons');
    $searchTypes = \App\Models\ListingType::query()->where('is_active', true)->orderBy('name')->get(['name', 'slug'])->map(fn ($t) => ['name' => $t->name, 'slug' => $t->slug])->all();
    $currency = session('currency', optional($activeCountry ?? null)->currency_code ?? 'PKR');
?>

<?php $__env->startPush('styles'); ?>
<link rel="preload" as="image" href="<?php echo e($slides[0]['desktop'] ?? $img['hero']['desktop']); ?>" fetchpriority="high">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('home_content'); ?>


<section
    class="relative overflow-visible w-full min-h-[600px] h-[min(92svh,820px)] flex flex-col justify-end -mt-[var(--header-h)] pt-[var(--header-h)] z-0"
    data-hero-slideshow
    aria-roledescription="carousel"
    aria-label="Featured spaces"
    tabindex="0"
>
    <div class="fs-hero-media" aria-hidden="true">
        <?php $__currentLoopData = $slides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div
                class="fs-hero-slide <?php echo e($i === 0 ? 'is-active' : ''); ?>"
                role="group"
                aria-label="<?php echo e($i + 1); ?> of <?php echo e(count($slides)); ?>"
                <?php if($i > 0): ?> aria-hidden="true" <?php endif; ?>
            >
                <picture>
                    <source
                        media="(max-width: 767px)"
                        <?php if($i === 0): ?> srcset="<?php echo e($slide['mobile']); ?>" <?php else: ?> data-srcset="<?php echo e($slide['mobile']); ?>" <?php endif; ?>
                    >
                    <img
                        <?php if($i === 0): ?>
                            src="<?php echo e($slide['desktop']); ?>"
                            fetchpriority="high"
                        <?php else: ?>
                            data-src="<?php echo e($slide['desktop']); ?>"
                            src=""
                            loading="lazy"
                        <?php endif; ?>
                        alt="<?php echo e($slide['alt']); ?>"
                        width="2400"
                        height="1350"
                        decoding="async"
                        class="absolute inset-0 w-full h-full object-cover"
                        style="object-position: <?php echo e($slide['object_position']); ?>; transform-origin: <?php echo e($slide['origin'] ?? 'center'); ?>;"
                    >
                </picture>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div class="absolute inset-0 fs-hero-overlay z-[2]"></div>
    </div>

    <div class="fs-hero-load relative z-10 w-full flex-1 flex flex-col justify-center pb-28 md:pb-36">
        <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => ['class' => 'w-full pt-8']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full pt-8']); ?>
            <div class="max-w-[640px]">
                <div data-hero-step="1">
                    <?php if (isset($component)) { $__componentOriginal2fd4850bfa2168af367e79900fb8c8ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2fd4850bfa2168af367e79900fb8c8ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.eyebrow','data' => ['onDark' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eyebrow'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['on-dark' => true]); ?>Hospitality • Property • Living <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2fd4850bfa2168af367e79900fb8c8ff)): ?>
<?php $attributes = $__attributesOriginal2fd4850bfa2168af367e79900fb8c8ff; ?>
<?php unset($__attributesOriginal2fd4850bfa2168af367e79900fb8c8ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2fd4850bfa2168af367e79900fb8c8ff)): ?>
<?php $component = $__componentOriginal2fd4850bfa2168af367e79900fb8c8ff; ?>
<?php unset($__componentOriginal2fd4850bfa2168af367e79900fb8c8ff); ?>
<?php endif; ?>
                </div>
                <h1 class="mt-4 text-[var(--color-ivory)]" data-hero-step="2">
                    Elevating Spaces.<br>Enriching Stays.
                </h1>
                <p class="mt-4 text-base md:text-[17px] text-[var(--color-sage)] fs-lead !text-[var(--color-sage)]" data-hero-step="3">
                    Hotels, residences and thoughtfully selected properties, presented with care.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-3" data-hero-step="4">
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','href' => ''.e(route('properties')).'','surface' => 'dark','class' => 'fs-btn-motion']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','href' => ''.e(route('properties')).'','surface' => 'dark','class' => 'fs-btn-motion']); ?>Explore Properties <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'secondary','href' => ''.e(route('contact')).'','surface' => 'dark','class' => 'fs-btn-motion']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','href' => ''.e(route('contact')).'','surface' => 'dark','class' => 'fs-btn-motion']); ?>Start a Conversation <?php echo $__env->renderComponent(); ?>
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
    </div>

    <div class="absolute bottom-8 right-[var(--gutter)] z-20 flex items-center gap-2" data-hero-controls>
        <button type="button" data-hero-prev class="fs-carousel__btn !bg-[rgba(13,29,25,.45)] !text-[var(--color-ivory)] !border-white/20" aria-label="Previous slide">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button type="button" data-hero-pause class="h-11 px-3 rounded-full text-xs font-semibold bg-[rgba(13,29,25,.45)] text-[var(--color-ivory)] border border-white/20" aria-pressed="false">Pause</button>
        <button type="button" data-hero-next class="fs-carousel__btn !bg-[rgba(13,29,25,.45)] !text-[var(--color-ivory)] !border-white/20" aria-label="Next slide">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/></svg>
        </button>
        <div class="flex items-center gap-1.5 ml-1" role="tablist" aria-label="Slides">
            <?php $__currentLoopData = $slides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button" data-hero-dot class="relative w-6 h-6 inline-flex items-center justify-center" aria-label="Go to slide <?php echo e($i + 1); ?>" <?php if($i === 0): ?> aria-current="true" <?php endif; ?>>
                    <span class="block w-full h-0.5 bg-white/35 overflow-hidden rounded-full">
                        <?php if($i === 0): ?><span data-hero-progress class="block h-full w-0 bg-[var(--color-gold)]"></span><?php endif; ?>
                    </span>
                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <span class="sr-only" data-hero-live aria-live="polite"></span>
    </div>
</section>


<div class="relative z-30 w-full -mt-[72px]" data-hero-search>
    <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => ['class' => 'w-full','dataHeroStep' => '5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','data-hero-step' => '5']); ?>
        <?php if (isset($component)) { $__componentOriginald436d6b4a4b98f16bbd570f0eb00aa3c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald436d6b4a4b98f16bbd570f0eb00aa3c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.property-search','data' => ['suggestions' => $locationSuggestions ?? [],'types' => $searchTypes,'currency' => $currency]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('property-search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['suggestions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($locationSuggestions ?? []),'types' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($searchTypes),'currency' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currency)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald436d6b4a4b98f16bbd570f0eb00aa3c)): ?>
<?php $attributes = $__attributesOriginald436d6b4a4b98f16bbd570f0eb00aa3c; ?>
<?php unset($__attributesOriginald436d6b4a4b98f16bbd570f0eb00aa3c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald436d6b4a4b98f16bbd570f0eb00aa3c)): ?>
<?php $component = $__componentOriginald436d6b4a4b98f16bbd570f0eb00aa3c; ?>
<?php unset($__componentOriginald436d6b4a4b98f16bbd570f0eb00aa3c); ?>
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
</div>


<section class="relative z-0 fs-section-ivory pt-[calc(var(--section-y)+72px)] pb-[var(--section-y)]" aria-labelledby="intro-heading">
    <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => ['class' => 'grid lg:grid-cols-2 gap-8 lg:gap-16 items-start','dataReveal' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'grid lg:grid-cols-2 gap-8 lg:gap-16 items-start','data-reveal' => true]); ?>
        <div>
            <?php if (isset($component)) { $__componentOriginal2fd4850bfa2168af367e79900fb8c8ff = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2fd4850bfa2168af367e79900fb8c8ff = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.eyebrow','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eyebrow'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?><?php echo e($siteName ?? config('app.name')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2fd4850bfa2168af367e79900fb8c8ff)): ?>
<?php $attributes = $__attributesOriginal2fd4850bfa2168af367e79900fb8c8ff; ?>
<?php unset($__attributesOriginal2fd4850bfa2168af367e79900fb8c8ff); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2fd4850bfa2168af367e79900fb8c8ff)): ?>
<?php $component = $__componentOriginal2fd4850bfa2168af367e79900fb8c8ff; ?>
<?php unset($__componentOriginal2fd4850bfa2168af367e79900fb8c8ff); ?>
<?php endif; ?>
            <h2 id="intro-heading" class="mt-4">Spaces shape how we live, travel, and connect.</h2>
        </div>
        <div>
            <p class="fs-lead">
                We bring hospitality and property together through a thoughtful, detail-led approach.
            </p>
            <a href="<?php echo e(route('home')); ?>#about" class="mt-6 inline-flex items-center min-h-11 text-sm font-semibold text-[var(--color-gold-dark)] hover:underline">
                About us →
            </a>
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
</section>


<section class="relative z-0 fs-section fs-section-ivory-50" aria-labelledby="divisions-heading">
    <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <?php if (isset($component)) { $__componentOriginal755230460fd16c04121658d92fbf99f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal755230460fd16c04121658d92fbf99f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-heading','data' => ['eyebrow' => 'Our divisions','title' => 'Three ways to experience '.e($siteName ?? config('app.name')).'','lead' => 'Hospitality, property and living — each with its own character.','headingId' => 'divisions-heading']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['eyebrow' => 'Our divisions','title' => 'Three ways to experience '.e($siteName ?? config('app.name')).'','lead' => 'Hospitality, property and living — each with its own character.','heading-id' => 'divisions-heading']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $attributes = $__attributesOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__attributesOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $component = $__componentOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__componentOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
        <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6" data-reveal data-reveal-stagger>
            <?php $__currentLoopData = [
                ['key' => 'hotels', 'name' => 'Hotels', 'href' => route('divisions.hospitality'), 'copy' => 'Considered guest experiences, built around comfort, character and thoughtful service.', 'cta' => 'Discover Hotels'],
                ['key' => 'properties', 'name' => 'Properties', 'href' => route('properties'), 'copy' => 'A considered approach to property opportunities, ownership, leasing and management.', 'cta' => 'Discover Properties'],
                ['key' => 'living', 'name' => 'Living', 'href' => route('divisions.living'), 'copy' => 'Refined residences for stays that feel effortless, short-term or extended.', 'cta' => 'Discover Living'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $div): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="group relative rounded-[var(--radius-card)] overflow-hidden">
                    <a href="<?php echo e($div['href']); ?>" class="absolute inset-0 z-10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-gold)] rounded-[var(--radius-card)]" aria-label="<?php echo e($div['cta']); ?>"><span class="sr-only"><?php echo e($div['cta']); ?></span></a>
                    <div class="relative aspect-[16/10] lg:aspect-[4/5] fs-card-media">
                        <img src="<?php echo e($img['divisions'][$div['key']]['src']); ?>" alt="<?php echo e($img['divisions'][$div['key']]['alt']); ?>" width="1200" height="1500" loading="lazy" decoding="async" class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 fs-division-overlay" aria-hidden="true"></div>
                        <div class="absolute inset-x-0 bottom-0 p-5 md:p-6 text-[var(--color-ivory)]">
                            <h3 class="font-[family-name:var(--font-display)] text-[30px] md:text-[32px] font-semibold text-[var(--color-ivory)]"><?php echo e($div['name']); ?></h3>
                            <p class="mt-2 text-[15px] text-[var(--color-ivory)]/90 max-w-[36ch]"><?php echo e($div['copy']); ?></p>
                            <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-[var(--color-gold)] transition-transform duration-200 group-hover:translate-x-1"><?php echo e($div['cta']); ?> →</span>
                        </div>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
</section>


<section class="relative z-0 fs-section fs-section-ivory" aria-labelledby="types-heading">
    <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <?php if (isset($component)) { $__componentOriginal755230460fd16c04121658d92fbf99f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal755230460fd16c04121658d92fbf99f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-heading','data' => ['eyebrow' => 'Explore','title' => 'Browse by property type','lead' => 'Start with what you are looking for.','headingId' => 'types-heading']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['eyebrow' => 'Explore','title' => 'Browse by property type','lead' => 'Start with what you are looking for.','heading-id' => 'types-heading']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $attributes = $__attributesOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__attributesOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $component = $__componentOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__componentOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
        <div class="mt-10 grid grid-cols-2 lg:grid-cols-4 gap-4" data-reveal data-reveal-stagger>
            <?php $homeTypes = \App\Models\ListingType::query()->where('is_active', true)->orderBy('name')->take(4)->get(); ?>
            <?php $__currentLoopData = $homeTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $homeType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $href = $homeType->slug === 'hotel' ? route('divisions.hospitality') : route('properties', ['type' => $homeType->slug]);
                    $count = \App\Models\Listing::query()->published()->where('listing_type_id', $homeType->id)->count();
                ?>
                <a href="<?php echo e($href); ?>" class="flex items-center gap-3 h-24 px-4 rounded-[var(--radius-card)] bg-white border border-[var(--color-border)] hover:border-[color-mix(in_srgb,var(--color-gold-dark)_40%,transparent)] transition-colors duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-forest)]">
                    <span class="inline-flex text-[var(--color-forest)]" aria-hidden="true">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="<?php echo e($typeIcons[$homeType->slug] ?? $typeIcons['house']); ?>"/></svg>
                    </span>
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold text-[var(--color-ink)]"><?php echo e($homeType->name); ?></span>
                        <?php if($count > 0): ?>
                            <span class="block text-xs text-[var(--color-muted)] mt-0.5 fs-numeral"><?php echo e($count); ?> <?php echo e(\Illuminate\Support\Str::plural('listing', $count)); ?></span>
                        <?php endif; ?>
                    </span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
</section>


<?php if($featuredSale->isNotEmpty()): ?>
<section class="relative z-0 fs-section fs-section-ivory-50" aria-labelledby="featured-heading" data-carousel>
    <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <?php if (isset($component)) { $__componentOriginal755230460fd16c04121658d92fbf99f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal755230460fd16c04121658d92fbf99f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-heading','data' => ['eyebrow' => 'Curated','title' => 'Featured properties','lead' => 'A selection of curated listings ready to explore.','headingId' => 'featured-heading','actionHref' => ''.e(route('properties')).'','actionLabel' => 'View all']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['eyebrow' => 'Curated','title' => 'Featured properties','lead' => 'A selection of curated listings ready to explore.','heading-id' => 'featured-heading','action-href' => ''.e(route('properties')).'','action-label' => 'View all']); ?>
             <?php $__env->slot('controls', null, []); ?> 
                <div class="flex items-center gap-2" data-carousel-controls>
                    <button type="button" class="fs-carousel__btn" data-carousel-prev aria-label="Previous properties" disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button type="button" class="fs-carousel__btn" data-carousel-next aria-label="Next properties">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $attributes = $__attributesOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__attributesOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $component = $__componentOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__componentOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
        
        <div class="mt-10 fs-carousel" data-reveal>
            <div class="fs-carousel__track" data-carousel-track tabindex="0" aria-label="Featured properties">
                <?php $__currentLoopData = $featuredSale; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $house): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="fs-carousel__slide" data-carousel-slide>
                        <?php if (isset($component)) { $__componentOriginal58439408c0a13a2b2db6109a4cd58e23 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal58439408c0a13a2b2db6109a4cd58e23 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.property-card','data' => ['property' => $house,'isSample' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('property-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['property' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($house),'is-sample' => true]); ?>
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
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="fs-carousel__progress" aria-hidden="true"><span data-carousel-thumb></span></div>
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
</section>
<?php endif; ?>


<?php if($featuredStays->isNotEmpty()): ?>
<section class="relative z-0 fs-section fs-section-ivory" aria-labelledby="stays-heading" data-carousel>
    <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <?php if (isset($component)) { $__componentOriginal755230460fd16c04121658d92fbf99f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal755230460fd16c04121658d92fbf99f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-heading','data' => ['eyebrow' => 'Hospitality','title' => 'Featured stays','lead' => 'Short stays with the same care as a private residence.','headingId' => 'stays-heading','actionHref' => ''.e(route('properties', ['purpose' => 'short_stay'])).'','actionLabel' => 'View all']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['eyebrow' => 'Hospitality','title' => 'Featured stays','lead' => 'Short stays with the same care as a private residence.','heading-id' => 'stays-heading','action-href' => ''.e(route('properties', ['purpose' => 'short_stay'])).'','action-label' => 'View all']); ?>
             <?php $__env->slot('controls', null, []); ?> 
                <div class="flex items-center gap-2" data-carousel-controls>
                    <button type="button" class="fs-carousel__btn" data-carousel-prev aria-label="Previous stays" disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button type="button" class="fs-carousel__btn" data-carousel-next aria-label="Next stays">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $attributes = $__attributesOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__attributesOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $component = $__componentOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__componentOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
        <div class="mt-10 fs-carousel" data-reveal>
            <div class="fs-carousel__track" data-carousel-track tabindex="0" aria-label="Featured stays">
                <?php $__currentLoopData = $featuredStays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="fs-carousel__slide" data-carousel-slide>
                        
                        <?php if (isset($component)) { $__componentOriginal58439408c0a13a2b2db6109a4cd58e23 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal58439408c0a13a2b2db6109a4cd58e23 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.property-card','data' => ['property' => $stay,'isSample' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('property-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['property' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stay),'is-sample' => true]); ?>
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
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="fs-carousel__progress" aria-hidden="true"><span data-carousel-thumb></span></div>
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
</section>
<?php endif; ?>


<section id="about" class="relative z-0 fs-section fs-section-ivory-50 scroll-mt-[calc(var(--header-h)+16px)]" aria-labelledby="about-heading">
    <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <?php if (isset($component)) { $__componentOriginal62da636a689a5f446873e0a1627a889c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal62da636a689a5f446873e0a1627a889c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.split-section','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('split-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
             <?php $__env->slot('media', null, []); ?> 
                <div class="aspect-[4/5] w-full rounded-[var(--radius-card)] overflow-hidden" data-reveal>
                    <img src="<?php echo e($img['about']['src']); ?>" alt="<?php echo e($img['about']['alt']); ?>" width="1200" height="1500" loading="lazy" decoding="async" class="w-full h-full object-cover">
                </div>
             <?php $__env->endSlot(); ?>
            <div data-reveal>
                <?php if (isset($component)) { $__componentOriginal755230460fd16c04121658d92fbf99f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal755230460fd16c04121658d92fbf99f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-heading','data' => ['eyebrow' => 'Why '.e($siteName ?? config('app.name')).'','title' => 'A quieter, more considered way to find a place.','lead' => 'Principles that guide how we present spaces and welcome guests.','headingId' => 'about-heading']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['eyebrow' => 'Why '.e($siteName ?? config('app.name')).'','title' => 'A quieter, more considered way to find a place.','lead' => 'Principles that guide how we present spaces and welcome guests.','heading-id' => 'about-heading']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $attributes = $__attributesOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__attributesOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $component = $__componentOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__componentOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
                <div class="mt-10 space-y-6">
                    <?php $__currentLoopData = [
                        ['01', 'Thoughtful by design', 'Every detail — from materials to messaging — is chosen with care.'],
                        ['02', 'Personal by nature', 'We favour clarity and conversation over volume and noise.'],
                        // TODO owner: confirm international footprint wording.
                        ['03', 'International in outlook', 'An international outlook, with respect for local context.'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$num, $label, $copy]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="pt-5 border-t border-[var(--color-border)]">
                            <p class="font-[family-name:var(--font-display)] text-xl text-[var(--color-gold-dark)] fs-numeral"><?php echo e($num); ?></p>
                            <h3 class="mt-2 text-[var(--color-ink)]"><?php echo e($label); ?></h3>
                            <p class="mt-2 text-sm text-[var(--color-muted-strong)] max-w-[48ch]"><?php echo e($copy); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal62da636a689a5f446873e0a1627a889c)): ?>
<?php $attributes = $__attributesOriginal62da636a689a5f446873e0a1627a889c; ?>
<?php unset($__attributesOriginal62da636a689a5f446873e0a1627a889c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal62da636a689a5f446873e0a1627a889c)): ?>
<?php $component = $__componentOriginal62da636a689a5f446873e0a1627a889c; ?>
<?php unset($__componentOriginal62da636a689a5f446873e0a1627a889c); ?>
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


<section id="services" class="relative z-0 fs-section fs-section-ivory scroll-mt-[calc(var(--header-h)+16px)]" aria-labelledby="services-heading">
    <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <?php if (isset($component)) { $__componentOriginal755230460fd16c04121658d92fbf99f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal755230460fd16c04121658d92fbf99f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-heading','data' => ['eyebrow' => 'Support','title' => 'How we help','lead' => 'Guidance from first enquiry through to a confident next step.','headingId' => 'services-heading']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['eyebrow' => 'Support','title' => 'How we help','lead' => 'Guidance from first enquiry through to a confident next step.','heading-id' => 'services-heading']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $attributes = $__attributesOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__attributesOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $component = $__componentOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__componentOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
        <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-6" data-reveal data-reveal-stagger>
            <?php $__currentLoopData = [
                ['title' => 'Buying guidance', 'copy' => 'Shortlist homes that match your budget, lifestyle, and timeline.', 'href' => route('contact', ['enquiry' => 'buying']), 'icon' => 'M3 10l9-7 9 7v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z'],
                ['title' => 'Rental support', 'copy' => 'Compare apartments and residences with clear pricing and next steps.', 'href' => route('contact', ['enquiry' => 'rental']), 'icon' => 'M4 21V3h16v18M8 7h2M8 11h2M14 7h2M14 11h2'],
                ['title' => 'Listing help', 'copy' => 'Present your property with care to serious buyers and renters.', 'href' => route('contact', ['enquiry' => 'listing']), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="fs-card p-8 h-full flex flex-col">
                    <span class="inline-flex text-[var(--color-forest)]" aria-hidden="true">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?php echo e($service['icon']); ?>"/></svg>
                    </span>
                    <h3 class="mt-5"><?php echo e($service['title']); ?></h3>
                    <p class="mt-2 text-sm text-[var(--color-muted-strong)] flex-1"><?php echo e($service['copy']); ?></p>
                    <a href="<?php echo e($service['href']); ?>" class="mt-6 inline-flex items-center min-h-11 text-sm font-semibold text-[var(--color-gold-dark)] hover:underline">Talk to us →</a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
</section>


<section class="relative z-0 w-full overflow-hidden fs-break-media" data-break aria-label="Brand statement">
    <picture>
        <source media="(max-width: 767px)" srcset="<?php echo e($img['break']['mobile']); ?>">
        <img src="<?php echo e($img['break']['desktop']); ?>" alt="<?php echo e($img['break']['alt']); ?>" width="2400" height="1030" loading="lazy" decoding="async" class="w-full aspect-[4/5] md:aspect-[21/9] object-cover">
    </picture>
    <div class="absolute inset-0 fs-break-overlay" aria-hidden="true"></div>
    <div class="absolute inset-0 flex items-center justify-center px-6">
        <p class="fs-break-quote font-[family-name:var(--font-display)] font-medium text-[var(--color-ivory)] text-center text-[clamp(28px,4vw,52px)] leading-tight max-w-[18ch]">
            A quieter expression of luxury.
        </p>
    </div>
</section>

<?php if($agents->isNotEmpty()): ?>
<section id="agents" class="relative z-0 fs-section fs-section-ivory scroll-mt-[calc(var(--header-h)+16px)]" aria-labelledby="agents-heading" data-carousel>
    <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <?php if (isset($component)) { $__componentOriginal755230460fd16c04121658d92fbf99f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal755230460fd16c04121658d92fbf99f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-heading','data' => ['eyebrow' => 'People','title' => 'Meet our agents','lead' => 'Local experts ready to help.','headingId' => 'agents-heading']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['eyebrow' => 'People','title' => 'Meet our agents','lead' => 'Local experts ready to help.','heading-id' => 'agents-heading']); ?>
             <?php $__env->slot('controls', null, []); ?> 
                <div class="flex items-center gap-2" data-carousel-controls>
                    <button type="button" class="fs-carousel__btn" data-carousel-prev aria-label="Previous agents" disabled><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/></svg></button>
                    <button type="button" class="fs-carousel__btn" data-carousel-next aria-label="Next agents"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/></svg></button>
                </div>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $attributes = $__attributesOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__attributesOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $component = $__componentOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__componentOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
        <div class="mt-10 fs-carousel" data-reveal>
            <div class="fs-carousel__track" data-carousel-track tabindex="0" aria-label="Agents">
                <?php $__currentLoopData = $agents->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="fs-carousel__slide" data-carousel-slide>
                        <?php if (isset($component)) { $__componentOriginal5d2f6bef20766c94ac4196a03374805c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5d2f6bef20766c94ac4196a03374805c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.agent-card','data' => ['name' => $agent->user->name,'role' => $agent->display_role,'image' => $agent->photo_url,'listings' => $agent->user->agent_listings_count ?? null,'phone' => $agent->phone,'verified' => $agent->is_verified || $agent->user?->isKycVerified(),'rating' => $agent->averageRating(),'href' => '#agents']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('agent-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($agent->user->name),'role' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($agent->display_role),'image' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($agent->photo_url),'listings' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($agent->user->agent_listings_count ?? null),'phone' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($agent->phone),'verified' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($agent->is_verified || $agent->user?->isKycVerified()),'rating' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($agent->averageRating()),'href' => '#agents']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5d2f6bef20766c94ac4196a03374805c)): ?>
<?php $attributes = $__attributesOriginal5d2f6bef20766c94ac4196a03374805c; ?>
<?php unset($__attributesOriginal5d2f6bef20766c94ac4196a03374805c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5d2f6bef20766c94ac4196a03374805c)): ?>
<?php $component = $__componentOriginal5d2f6bef20766c94ac4196a03374805c; ?>
<?php unset($__componentOriginal5d2f6bef20766c94ac4196a03374805c); ?>
<?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="fs-carousel__progress"><span data-carousel-thumb></span></div>
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
</section>
<?php endif; ?>

<?php if($testimonials->isNotEmpty()): ?>
<section class="relative z-0 fs-section fs-section-ivory-50" aria-labelledby="testimonials-heading" data-carousel>
    <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <?php if (isset($component)) { $__componentOriginal755230460fd16c04121658d92fbf99f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal755230460fd16c04121658d92fbf99f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-heading','data' => ['eyebrow' => 'Voices','title' => 'What clients say','lead' => 'Words from people who worked with us.','headingId' => 'testimonials-heading']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['eyebrow' => 'Voices','title' => 'What clients say','lead' => 'Words from people who worked with us.','heading-id' => 'testimonials-heading']); ?>
             <?php $__env->slot('controls', null, []); ?> 
                <div class="flex items-center gap-2" data-carousel-controls>
                    <button type="button" class="fs-carousel__btn" data-carousel-prev aria-label="Previous" disabled><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/></svg></button>
                    <button type="button" class="fs-carousel__btn" data-carousel-next aria-label="Next"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/></svg></button>
                </div>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $attributes = $__attributesOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__attributesOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $component = $__componentOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__componentOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
        <div class="mt-10 fs-carousel" data-reveal>
            <div class="fs-carousel__track" data-carousel-track tabindex="0">
                <?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="fs-carousel__slide" data-carousel-slide>
                        <?php if (isset($component)) { $__componentOriginal6426b16f7e43cdd8260215c3f2dbf890 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6426b16f7e43cdd8260215c3f2dbf890 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.testimonial-card','data' => ['quote' => $item->quote,'name' => $item->name,'role' => $item->role]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('testimonial-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['quote' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->quote),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->name),'role' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->role)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6426b16f7e43cdd8260215c3f2dbf890)): ?>
<?php $attributes = $__attributesOriginal6426b16f7e43cdd8260215c3f2dbf890; ?>
<?php unset($__attributesOriginal6426b16f7e43cdd8260215c3f2dbf890); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6426b16f7e43cdd8260215c3f2dbf890)): ?>
<?php $component = $__componentOriginal6426b16f7e43cdd8260215c3f2dbf890; ?>
<?php unset($__componentOriginal6426b16f7e43cdd8260215c3f2dbf890); ?>
<?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="fs-carousel__progress"><span data-carousel-thumb></span></div>
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
</section>
<?php endif; ?>

<?php if($posts->isNotEmpty()): ?>
<section id="blog" class="relative z-0 fs-section fs-section-ivory scroll-mt-[calc(var(--header-h)+16px)]" aria-labelledby="blog-heading" data-carousel>
    <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <?php if (isset($component)) { $__componentOriginal755230460fd16c04121658d92fbf99f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal755230460fd16c04121658d92fbf99f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-heading','data' => ['eyebrow' => 'Journal','title' => 'From the blog','lead' => 'Notes on places, stays and considered living.','headingId' => 'blog-heading','actionHref' => ''.e(route('blog.index')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['eyebrow' => 'Journal','title' => 'From the blog','lead' => 'Notes on places, stays and considered living.','heading-id' => 'blog-heading','action-href' => ''.e(route('blog.index')).'']); ?>
             <?php $__env->slot('controls', null, []); ?> 
                <div class="flex items-center gap-2" data-carousel-controls>
                    <button type="button" class="fs-carousel__btn" data-carousel-prev aria-label="Previous posts" disabled><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/></svg></button>
                    <button type="button" class="fs-carousel__btn" data-carousel-next aria-label="Next posts"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/></svg></button>
                </div>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $attributes = $__attributesOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__attributesOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $component = $__componentOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__componentOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
        <div class="mt-10 fs-carousel" data-reveal>
            <div class="fs-carousel__track" data-carousel-track tabindex="0">
                <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="fs-carousel__slide" data-carousel-slide>
                        <?php if (isset($component)) { $__componentOriginalef84dbe2113ee1aa06beffddb73fe07d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalef84dbe2113ee1aa06beffddb73fe07d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.blog-card','data' => ['title' => $post->title,'excerpt' => $post->excerpt,'image' => $post->featured_image,'date' => optional($post->published_at)->toDateString(),'category' => $post->category,'href' => route('blog.show', $post->slug)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('blog-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($post->title),'excerpt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($post->excerpt),'image' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($post->featured_image),'date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(optional($post->published_at)->toDateString()),'category' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($post->category),'href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('blog.show', $post->slug))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalef84dbe2113ee1aa06beffddb73fe07d)): ?>
<?php $attributes = $__attributesOriginalef84dbe2113ee1aa06beffddb73fe07d; ?>
<?php unset($__attributesOriginalef84dbe2113ee1aa06beffddb73fe07d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalef84dbe2113ee1aa06beffddb73fe07d)): ?>
<?php $component = $__componentOriginalef84dbe2113ee1aa06beffddb73fe07d; ?>
<?php unset($__componentOriginalef84dbe2113ee1aa06beffddb73fe07d); ?>
<?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="fs-carousel__progress"><span data-carousel-thumb></span></div>
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
</section>
<?php endif; ?>


<section id="faq" class="relative z-0 fs-section fs-section-ivory-50 scroll-mt-[calc(var(--header-h)+16px)]" aria-labelledby="faq-heading">
    <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => ['class' => 'grid lg:grid-cols-12 gap-10 lg:gap-16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'grid lg:grid-cols-12 gap-10 lg:gap-16']); ?>
        <div class="lg:col-span-4" data-reveal>
            <?php if (isset($component)) { $__componentOriginal755230460fd16c04121658d92fbf99f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal755230460fd16c04121658d92fbf99f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-heading','data' => ['eyebrow' => 'Help','title' => 'Frequently asked questions','lead' => 'Quick answers before you reach out.','headingId' => 'faq-heading']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['eyebrow' => 'Help','title' => 'Frequently asked questions','lead' => 'Quick answers before you reach out.','heading-id' => 'faq-heading']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $attributes = $__attributesOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__attributesOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $component = $__componentOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__componentOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
            
        </div>
        <div class="lg:col-span-8 space-y-3" data-reveal>
            <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if (isset($component)) { $__componentOriginalfe0129bdd3eda14493d52cb648216c99 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe0129bdd3eda14493d52cb648216c99 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.faq-item','data' => ['question' => $faq->question,'answer' => $faq->answer,'open' => $index === 0]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('faq-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['question' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($faq->question),'answer' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($faq->answer),'open' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($index === 0)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe0129bdd3eda14493d52cb648216c99)): ?>
<?php $attributes = $__attributesOriginalfe0129bdd3eda14493d52cb648216c99; ?>
<?php unset($__attributesOriginalfe0129bdd3eda14493d52cb648216c99); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe0129bdd3eda14493d52cb648216c99)): ?>
<?php $component = $__componentOriginalfe0129bdd3eda14493d52cb648216c99; ?>
<?php unset($__componentOriginalfe0129bdd3eda14493d52cb648216c99); ?>
<?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
</section>

<?php if (isset($component)) { $__componentOriginalb9eda212fea7921b4a03feb1b01f47d7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb9eda212fea7921b4a03feb1b01f47d7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cta-band','data' => ['class' => 'pb-24 pt-8','rows' => [
        ['title' => 'Stay', 'copy' => 'Enquire about a short stay or hospitality partnership.', 'href' => route('contact', ['type' => 'living'])],
        ['title' => 'Property', 'copy' => 'Buying, renting, or listing a considered home.', 'href' => route('contact', ['type' => 'property'])],
        ['title' => 'Partnership', 'copy' => 'Explore working together across hospitality and property.', 'href' => route('contact', ['type' => 'partnership'])],
    ],'secondaryLabel' => 'Browse properties','secondaryHref' => route('properties')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cta-band'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'pb-24 pt-8','rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['title' => 'Stay', 'copy' => 'Enquire about a short stay or hospitality partnership.', 'href' => route('contact', ['type' => 'living'])],
        ['title' => 'Property', 'copy' => 'Buying, renting, or listing a considered home.', 'href' => route('contact', ['type' => 'property'])],
        ['title' => 'Partnership', 'copy' => 'Explore working together across hospitality and property.', 'href' => route('contact', ['type' => 'partnership'])],
    ]),'secondary-label' => 'Browse properties','secondary-href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('properties'))]); ?>
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

<?php echo $__env->make('include.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/home.blade.php ENDPATH**/ ?>