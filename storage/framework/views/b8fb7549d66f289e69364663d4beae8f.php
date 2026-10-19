<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'countries' => collect(),
    'countryName' => 'country_id',
    'cityName' => 'city_id',
    'countryId' => null,
    'cityId' => null,
    'cityLabel' => null,
    'required' => false,
    'countryLabel' => 'Country',
    'cityLabelText' => 'City',
    'citiesUrlTemplate' => null,
    'allowEmptyCountry' => true,
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
    'countries' => collect(),
    'countryName' => 'country_id',
    'cityName' => 'city_id',
    'countryId' => null,
    'cityId' => null,
    'cityLabel' => null,
    'required' => false,
    'countryLabel' => 'Country',
    'cityLabelText' => 'City',
    'citiesUrlTemplate' => null,
    'allowEmptyCountry' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $selectedCountry = old($countryName, $countryId);
    $selectedCity = old($cityName, $cityId);
    $selectedCityLabel = $cityLabel;
    if ($selectedCity && ! $selectedCityLabel) {
        $selectedCityLabel = optional(\App\Models\City::query()->find($selectedCity))->displayLabel();
    }
    $citiesUrlTemplate = $citiesUrlTemplate ?: url('/countries/__COUNTRY__/cities');
    $config = [
        'countryName' => $countryName,
        'cityName' => $cityName,
        'countryId' => $selectedCountry !== null && $selectedCountry !== '' ? (int) $selectedCountry : null,
        'cityId' => $selectedCity !== null && $selectedCity !== '' ? (int) $selectedCity : null,
        'cityLabel' => $selectedCityLabel,
        'citiesUrlTemplate' => $citiesUrlTemplate,
        'required' => (bool) $required,
        'debounceMs' => 300,
        'minChars' => 2,
    ];
?>

<div
    <?php echo e($attributes->merge(['class' => 'grid gap-4 sm:grid-cols-2'])); ?>

    x-data="locationPicker"
    data-alpine-config="<?php echo e(json_encode($config, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)); ?>"
>
    <div>
        <label for="<?php echo e($countryName); ?>" class="block text-xs font-semibold text-fs-primary mb-1"><?php echo e($countryLabel); ?><?php if($required): ?> * <?php endif; ?></label>
        <select
            name="<?php echo e($countryName); ?>"
            id="<?php echo e($countryName); ?>"
            class="w-full h-12 px-4 rounded-sm border border-slate-200 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20"
            x-model="countryId"
            @change="onCountryChange()"
            <?php if($required): ?> required <?php endif; ?>
        >
            <?php if($allowEmptyCountry): ?>
                <option value="">Select country</option>
            <?php endif; ?>
            <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $cid = is_array($c) ? $c['id'] : $c->id;
                    $cname = is_array($c) ? $c['name'] : $c->name;
                ?>
                <option value="<?php echo e($cid); ?>" <?php if((string) $selectedCountry === (string) $cid): echo 'selected'; endif; ?>><?php echo e($cname); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = [$countryName];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <p class="mt-1 text-xs text-fs-danger"><?php echo e($message); ?></p>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="relative">
        <label for="<?php echo e($cityName); ?>_search" class="block text-xs font-semibold text-fs-primary mb-1"><?php echo e($cityLabelText); ?><?php if($required): ?> * <?php endif; ?></label>
        <input type="hidden" name="<?php echo e($cityName); ?>" x-model="cityId">
        <input
            type="search"
            id="<?php echo e($cityName); ?>_search"
            class="w-full h-12 px-4 rounded-sm border border-slate-200 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20"
            placeholder="Search city…"
            autocomplete="off"
            x-model="cityQuery"
            @input="onCityQuery()"
            @focus="onCityFocus()"
            @keydown.escape="closeDropdown()"
            :disabled="cityDisabled"
        >
        <div
            x-show="showDropdown"
            x-cloak
            class="absolute z-20 mt-1 w-full max-h-56 overflow-auto rounded-sm border border-slate-200 bg-white shadow-sm"
            @click.outside="closeDropdown()"
        >
            <p x-show="loading" class="px-3 py-2 text-xs text-fs-muted">Loading…</p>
            <div x-show="showError" class="px-3 py-2 text-xs text-fs-danger">
                <span x-text="error"></span>
                <button type="button" class="ml-2 underline" @click="loadCities()">Retry</button>
            </div>
            <p x-show="showEmpty" class="px-3 py-2 text-xs text-fs-muted">No cities found</p>
            <template x-for="city in results" :key="city.id">
                <button
                    type="button"
                    class="block w-full text-left px-3 py-2 text-sm hover:bg-fs-surface"
                    @click="selectCity(city)"
                    x-text="city.label"
                ></button>
            </template>
        </div>
        <p x-show="hasSelection" class="mt-1 text-xs text-fs-muted" x-text="selectedHint"></p>
        <?php $__errorArgs = [$cityName];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <p class="mt-1 text-xs text-fs-danger"><?php echo e($message); ?></p>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/components/location-picker.blade.php ENDPATH**/ ?>