<footer class="mt-0 border-t border-white/10 bg-[#142A25] text-white">
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
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-x-10 lg:gap-y-0">
            <div>
                <a href="<?php echo e(route('home')); ?>" class="inline-flex flex-col items-start gap-2 rounded-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                    <?php if (isset($component)) { $__componentOriginal8741a05e11b0c77d19ec61b6b35b26b3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8741a05e11b0c77d19ec61b6b35b26b3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.brand-logo','data' => ['layout' => 'stacked','surface' => 'dark','class' => '[&_img]:h-16 [&_img]:min-w-[140px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('brand-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['layout' => 'stacked','surface' => 'dark','class' => '[&_img]:h-16 [&_img]:min-w-[140px]']); ?>
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
                <p class="mt-4 max-w-xs fs-display text-base italic leading-relaxed text-[#D6B779]">
                    <?php echo e(\App\Support\BrandAssets::TAGLINE); ?>

                </p>
                <?php if(!empty($siteTagline) && $siteTagline !== \App\Support\BrandAssets::TAGLINE): ?>
                    <p class="mt-2 max-w-xs text-sm leading-relaxed text-slate-300"><?php echo e($siteTagline); ?></p>
                <?php endif; ?>
            </div>

            <div>
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Explore</h2>
                <ul class="mt-4 space-y-1 text-sm">
                    <li>
                        <a href="<?php echo e(route('properties')); ?>" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                            Properties
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('home')); ?>#about" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                            Why <?php echo e($siteName ?? config('app.name')); ?>

                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('contact')); ?>" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                            Contact
                        </a>
                    </li>
                    <?php if($legalPrivacy ?? null): ?>
                        <li>
                            <a href="<?php echo e(route('pages.show', $legalPrivacy->slug)); ?>" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                                <?php echo e($legalPrivacy->title); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if($legalTerms ?? null): ?>
                        <li>
                            <a href="<?php echo e(route('pages.show', $legalTerms->slug)); ?>" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                                <?php echo e($legalTerms->title); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if($legalRefund ?? null): ?>
                        <li>
                            <a href="<?php echo e(route('pages.show', $legalRefund->slug)); ?>" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                                <?php echo e($legalRefund->title); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <div>
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Account</h2>
                <ul class="mt-4 space-y-1 text-sm">
                    <?php if(auth()->guard()->guest()): ?>
                        <li>
                            <a href="<?php echo e(route('login.form')); ?>" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                                Login
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('register.form')); ?>" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                                Register
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="<?php echo e(route('profile')); ?>" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                                My Profile
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('profile')); ?>#favorites" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                                Saved Properties
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                                    Logout
                                </button>
                            </form>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <div>
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Contact</h2>
                <ul class="mt-4 space-y-2.5 text-sm text-slate-300">
                    <?php if(!empty($siteEmail)): ?>
                        <li>
                            <a href="mailto:<?php echo e($siteEmail); ?>" class="transition-colors duration-150 hover:text-white"><?php echo e($siteEmail); ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if(!empty($sitePhone)): ?>
                        <li>
                            <a href="tel:<?php echo e(preg_replace('/\s+/', '', $sitePhone)); ?>" class="transition-colors duration-150 hover:text-white"><?php echo e($sitePhone); ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if(!empty($siteHours)): ?>
                        <li class="text-slate-400"><?php echo e($siteHours); ?></li>
                    <?php endif; ?>
                    <?php if(!empty($siteAddress)): ?>
                        <li class="text-slate-400"><?php echo e($siteAddress); ?></li>
                    <?php endif; ?>
                    <?php if(empty($siteEmail) && empty($sitePhone) && empty($siteHours) && empty($siteAddress)): ?>
                        <li class="text-slate-500"><?php echo e(__('Use the contact form to reach us.')); ?></li>
                    <?php endif; ?>
                </ul>
                <div class="mt-5">
                    <a
                        href="<?php echo e(route('contact')); ?>"
                        class="inline-flex h-10 items-center justify-center rounded-sm bg-fs-brand px-4 text-sm font-semibold text-white transition-colors duration-150 hover:bg-fs-brand-dark focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                    >
                        Send a message
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-10 flex flex-col gap-2 border-t border-white/10 pt-5 text-xs text-slate-400 sm:flex-row sm:items-center sm:justify-between">
            <p>© <?php echo e(date('Y')); ?> <?php echo e($siteLegalName ?? $siteName ?? config('app.name')); ?>. All rights reserved.</p>
            <?php echo $__env->make('include.branding', ['brandingClass' => 'text-slate-400', 'strongClass' => 'text-slate-200 font-medium'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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