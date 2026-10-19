<?php
    use App\Support\Media;
    $featuredUrl = filled($listing?->featured_image) ? Media::url($listing->featured_image) : '';
    $selectedType = (string) old('listing_type_id', $listing?->listing_type_id ?? ($listingTypes->first()->id ?? ''));
    $currentAttrs = old('attributes', $listing?->attributeValues() ?? []);
    $listingFormConfig = [
        'listingTypeId' => $selectedType,
        'types' => $typesPayload,
        'attrValues' => $currentAttrs,
    ];
?>

<div
    class="grid grid-cols-1 md:grid-cols-2 gap-4"
    x-data="listingForm"
    data-alpine-config="<?php echo e(json_encode($listingFormConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)); ?>"
>
    <div class="md:col-span-2">
        <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['label' => 'Title *','name' => 'title','value' => old('title', $listing?->title),'placeholder' => 'e.g. Modern Villa in DHA','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Title *','name' => 'title','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('title', $listing?->title)),'placeholder' => 'e.g. Modern Villa in DHA','required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $attributes = $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $component = $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
    </div>
    <div class="md:col-span-2">
        <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['label' => 'Location *','name' => 'location','value' => old('location', $listing?->location),'placeholder' => 'e.g. DHA Phase 6, Karachi','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Location *','name' => 'location','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('location', $listing?->location)),'placeholder' => 'e.g. DHA Phase 6, Karachi','required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $attributes = $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $component = $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
    </div>

    <div>
        <label for="listing_type_id" class="block text-xs font-semibold text-fs-primary mb-1">Listing type *</label>
        <select
            id="listing_type_id"
            name="listing_type_id"
            required
            x-model="listingTypeId"
            class="w-full h-12 px-4 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20"
        >
            <option value="">Select type</option>
            <?php $__currentLoopData = $listingTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($type->id); ?>"><?php echo e($type->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['listing_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-fs-danger"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['label' => 'Purpose *','name' => 'purpose','options' => collect($purposes)->mapWithKeys(fn ($p) => [$p => str_replace('_', ' ', ucfirst($p))])->all(),'selected' => old('purpose', $listing?->purpose ?? 'sale'),'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Purpose *','name' => 'purpose','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($purposes)->mapWithKeys(fn ($p) => [$p => str_replace('_', ' ', ucfirst($p))])->all()),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('purpose', $listing?->purpose ?? 'sale')),'required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['label' => 'Price *','name' => 'price','type' => 'number','value' => old('price', $listing?->price),'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Price *','name' => 'price','type' => 'number','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('price', $listing?->price)),'required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $attributes = $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $component = $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['label' => 'Price unit *','name' => 'price_unit','options' => collect($priceUnits)->mapWithKeys(fn ($u) => [$u => str_replace('_', ' ', ucfirst($u))])->all(),'selected' => old('price_unit', $listing?->price_unit ?? 'total'),'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Price unit *','name' => 'price_unit','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($priceUnits)->mapWithKeys(fn ($u) => [$u => str_replace('_', ' ', ucfirst($u))])->all()),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('price_unit', $listing?->price_unit ?? 'total')),'required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['label' => 'Currency','name' => 'currency','value' => old('currency', $listing?->currency ?? optional(\App\Support\CountryContext::current())->currency_code)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Currency','name' => 'currency','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('currency', $listing?->currency ?? optional(\App\Support\CountryContext::current())->currency_code))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $attributes = $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $component = $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['label' => 'Status *','name' => 'status','options' => collect($statuses)->mapWithKeys(fn ($s) => [$s => ucfirst($s)])->all(),'selected' => old('status', $listing?->status ?? 'published'),'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Status *','name' => 'status','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($statuses)->mapWithKeys(fn ($s) => [$s => ucfirst($s)])->all()),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('status', $listing?->status ?? 'published')),'required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal85a49ba5b5469157f45f95cc92fe58f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal85a49ba5b5469157f45f95cc92fe58f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.location-picker','data' => ['class' => 'sm:col-span-2','countries' => \App\Support\CountryCatalog::forUser(auth()->user()),'countryName' => 'country_id','cityName' => 'city_id','countryId' => old('country_id', $listing?->country_id ?? optional(\App\Support\CountryContext::current())->id),'cityId' => old('city_id', $listing?->city_id),'citiesUrlTemplate' => url('/countries/__COUNTRY__/cities')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('location-picker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'sm:col-span-2','countries' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\CountryCatalog::forUser(auth()->user())),'country-name' => 'country_id','city-name' => 'city_id','country-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('country_id', $listing?->country_id ?? optional(\App\Support\CountryContext::current())->id)),'city-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('city_id', $listing?->city_id)),'cities-url-template' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(url('/countries/__COUNTRY__/cities'))]); ?>
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
    <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['label' => 'Area size','name' => 'area_size','type' => 'number','value' => old('area_size', $listing?->area_size)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Area size','name' => 'area_size','type' => 'number','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('area_size', $listing?->area_size))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $attributes = $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $component = $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['label' => 'Area unit','name' => 'area_unit','options' => collect($areaUnits)->mapWithKeys(fn ($u) => [$u => str_replace('_', ' ', $u)])->all(),'selected' => old('area_unit', $listing?->area_unit),'placeholder' => 'Unit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Area unit','name' => 'area_unit','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($areaUnits)->mapWithKeys(fn ($u) => [$u => str_replace('_', ' ', $u)])->all()),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('area_unit', $listing?->area_unit)),'placeholder' => 'Unit']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['label' => 'Owner','name' => 'owner_id','options' => $users->mapWithKeys(fn ($u) => [$u->id => $u->name])->all(),'selected' => old('owner_id', $listing?->owner_id),'placeholder' => 'Optional']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Owner','name' => 'owner_id','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users->mapWithKeys(fn ($u) => [$u->id => $u->name])->all()),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('owner_id', $listing?->owner_id)),'placeholder' => 'Optional']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['label' => 'Agent','name' => 'agent_id','options' => $users->mapWithKeys(fn ($u) => [$u->id => $u->name])->all(),'selected' => old('agent_id', $listing?->agent_id),'placeholder' => 'Optional']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Agent','name' => 'agent_id','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($users->mapWithKeys(fn ($u) => [$u->id => $u->name])->all()),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('agent_id', $listing?->agent_id)),'placeholder' => 'Optional']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>

    <div class="md:col-span-2">
        <label for="description" class="block text-xs font-semibold text-fs-primary mb-1">Description</label>
        <textarea id="description" name="description" rows="4" placeholder="Brief description..."
                  class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20"><?php echo e(old('description', $listing?->description)); ?></textarea>
        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-fs-danger"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <?php if (isset($component)) { $__componentOriginal36665f0dc0e45320e21db1e20a989acf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36665f0dc0e45320e21db1e20a989acf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.panel','data' => ['class' => 'md:col-span-2 bg-fs-surface/60 space-y-3','xShow' => 'hasDynamicAttributes','xCloak' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'md:col-span-2 bg-fs-surface/60 space-y-3','x-show' => 'hasDynamicAttributes','x-cloak' => true]); ?>
        <h3 class="fs-display text-sm font-semibold text-fs-primary">Type attributes</h3>
        <template x-for="attr in dynamicAttributes" :key="attr.key">
            <div>
                <label class="block text-xs font-semibold text-fs-primary mb-1" :for="attrId(attr.key)">
                    <span x-text="attr.label"></span>
                    <span x-show="attr.is_required"> *</span>
                </label>

                <template x-if="isInputType(attr, 'boolean')">
                    <label class="inline-flex items-center gap-2 min-h-11">
                        <input type="hidden" :name="attrName(attr.key)" value="0">
                        <input
                            type="checkbox"
                            value="1"
                            class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand"
                            :name="attrName(attr.key)"
                            :id="attrId(attr.key)"
                            :checked="isAttrChecked(attr.key)"
                        >
                        <span class="text-sm text-fs-secondary">Yes</span>
                    </label>
                </template>

                <template x-if="isInputType(attr, 'select')">
                    <select
                        class="w-full h-12 px-4 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20"
                        :name="attrName(attr.key)"
                        :id="attrId(attr.key)"
                        :required="attr.is_required"
                    >
                        <option value="">Select</option>
                        <template x-for="opt in optionEntries(attr.options)" :key="opt.value">
                            <option :value="opt.value" :selected="isOptSelected(attr.key, opt.value)" x-text="opt.label"></option>
                        </template>
                    </select>
                </template>

                <template x-if="isInputType(attr, 'number')">
                    <input
                        type="number"
                        class="w-full h-12 px-4 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20"
                        :name="attrName(attr.key)"
                        :id="attrId(attr.key)"
                        :required="attr.is_required"
                        :value="attrValue(attr.key)"
                    >
                </template>

                <template x-if="isInputType(attr, 'text')">
                    <input
                        type="text"
                        class="w-full h-12 px-4 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20"
                        :name="attrName(attr.key)"
                        :id="attrId(attr.key)"
                        :required="attr.is_required"
                        :value="attrValue(attr.key)"
                    >
                </template>
            </div>
        </template>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal36665f0dc0e45320e21db1e20a989acf)): ?>
<?php $attributes = $__attributesOriginal36665f0dc0e45320e21db1e20a989acf; ?>
<?php unset($__attributesOriginal36665f0dc0e45320e21db1e20a989acf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal36665f0dc0e45320e21db1e20a989acf)): ?>
<?php $component = $__componentOriginal36665f0dc0e45320e21db1e20a989acf; ?>
<?php unset($__componentOriginal36665f0dc0e45320e21db1e20a989acf); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal36665f0dc0e45320e21db1e20a989acf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36665f0dc0e45320e21db1e20a989acf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.panel','data' => ['class' => 'md:col-span-2 space-y-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'md:col-span-2 space-y-3']); ?>
        <h3 class="fs-display text-sm font-semibold text-fs-primary">Featured image</h3>
        <?php if($featuredUrl !== ''): ?>
            <div class="flex items-start gap-3">
                <img src="<?php echo e($featuredUrl); ?>" alt="Current featured image" width="160" height="120" class="w-40 h-28 object-cover rounded-sm border border-black/10 bg-fs-surface">
                <p class="text-xs text-fs-muted">Current featured image. Upload a new file to replace it.</p>
            </div>
        <?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal22dd814e34b3292120e6fee9433ec671 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22dd814e34b3292120e6fee9433ec671 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.file-upload','data' => ['label' => 'Upload featured image','name' => 'featured_image','id' => 'featured_image','accept' => 'image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp','@change' => 'onFeaturedChange($event)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('file-upload'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Upload featured image','name' => 'featured_image','id' => 'featured_image','accept' => 'image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp','@change' => 'onFeaturedChange($event)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22dd814e34b3292120e6fee9433ec671)): ?>
<?php $attributes = $__attributesOriginal22dd814e34b3292120e6fee9433ec671; ?>
<?php unset($__attributesOriginal22dd814e34b3292120e6fee9433ec671); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22dd814e34b3292120e6fee9433ec671)): ?>
<?php $component = $__componentOriginal22dd814e34b3292120e6fee9433ec671; ?>
<?php unset($__componentOriginal22dd814e34b3292120e6fee9433ec671); ?>
<?php endif; ?>
        <template x-if="featuredPreview">
            <div class="flex items-start gap-3">
                <img :src="featuredPreview" alt="Preview of new featured image upload" width="160" height="120" class="w-40 h-28 object-cover rounded-sm border border-black/10 bg-fs-surface">
                <p class="text-xs text-fs-muted">New featured image preview</p>
            </div>
        </template>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal36665f0dc0e45320e21db1e20a989acf)): ?>
<?php $attributes = $__attributesOriginal36665f0dc0e45320e21db1e20a989acf; ?>
<?php unset($__attributesOriginal36665f0dc0e45320e21db1e20a989acf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal36665f0dc0e45320e21db1e20a989acf)): ?>
<?php $component = $__componentOriginal36665f0dc0e45320e21db1e20a989acf; ?>
<?php unset($__componentOriginal36665f0dc0e45320e21db1e20a989acf); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal36665f0dc0e45320e21db1e20a989acf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36665f0dc0e45320e21db1e20a989acf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.panel','data' => ['class' => 'md:col-span-2 space-y-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'md:col-span-2 space-y-3']); ?>
        <h3 class="fs-display text-sm font-semibold text-fs-primary">Gallery</h3>
        <?php if($listing && $listing->relationLoaded('images') && $listing->images->isNotEmpty()): ?>
            <?php
                $imageOrderConfig = ['order' => $listing->images->pluck('id')->values()->all()];
            ?>
            <div
                class="space-y-3"
                x-data="imageOrder"
                data-alpine-config="<?php echo e(json_encode($imageOrderConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)); ?>"
            >
                <div class="flex flex-wrap gap-3">
                    <?php $__currentLoopData = $listing->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $galleryUrl = Media::url($image->path); ?>
                        <div class="relative w-28 space-y-1">
                            <?php if($galleryUrl !== ''): ?>
                                <img src="<?php echo e($galleryUrl); ?>" alt="Gallery image <?php echo e($loop->iteration); ?>" width="112" height="84" class="w-28 h-[84px] object-cover rounded-sm border border-black/10 bg-fs-surface">
                            <?php endif; ?>
                            <div class="flex gap-1">
                                <button type="button" class="text-[10px] text-fs-brand underline" @click="moveUp(<?php echo e((int) $image->id); ?>)">Up</button>
                                <button type="button" class="text-[10px] text-fs-brand underline" @click="moveDown(<?php echo e((int) $image->id); ?>)">Down</button>
                            </div>
                            <?php if (isset($component)) { $__componentOriginal31d8dad53715b0a3883f430a1e78226c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31d8dad53715b0a3883f430a1e78226c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirm-delete','data' => ['action' => route('admin.listings.images.destroy', [$listing, $image]),'title' => 'Delete gallery image?','message' => 'This image will be removed from the listing.','confirmLabel' => 'Delete']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirm-delete'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.listings.images.destroy', [$listing, $image])),'title' => 'Delete gallery image?','message' => 'This image will be removed from the listing.','confirm-label' => 'Delete']); ?>
                                <button type="button" class="text-[10px] text-fs-danger underline">Delete</button>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31d8dad53715b0a3883f430a1e78226c)): ?>
<?php $attributes = $__attributesOriginal31d8dad53715b0a3883f430a1e78226c; ?>
<?php unset($__attributesOriginal31d8dad53715b0a3883f430a1e78226c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31d8dad53715b0a3883f430a1e78226c)): ?>
<?php $component = $__componentOriginal31d8dad53715b0a3883f430a1e78226c; ?>
<?php unset($__componentOriginal31d8dad53715b0a3883f430a1e78226c); ?>
<?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <form method="POST" action="<?php echo e(route('admin.listings.images.reorder', $listing)); ?>" class="inline">
                    <?php echo csrf_field(); ?>
                    <template x-for="id in order" :key="id">
                        <input type="hidden" name="order[]" :value="id">
                    </template>
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit','variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'ghost']); ?>Save gallery order <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                </form>
            </div>
        <?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal22dd814e34b3292120e6fee9433ec671 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22dd814e34b3292120e6fee9433ec671 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.file-upload','data' => ['label' => 'Add gallery images','name' => 'images[]','id' => 'images','accept' => 'image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp','multiple' => true,'@change' => 'onGalleryChange($event)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('file-upload'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Add gallery images','name' => 'images[]','id' => 'images','accept' => 'image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp','multiple' => true,'@change' => 'onGalleryChange($event)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22dd814e34b3292120e6fee9433ec671)): ?>
<?php $attributes = $__attributesOriginal22dd814e34b3292120e6fee9433ec671; ?>
<?php unset($__attributesOriginal22dd814e34b3292120e6fee9433ec671); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22dd814e34b3292120e6fee9433ec671)): ?>
<?php $component = $__componentOriginal22dd814e34b3292120e6fee9433ec671; ?>
<?php unset($__componentOriginal22dd814e34b3292120e6fee9433ec671); ?>
<?php endif; ?>
        <div class="flex flex-wrap gap-2" x-show="hasGalleryPreviews" x-cloak>
            <template x-for="(src, index) in galleryPreviews" :key="index">
                <img :src="src" :alt="galleryAlt(index)" width="96" height="72" class="w-24 h-[72px] object-cover rounded-sm border border-black/10 bg-fs-surface">
            </template>
        </div>
        <?php $__errorArgs = ['images.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-fs-danger"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal36665f0dc0e45320e21db1e20a989acf)): ?>
<?php $attributes = $__attributesOriginal36665f0dc0e45320e21db1e20a989acf; ?>
<?php unset($__attributesOriginal36665f0dc0e45320e21db1e20a989acf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal36665f0dc0e45320e21db1e20a989acf)): ?>
<?php $component = $__componentOriginal36665f0dc0e45320e21db1e20a989acf; ?>
<?php unset($__componentOriginal36665f0dc0e45320e21db1e20a989acf); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal36665f0dc0e45320e21db1e20a989acf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36665f0dc0e45320e21db1e20a989acf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.panel','data' => ['class' => 'md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4']); ?>
        <div>
            <h3 class="fs-display text-sm font-semibold text-fs-primary mb-3">Video</h3>
            <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['label' => 'Video URL','name' => 'video_url','type' => 'url','value' => old('video_url', $listing?->video_url),'placeholder' => 'https://...']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Video URL','name' => 'video_url','type' => 'url','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('video_url', $listing?->video_url)),'placeholder' => 'https://...']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $attributes = $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $component = $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
        </div>
        <div>
            <h3 class="fs-display text-sm font-semibold text-fs-primary mb-3">Floor plan</h3>
            <?php if($listing?->floor_plan_path): ?>
                <p class="mb-2 text-xs text-fs-muted">Current: <?php echo e($listing->floor_plan_path); ?></p>
            <?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal22dd814e34b3292120e6fee9433ec671 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22dd814e34b3292120e6fee9433ec671 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.file-upload','data' => ['label' => 'Upload floor plan','name' => 'floor_plan','id' => 'floor_plan','accept' => '.jpg,.jpeg,.png,.pdf']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('file-upload'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Upload floor plan','name' => 'floor_plan','id' => 'floor_plan','accept' => '.jpg,.jpeg,.png,.pdf']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22dd814e34b3292120e6fee9433ec671)): ?>
<?php $attributes = $__attributesOriginal22dd814e34b3292120e6fee9433ec671; ?>
<?php unset($__attributesOriginal22dd814e34b3292120e6fee9433ec671); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22dd814e34b3292120e6fee9433ec671)): ?>
<?php $component = $__componentOriginal22dd814e34b3292120e6fee9433ec671; ?>
<?php unset($__componentOriginal22dd814e34b3292120e6fee9433ec671); ?>
<?php endif; ?>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal36665f0dc0e45320e21db1e20a989acf)): ?>
<?php $attributes = $__attributesOriginal36665f0dc0e45320e21db1e20a989acf; ?>
<?php unset($__attributesOriginal36665f0dc0e45320e21db1e20a989acf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal36665f0dc0e45320e21db1e20a989acf)): ?>
<?php $component = $__componentOriginal36665f0dc0e45320e21db1e20a989acf; ?>
<?php unset($__componentOriginal36665f0dc0e45320e21db1e20a989acf); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal36665f0dc0e45320e21db1e20a989acf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36665f0dc0e45320e21db1e20a989acf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.panel','data' => ['class' => 'md:col-span-2 space-y-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'md:col-span-2 space-y-3']); ?>
        <h3 class="fs-display text-sm font-semibold text-fs-primary">Private document (title deed / NOC)</h3>
        <?php if($listing?->private_document_path): ?>
            <p class="text-xs text-fs-muted">Stored on private disk</p>
        <?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal22dd814e34b3292120e6fee9433ec671 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22dd814e34b3292120e6fee9433ec671 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.file-upload','data' => ['label' => 'Upload private document','name' => 'private_document','id' => 'private_document','accept' => '.pdf,.doc,.docx,.jpg,.jpeg,.png']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('file-upload'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Upload private document','name' => 'private_document','id' => 'private_document','accept' => '.pdf,.doc,.docx,.jpg,.jpeg,.png']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22dd814e34b3292120e6fee9433ec671)): ?>
<?php $attributes = $__attributesOriginal22dd814e34b3292120e6fee9433ec671; ?>
<?php unset($__attributesOriginal22dd814e34b3292120e6fee9433ec671); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22dd814e34b3292120e6fee9433ec671)): ?>
<?php $component = $__componentOriginal22dd814e34b3292120e6fee9433ec671; ?>
<?php unset($__componentOriginal22dd814e34b3292120e6fee9433ec671); ?>
<?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal36665f0dc0e45320e21db1e20a989acf)): ?>
<?php $attributes = $__attributesOriginal36665f0dc0e45320e21db1e20a989acf; ?>
<?php unset($__attributesOriginal36665f0dc0e45320e21db1e20a989acf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal36665f0dc0e45320e21db1e20a989acf)): ?>
<?php $component = $__componentOriginal36665f0dc0e45320e21db1e20a989acf; ?>
<?php unset($__componentOriginal36665f0dc0e45320e21db1e20a989acf); ?>
<?php endif; ?>
</div>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/admin/listings/_form.blade.php ENDPATH**/ ?>