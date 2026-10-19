<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'listingTypes' => collect(),
    'cities' => collect(),
    'countries' => collect(),
    'total' => 0,
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
    'listingTypes' => collect(),
    'cities' => collect(),
    'countries' => collect(),
    'total' => 0,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $purposes = [
        '' => 'All',
        'sale' => 'Buy',
        'rent' => 'Rent',
        'short_stay' => 'Stay',
    ];
    $presets = config('search.budget_presets', []);
    $icons = config('search.type_icons', []);
    $currentPurpose = request('purpose', '');
    $currentType = request('type', '');
    $currentSearch = request('search', '');
    $currentPrice = request('price', '');
    $currentPriceMin = request('price_min', '');
    $currentPriceMax = request('price_max', '');
    $currentCity = request('city_id', '');
    $currentCountry = request('country_id', '');
    $currentBeds = request('attr_bedrooms', '');
    $currentBaths = request('attr_bathrooms', '');
    $currentFurnished = request('attr_furnished', '');
    $currentGarage = request('attr_garage', '');
    $currentStars = request('attr_star_rating', '');
    $currentBreakfast = request('attr_breakfast_included', '');
    $currentSort = request('sort', '');
    $currency = session('currency', optional($activeCountry ?? null)->currency_code ?? 'PKR');


    $typeLabel = 'Type';
    if ($currentType) {
        $match = $listingTypes->firstWhere('slug', $currentType) ?? $listingTypes->firstWhere('id', (int) $currentType);
        $typeLabel = $match?->name ? 'Type: '.$match->name : 'Type';
    }

    $priceLabel = 'Price';
    foreach ($presets as $p) {
        if (($p['price'] ?? null) !== null && (string) $p['price'] === (string) $currentPrice) {
            $priceLabel = 'Price: '.$p['label'];
            break;
        }
    }
    if ($priceLabel === 'Price' && ($currentPriceMin !== '' || $currentPriceMax !== '')) {
        $priceLabel = 'Price: set';
    }

    $bedsLabel = $currentBeds !== '' && $currentBeds !== null ? 'Beds: '.$currentBeds : 'Beds';

    $sortLabels = [
        '' => 'Newest',
        'price_asc' => 'Price low to high',
        'price_desc' => 'Price high to low',
    ];

    $isStay = $currentPurpose === 'short_stay' || $currentType === 'hotel';
    $isProperty = in_array($currentPurpose, ['sale', 'rent', ''], true) && $currentType !== 'hotel';
    if ($currentType && in_array($currentType, ['apartment', 'house', 'plot'], true)) {
        $isProperty = true;
        $isStay = false;
    }
    if ($currentPurpose === 'short_stay' || $currentType === 'hotel') {
        $isStay = true;
    }

    $moreCount = collect([
        $currentCountry,
        $currentCity,
        $currentBaths,
        $currentFurnished,
        $currentGarage,
        $currentStars,
        $currentBreakfast,
    ])->filter(fn ($v) => $v !== '' && $v !== null)->count();

    $chips = [];
    if ($currentPurpose !== '') {
        $chips[] = ['key' => 'purpose', 'label' => $purposes[$currentPurpose] ?? $currentPurpose, 'remove' => ['purpose']];
    }
    if ($currentSearch !== '') {
        $chips[] = ['key' => 'search', 'label' => $currentSearch, 'remove' => ['search']];
    }
    if ($currentType !== '') {
        $tName = optional($listingTypes->firstWhere('slug', $currentType))->name ?? $currentType;
        $chips[] = ['key' => 'type', 'label' => $tName, 'remove' => ['type']];
    }
    if ($currentCountry !== '') {
        $coName = optional($countries->firstWhere('id', (int) $currentCountry))->name ?? 'Country';
        $chips[] = ['key' => 'country_id', 'label' => $coName, 'remove' => ['country_id']];
    }
    if ($currentCity !== '') {
        $cName = optional($cities->firstWhere('id', (int) $currentCity))->name ?? 'City';
        $chips[] = ['key' => 'city_id', 'label' => $cName, 'remove' => ['city_id']];
    }
    if ($currentPrice !== '') {
        $pl = collect($presets)->firstWhere('price', $currentPrice)['label'] ?? $currentPrice;
        $chips[] = ['key' => 'price', 'label' => $pl, 'remove' => ['price', 'price_min', 'price_max']];
    } elseif ($currentPriceMin !== '' || $currentPriceMax !== '') {
        $chips[] = ['key' => 'price_range', 'label' => 'Custom budget', 'remove' => ['price_min', 'price_max']];
    }
    if ($currentBeds !== '') {
        $chips[] = ['key' => 'attr_bedrooms', 'label' => $currentBeds.' bed', 'remove' => ['attr_bedrooms']];
    }
    if ($currentBaths !== '') {
        $chips[] = ['key' => 'attr_bathrooms', 'label' => $currentBaths.' bath', 'remove' => ['attr_bathrooms']];
    }
    if ($currentFurnished !== '') {
        $chips[] = ['key' => 'attr_furnished', 'label' => ((string) $currentFurnished === '1' ? 'Furnished' : 'Unfurnished'), 'remove' => ['attr_furnished']];
    }
    if ($currentGarage !== '') {
        $chips[] = ['key' => 'attr_garage', 'label' => 'Garage', 'remove' => ['attr_garage']];
    }
    if ($currentStars !== '') {
        $chips[] = ['key' => 'attr_star_rating', 'label' => $currentStars.'★', 'remove' => ['attr_star_rating']];
    }
    if ($currentBreakfast !== '') {
        $chips[] = ['key' => 'attr_breakfast_included', 'label' => 'Breakfast', 'remove' => ['attr_breakfast_included']];
    }

    $chipUrl = function (array $removeKeys) {
        $q = request()->except(array_merge($removeKeys, ['page']));
        return route('properties', $q);
    };

    $purposeUrl = function (string $purpose) {
        $q = request()->except(['purpose', 'page']);
        if ($purpose !== '') {
            $q['purpose'] = $purpose;
        }
        return route('properties', $q);
    };
?>

<div
    x-data="propertiesFilters"
    data-alpine-config="<?php echo e(json_encode(['moreCount' => $moreCount], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)); ?>"
    class="w-full"
>
    
    <div
        class="fs-props-toolbar"
        data-props-toolbar
        :class="stuck ? 'is-stuck' : ''"
    >
        <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => ['class' => 'py-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'py-3']); ?>
            
            <div class="lg:hidden mb-3 -mx-1 px-1 overflow-x-auto fs-hide-scrollbar snap-x snap-mandatory">
                <div class="fs-purpose-seg inline-flex min-w-max">
                    <?php $__currentLoopData = $purposes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($purposeUrl($value)); ?>" class="snap-start <?php echo e((string) $currentPurpose === (string) $value ? 'is-active' : ''); ?>"><?php echo e($label); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <form method="GET" action="<?php echo e(route('properties')); ?>" class="flex flex-wrap items-center gap-2 lg:gap-3" id="properties-filter-form">
                <?php if($currentSort): ?>
                    <input type="hidden" name="sort" value="<?php echo e($currentSort); ?>">
                <?php endif; ?>

                
                <div class="hidden lg:block fs-purpose-seg shrink-0" role="group" aria-label="Purpose">
                    <?php $__currentLoopData = $purposes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($purposeUrl($value)); ?>" class="<?php echo e((string) $currentPurpose === (string) $value ? 'is-active' : ''); ?>"><?php echo e($label); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <?php if($currentPurpose !== ''): ?>
                    <input type="hidden" name="purpose" value="<?php echo e($currentPurpose); ?>">
                <?php endif; ?>
                <?php if($currentCountry !== ''): ?>
                    <input type="hidden" name="country_id" value="<?php echo e($currentCountry); ?>">
                <?php endif; ?>
                <?php if($currentCity !== ''): ?>
                    <input type="hidden" name="city_id" value="<?php echo e($currentCity); ?>">
                <?php endif; ?>

                <label class="relative flex-1 min-w-[160px] lg:min-w-[200px] lg:max-w-xs">
                    <span class="sr-only">Search city, area or project</span>
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-muted)]" aria-hidden="true">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                    </span>
                    <input
                        type="search"
                        name="search"
                        value="<?php echo e($currentSearch); ?>"
                        placeholder="City, area or project"
                        class="w-full h-11 pl-10 pr-9 rounded-[var(--radius-button)] border border-[var(--color-border)] bg-white text-sm text-[var(--color-ink)] placeholder:text-[var(--color-muted)] focus-visible:outline-2 focus-visible:outline-offset-0 focus-visible:outline-[var(--color-forest)]"
                    >
                </label>

                
                <div class="hidden lg:flex items-center gap-2">
                    <div class="relative" @click.outside="closePopover('type')">
                        <button type="button" class="fs-filter-trigger <?php echo e($currentType ? 'is-active' : ''); ?>" @click="togglePopover('type')" :aria-expanded="openPopover === 'type' ? 'true' : 'false'" aria-haspopup="listbox">
                            <?php echo e($typeLabel); ?>

                            <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-cloak x-show="openPopover === 'type'" class="fs-props-popover" role="listbox" aria-label="Property type">
                            <label class="flex items-center gap-2 p-2 rounded-[var(--radius-input)] hover:bg-[var(--color-ivory-50)] cursor-pointer">
                                <input type="radio" name="type" value="" <?php if($currentType === ''): echo 'checked'; endif; ?> class="accent-[var(--color-forest)]">
                                <span class="text-sm">Any type</span>
                            </label>
                            <?php $__currentLoopData = $listingTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-2 p-2 rounded-[var(--radius-input)] hover:bg-[var(--color-ivory-50)] cursor-pointer">
                                    <input type="radio" name="type" value="<?php echo e($t->slug); ?>" <?php if($currentType === $t->slug): echo 'checked'; endif; ?> class="accent-[var(--color-forest)]">
                                    <span class="text-sm"><?php echo e($t->name); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-2 flex justify-end">
                                <button type="submit" class="h-10 px-4 rounded-[var(--radius-button)] bg-[var(--color-forest)] text-[var(--color-ivory)] text-sm font-semibold">Apply</button>
                            </div>
                        </div>
                    </div>

                    <div class="relative" @click.outside="closePopover('price')">
                        <button type="button" class="fs-filter-trigger <?php echo e(($currentPrice || $currentPriceMin || $currentPriceMax) ? 'is-active' : ''); ?>" @click="togglePopover('price')" :aria-expanded="openPopover === 'price' ? 'true' : 'false'">
                            <?php echo e($priceLabel); ?>

                            <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-cloak x-show="openPopover === 'price'" class="fs-props-popover min-w-[300px]">
                            <div class="grid grid-cols-2 gap-3">
                                <label class="text-xs font-semibold uppercase tracking-wide text-[var(--color-muted)]">
                                    Min (<?php echo e($currency); ?>)
                                    <input type="number" name="price_min" value="<?php echo e($currentPriceMin); ?>" inputmode="numeric" class="mt-1 w-full h-11 px-3 rounded-[var(--radius-input)] border border-[var(--color-border)] text-sm" placeholder="0">
                                </label>
                                <label class="text-xs font-semibold uppercase tracking-wide text-[var(--color-muted)]">
                                    Max (<?php echo e($currency); ?>)
                                    <input type="number" name="price_max" value="<?php echo e($currentPriceMax); ?>" inputmode="numeric" class="mt-1 w-full h-11 px-3 rounded-[var(--radius-input)] border border-[var(--color-border)] text-sm" placeholder="Any">
                                </label>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <?php $__currentLoopData = $presets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $preset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="inline-flex items-center h-9 px-3 rounded-full border border-[var(--color-border)] text-xs font-semibold cursor-pointer has-[:checked]:bg-[var(--color-forest)] has-[:checked]:text-[var(--color-ivory)] has-[:checked]:border-[var(--color-forest)]">
                                        <input type="radio" name="price" value="<?php echo e($preset['price'] ?? ''); ?>" class="sr-only" <?php if((string) $currentPrice === (string) ($preset['price'] ?? '')): echo 'checked'; endif; ?>>
                                        <?php echo e($preset['label']); ?>

                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <div class="mt-3 flex justify-end gap-2">
                                <a href="<?php echo e($chipUrl(['price', 'price_min', 'price_max'])); ?>" class="h-10 px-3 inline-flex items-center text-sm font-semibold text-[var(--color-muted)]">Clear</a>
                                <button type="submit" class="h-10 px-4 rounded-[var(--radius-button)] bg-[var(--color-forest)] text-[var(--color-ivory)] text-sm font-semibold">Apply</button>
                            </div>
                        </div>
                    </div>

                    <div class="relative" @click.outside="closePopover('beds')">
                        <button type="button" class="fs-filter-trigger <?php echo e($currentBeds !== '' ? 'is-active' : ''); ?>" @click="togglePopover('beds')" :aria-expanded="openPopover === 'beds' ? 'true' : 'false'">
                            <?php echo e($bedsLabel); ?>

                            <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-cloak x-show="openPopover === 'beds'" class="fs-props-popover">
                            <p class="text-xs font-semibold uppercase tracking-wide text-[var(--color-muted)] mb-2">Bedrooms</p>
                            <div class="flex flex-wrap gap-2">
                                <label class="inline-flex items-center justify-center min-w-11 h-11 px-3 rounded-full border border-[var(--color-border)] text-sm font-semibold cursor-pointer has-[:checked]:bg-[var(--color-forest)] has-[:checked]:text-[var(--color-ivory)]">
                                    <input type="radio" name="attr_bedrooms" value="" class="sr-only" <?php if($currentBeds === '' || $currentBeds === null): echo 'checked'; endif; ?>>
                                    Any
                                </label>
                                <?php $__currentLoopData = [1, 2, 3, 4, 5]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="inline-flex items-center justify-center min-w-11 h-11 px-3 rounded-full border border-[var(--color-border)] text-sm font-semibold cursor-pointer has-[:checked]:bg-[var(--color-forest)] has-[:checked]:text-[var(--color-ivory)]">
                                        <input type="radio" name="attr_bedrooms" value="<?php echo e($n); ?>" class="sr-only" <?php if((string) $currentBeds === (string) $n): echo 'checked'; endif; ?>>
                                        <?php echo e($n); ?><?php echo e($n === 5 ? '+' : ''); ?>

                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <p class="mt-2 text-[11px] text-[var(--color-muted)]">Matches exact bedroom count.</p>
                            <div class="mt-3 flex justify-end">
                                <button type="submit" class="h-10 px-4 rounded-[var(--radius-button)] bg-[var(--color-forest)] text-[var(--color-ivory)] text-sm font-semibold">Apply</button>
                            </div>
                        </div>
                    </div>
                </div>

                
                <button
                    type="button"
                    class="fs-filter-trigger <?php echo e($moreCount ? 'is-active' : ''); ?>"
                    @click="openDrawer()"
                    :aria-expanded="drawerOpen ? 'true' : 'false'"
                    aria-controls="properties-more-filters"
                >
                    More filters
                    <?php if($moreCount): ?>
                        <span class="inline-flex items-center justify-center min-w-5 h-5 px-1 rounded-full bg-[var(--color-forest)] text-[var(--color-ivory)] text-[11px] font-bold fs-numeral"><?php echo e($moreCount); ?></span>
                    <?php endif; ?>
                </button>

                
                <div class="relative ml-auto" @click.outside="closePopover('sort')">
                    <button type="button" class="fs-filter-trigger" @click="togglePopover('sort')" :aria-expanded="openPopover === 'sort' ? 'true' : 'false'">
                        Sort: <?php echo e($sortLabels[$currentSort] ?? 'Newest'); ?>

                        <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-cloak x-show="openPopover === 'sort'" class="fs-props-popover right-0 left-auto min-w-[220px]" role="listbox">
                        <?php $__currentLoopData = $sortLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a
                                href="<?php echo e(route('properties', array_merge(request()->except(['sort', 'page']), $value !== '' ? ['sort' => $value] : []))); ?>"
                                class="flex items-center min-h-11 px-3 rounded-[var(--radius-input)] text-sm <?php echo e((string) $currentSort === (string) $value ? 'bg-[var(--color-ivory-50)] font-semibold text-[var(--color-forest)]' : 'text-[var(--color-ink)] hover:bg-[var(--color-ivory-50)]'); ?>"
                                <?php if((string) $currentSort === (string) $value): ?> aria-current="true" <?php endif; ?>
                            ><?php echo e($label); ?></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                
                <?php if($currentType && false): ?> <?php endif; ?>
            </form>
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

    
    <?php if(count($chips)): ?>
        <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => ['class' => 'pt-4 pb-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'pt-4 pb-2']); ?>
            <div class="flex flex-wrap items-center gap-2">
                <?php $__currentLoopData = $chips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="fs-filter-chip">
                        <?php echo e($chip['label']); ?>

                        <a href="<?php echo e($chipUrl($chip['remove'])); ?>" class="inline-flex" aria-label="Remove filter <?php echo e($chip['label']); ?>">
                            <span class="sr-only">Remove</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('properties')); ?>" class="inline-flex items-center min-h-9 px-2 text-sm font-semibold text-[var(--color-gold-dark)] hover:underline underline-offset-2">Clear all</a>
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
    <?php endif; ?>

    
    <div
        id="properties-more-filters"
        x-cloak
        x-show="drawerOpen"
        class="fs-props-drawer"
        role="dialog"
        aria-modal="true"
        aria-label="More filters"
        @keydown.escape.window="closeDrawer()"
    >
        <div class="absolute inset-0 bg-[var(--color-forest-shadow)]/45" @click="closeDrawer()"></div>
        <div
            class="fs-props-drawer__panel"
            x-show="drawerOpen"
            x-transition:enter="transition transform duration-300"
            x-transition:enter-start="translate-x-full lg:translate-x-full max-lg:translate-y-full"
            x-transition:enter-end="translate-x-0 max-lg:translate-y-0"
            x-transition:leave="transition transform duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full max-lg:translate-y-full"
            @keydown.tab="trapDrawerFocus($event)"
        >
            <div class="flex items-center justify-between h-14 px-4 border-b border-[var(--color-border)] shrink-0">
                <h2 class="text-base font-semibold text-[var(--color-ink)]">More filters</h2>
                <button type="button" class="inline-flex items-center justify-center w-11 h-11 rounded-[var(--radius-button)] border border-[var(--color-border)]" @click="closeDrawer()" aria-label="Close filters">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="GET" action="<?php echo e(route('properties')); ?>" class="flex-1 overflow-y-auto p-4 space-y-6">
                <?php $__currentLoopData = ['search', 'purpose', 'type', 'price', 'price_min', 'price_max', 'attr_bedrooms', 'sort']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $keep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(request()->filled($keep)): ?>
                        <input type="hidden" name="<?php echo e($keep); ?>" value="<?php echo e(request($keep)); ?>">
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <details class="lg:hidden group border-b border-[var(--color-border)] pb-4" open>
                    <summary class="flex items-center justify-between min-h-11 cursor-pointer text-sm font-semibold text-[var(--color-ink)]">Type &amp; price</summary>
                    <div class="mt-3 space-y-3">
                        <label class="block text-xs font-semibold uppercase tracking-wide text-[var(--color-muted)]">
                            Type
                            <select name="type" class="mt-1 w-full h-11 px-3 rounded-[var(--radius-input)] border border-[var(--color-border)] text-sm bg-white">
                                <option value="">Any type</option>
                                <?php $__currentLoopData = $listingTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($t->slug); ?>" <?php if($currentType === $t->slug): echo 'selected'; endif; ?>><?php echo e($t->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </label>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-[var(--color-muted)]">
                            Budget preset
                            <select name="price" class="mt-1 w-full h-11 px-3 rounded-[var(--radius-input)] border border-[var(--color-border)] text-sm bg-white">
                                <?php $__currentLoopData = $presets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $preset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($preset['price'] ?? ''); ?>" <?php if((string) $currentPrice === (string) ($preset['price'] ?? '')): echo 'selected'; endif; ?>><?php echo e($preset['label']); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </label>
                    </div>
                </details>

                <details class="group border-b border-[var(--color-border)] pb-4" open>
                    <summary class="flex items-center justify-between min-h-11 cursor-pointer text-sm font-semibold text-[var(--color-ink)]">Location</summary>
                    <div class="mt-2">
                        <?php if (isset($component)) { $__componentOriginal85a49ba5b5469157f45f95cc92fe58f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal85a49ba5b5469157f45f95cc92fe58f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.location-picker','data' => ['countries' => $countries,'countryName' => 'country_id','cityName' => 'city_id','countryId' => $currentCountry ?: null,'cityId' => $currentCity ?: null,'required' => false,'citiesUrlTemplate' => url('/countries/__COUNTRY__/cities')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('location-picker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['countries' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($countries),'country-name' => 'country_id','city-name' => 'city_id','country-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentCountry ?: null),'city-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentCity ?: null),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'cities-url-template' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(url('/countries/__COUNTRY__/cities'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal85a49ba5b5469157f45f95cc92fe58f0)): ?>
<?php $attributes = $__attributesOriginal85a49ba5b5469157f45f95cc92fe58f0; ?>
<?php unset($__attributesOriginal85a49ba5b5469157f45f95cc92fe58f0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal85a49ba5b5469157f45f95cc92fe58f0)): ?>
<?php $component = $__componentOriginal85a49ba5b5469157f45f95cc92fe58f0; ?>
<?php unset($__componentOriginal85a49ba5b5469157f45f95cc92fe58f0); ?>
<?php endif; ?>
                        <span class="mt-2 block text-[12px] text-[var(--color-muted)]">
                            Pakistan uses PKR; other countries show prices in USD.
                        </span>
                    </div>
                </details>

                <details class="group border-b border-[var(--color-border)] pb-4" open>
                    <summary class="flex items-center justify-between min-h-11 cursor-pointer text-sm font-semibold text-[var(--color-ink)]">Rooms</summary>
                    <div class="mt-2 grid grid-cols-2 gap-3">
                        <label class="text-xs font-semibold uppercase tracking-wide text-[var(--color-muted)]">
                            Bedrooms
                            <select name="attr_bedrooms" class="mt-1 w-full h-11 px-3 rounded-[var(--radius-input)] border border-[var(--color-border)] text-sm bg-white">
                                <option value="">Any</option>
                                <?php $__currentLoopData = [1,2,3,4,5]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($n); ?>" <?php if((string) $currentBeds === (string) $n): echo 'selected'; endif; ?>><?php echo e($n); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </label>
                        <label class="text-xs font-semibold uppercase tracking-wide text-[var(--color-muted)]">
                            Bathrooms
                            <select name="attr_bathrooms" class="mt-1 w-full h-11 px-3 rounded-[var(--radius-input)] border border-[var(--color-border)] text-sm bg-white">
                                <option value="">Any</option>
                                <?php $__currentLoopData = [1,2,3,4,5]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($n); ?>" <?php if((string) $currentBaths === (string) $n): echo 'selected'; endif; ?>><?php echo e($n); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </label>
                    </div>
                </details>

                <?php if($isProperty || $currentPurpose === '' || $currentPurpose === 'sale' || $currentPurpose === 'rent'): ?>
                    <details class="group border-b border-[var(--color-border)] pb-4" open data-filter-group="property">
                        <summary class="flex items-center justify-between min-h-11 cursor-pointer text-sm font-semibold text-[var(--color-ink)]">Property details</summary>
                        <div class="mt-2 space-y-3">
                            <label class="text-xs font-semibold uppercase tracking-wide text-[var(--color-muted)] block">
                                Furnished
                                <select name="attr_furnished" class="mt-1 w-full h-11 px-3 rounded-[var(--radius-input)] border border-[var(--color-border)] text-sm bg-white">
                                    <option value="">Any</option>
                                    <option value="1" <?php if((string) $currentFurnished === '1'): echo 'selected'; endif; ?>>Yes</option>
                                    <option value="0" <?php if((string) $currentFurnished === '0'): echo 'selected'; endif; ?>>No</option>
                                </select>
                            </label>
                            <label class="text-xs font-semibold uppercase tracking-wide text-[var(--color-muted)] block">
                                Garage / parking
                                <select name="attr_garage" class="mt-1 w-full h-11 px-3 rounded-[var(--radius-input)] border border-[var(--color-border)] text-sm bg-white">
                                    <option value="">Any</option>
                                    <option value="1" <?php if((string) $currentGarage === '1'): echo 'selected'; endif; ?>>Yes</option>
                                    <option value="0" <?php if((string) $currentGarage === '0'): echo 'selected'; endif; ?>>No</option>
                                </select>
                            </label>
                        </div>
                    </details>
                <?php endif; ?>

                <?php if($isStay || $currentPurpose === 'short_stay' || $currentType === 'hotel'): ?>
                    <details class="group border-b border-[var(--color-border)] pb-4" open data-filter-group="hotel">
                        <summary class="flex items-center justify-between min-h-11 cursor-pointer text-sm font-semibold text-[var(--color-ink)]">Hotel stay</summary>
                        <div class="mt-2 space-y-3">
                            <label class="text-xs font-semibold uppercase tracking-wide text-[var(--color-muted)] block">
                                Star rating
                                <select name="attr_star_rating" class="mt-1 w-full h-11 px-3 rounded-[var(--radius-input)] border border-[var(--color-border)] text-sm bg-white">
                                    <option value="">Any</option>
                                    <?php $__currentLoopData = ['3','4','5']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($s); ?>" <?php if((string) $currentStars === $s): echo 'selected'; endif; ?>><?php echo e($s); ?> star</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </label>
                            <label class="text-xs font-semibold uppercase tracking-wide text-[var(--color-muted)] block">
                                Breakfast included
                                <select name="attr_breakfast_included" class="mt-1 w-full h-11 px-3 rounded-[var(--radius-input)] border border-[var(--color-border)] text-sm bg-white">
                                    <option value="">Any</option>
                                    <option value="1" <?php if((string) $currentBreakfast === '1'): echo 'selected'; endif; ?>>Yes</option>
                                    <option value="0" <?php if((string) $currentBreakfast === '0'): echo 'selected'; endif; ?>>No</option>
                                </select>
                            </label>
                        </div>
                    </details>
                <?php endif; ?>

                <div class="sticky bottom-0 -mx-4 px-4 py-4 bg-white border-t border-[var(--color-border)] flex items-center gap-3">
                    <a href="<?php echo e(route('properties')); ?>" class="inline-flex items-center justify-center h-12 px-4 text-sm font-semibold text-[var(--color-muted)] hover:text-[var(--color-forest)]">Reset</a>
                    <button type="submit" class="flex-1 h-12 rounded-[var(--radius-button)] bg-[var(--color-forest)] text-[var(--color-ivory)] text-sm font-semibold">
                        Show <?php echo e(number_format($total)); ?> results
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/properties-filters.blade.php ENDPATH**/ ?>