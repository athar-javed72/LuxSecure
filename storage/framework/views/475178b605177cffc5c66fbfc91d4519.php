<?php
    // TODO owner: replace personal Gmail with a business address in Settings (site.email).
    $contactEmail = $siteEmail ?? null;
    $isPersonalEmail = $contactEmail && str_ends_with(strtolower($contactEmail), '@gmail.com');
?>

<footer class="mt-0 bg-[var(--color-forest-shadow)] text-[var(--color-ivory)] border-t border-[color-mix(in_srgb,var(--color-gold)_40%,transparent)]">
    <?php if (isset($component)) { $__componentOriginala766c2d312d6f7864fe218e2500d2bba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala766c2d312d6f7864fe218e2500d2bba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.container','data' => ['class' => 'py-12 md:py-14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'py-12 md:py-14']); ?>
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-x-10">
            <div>
                <a href="<?php echo e(route('home')); ?>" class="inline-flex flex-col items-start rounded-[var(--radius-button)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-gold)]">
                    <?php if (isset($component)) { $__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.brand-logo','data' => ['layout' => 'stacked','surface' => 'dark','width' => 180,'class' => '[&_img]:!h-[68px] [&_img]:!min-w-[170px] [&_img]:!max-w-[190px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('brand-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['layout' => 'stacked','surface' => 'dark','width' => 180,'class' => '[&_img]:!h-[68px] [&_img]:!min-w-[170px] [&_img]:!max-w-[190px]']); ?>
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
                </a>
                <p class="mt-5 max-w-xs font-[family-name:var(--font-display)] text-[20px] italic font-normal leading-snug text-[var(--color-ivory)]">
                    <?php echo e(\App\Support\BrandAssets::TAGLINE); ?>

                </p>
            </div>

            <div>
                <h2 class="font-[family-name:var(--font-body)] text-xs font-semibold uppercase tracking-[0.16em] text-[var(--color-sage)]">Explore</h2>
                <ul class="mt-4 space-y-0 text-[15px]">
                    <?php $__currentLoopData = [
                        ['Properties', route('properties')],
                        ['Hotels', route('divisions.hospitality')],
                        ['Living', route('divisions.living')],
                        ['About', route('home').'#about'],
                        ['Contact', route('contact')],
                        ['FAQ', route('home').'#faq'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $href]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><a href="<?php echo e($href); ?>" class="inline-flex min-h-9 items-center text-[var(--color-ivory)] hover:underline"><?php echo e($label); ?></a></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>

            <div>
                <h2 class="font-[family-name:var(--font-body)] text-xs font-semibold uppercase tracking-[0.16em] text-[var(--color-sage)]">Account</h2>
                <ul class="mt-4 space-y-0 text-[15px]">
                    <?php if(auth()->guard()->guest()): ?>
                        <li><a href="<?php echo e(route('login.form')); ?>" class="inline-flex min-h-9 items-center text-[var(--color-ivory)] hover:underline">Login</a></li>
                        <li><a href="<?php echo e(route('register.form')); ?>" class="inline-flex min-h-9 items-center text-[var(--color-ivory)] hover:underline">Register</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo e(route('profile')); ?>" class="inline-flex min-h-9 items-center text-[var(--color-ivory)] hover:underline">My Profile</a></li>
                        <li><a href="<?php echo e(route('profile')); ?>#favorites" class="inline-flex min-h-9 items-center text-[var(--color-ivory)] hover:underline">Saved Properties</a></li>
                        <li>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="inline-flex min-h-9 items-center text-[var(--color-ivory)] hover:underline">Logout</button>
                            </form>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <div>
                <h2 class="font-[family-name:var(--font-body)] text-xs font-semibold uppercase tracking-[0.16em] text-[var(--color-sage)]">Contact</h2>
                <ul class="mt-4 space-y-3 text-[15px] text-[var(--color-sage)]">
                    <?php if(!empty($contactEmail)): ?>
                        <li>
                            <span class="block font-[family-name:var(--font-body)] text-xs uppercase tracking-[0.14em] text-[var(--color-sage)] mb-1">Email</span>
                            <a href="mailto:<?php echo e($contactEmail); ?>" class="text-[var(--color-ivory)] hover:underline"><?php echo e($contactEmail); ?></a>
                            <?php if($isPersonalEmail): ?>
                                
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                    <?php if(!empty($sitePhone)): ?>
                        <li>
                            <span class="block font-[family-name:var(--font-body)] text-xs uppercase tracking-[0.14em] text-[var(--color-sage)] mb-1">Phone</span>
                            <a href="tel:<?php echo e(preg_replace('/\s+/', '', $sitePhone)); ?>" class="text-[var(--color-ivory)] hover:underline"><?php echo e($sitePhone); ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if(!empty($siteHours)): ?>
                        <li>
                            <span class="block font-[family-name:var(--font-body)] text-xs uppercase tracking-[0.14em] text-[var(--color-sage)] mb-1">Hours</span>
                            <span class="text-[var(--color-ivory)]"><?php echo e($siteHours); ?></span>
                        </li>
                    <?php endif; ?>
                    <?php if(empty($contactEmail) && empty($sitePhone) && empty($siteHours)): ?>
                        <li class="text-[var(--color-sage)]"><?php echo e(__('Use the contact form to reach us.')); ?></li>
                    <?php endif; ?>
                </ul>
                <div class="mt-5">
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','href' => ''.e(route('contact')).'','surface' => 'dark','class' => 'fs-btn-motion']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','href' => ''.e(route('contact')).'','surface' => 'dark','class' => 'fs-btn-motion']); ?>Send a message <?php echo $__env->renderComponent(); ?>
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
        </div>

        <div class="mt-10 flex flex-col gap-3 border-t border-white/10 pt-5 text-xs text-[var(--color-sage)] sm:flex-row sm:items-center sm:justify-between">
            <p>© <?php echo e(date('Y')); ?> <?php echo e($siteLegalName ?? $siteName ?? config('app.name')); ?>. All rights reserved.</p>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                <?php if($legalPrivacy ?? null): ?>
                    <a href="<?php echo e(route('pages.show', $legalPrivacy->slug)); ?>" class="hover:text-[var(--color-ivory)] hover:underline">Privacy</a>
                <?php else: ?>
                    <a href="<?php echo e(url('/pages/privacy-policy')); ?>" class="hover:text-[var(--color-ivory)] hover:underline">Privacy</a>
                <?php endif; ?>
                <?php if($legalTerms ?? null): ?>
                    <a href="<?php echo e(route('pages.show', $legalTerms->slug)); ?>" class="hover:text-[var(--color-ivory)] hover:underline">Terms</a>
                <?php else: ?>
                    <a href="<?php echo e(url('/pages/terms-of-service')); ?>" class="hover:text-[var(--color-ivory)] hover:underline">Terms</a>
                <?php endif; ?>
                <?php echo $__env->make('include.branding', ['brandingClass' => 'text-[var(--color-sage)]', 'strongClass' => 'text-[var(--color-ivory)] font-medium'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
</footer>
<?php /**PATH D:\Nexora Lab\MaskanoorGroup\resources\views/include/footer.blade.php ENDPATH**/ ?>