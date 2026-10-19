<footer class="mt-0 border-t border-white/10 bg-[#142A25] text-white">
    <x-container class="py-12 md:py-14">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-x-10 lg:gap-y-0">
            <div>
                <a href="{{ route('home') }}" class="inline-flex flex-col items-start gap-2 rounded-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                    <x-brand-logo layout="stacked" surface="dark" class="[&_img]:h-16 [&_img]:min-w-[140px]" />
                </a>
                <p class="mt-4 max-w-xs fs-display text-base italic leading-relaxed text-[#D6B779]">
                    {{ \App\Support\BrandAssets::TAGLINE }}
                </p>
                @if(!empty($siteTagline) && $siteTagline !== \App\Support\BrandAssets::TAGLINE)
                    <p class="mt-2 max-w-xs text-sm leading-relaxed text-slate-300">{{ $siteTagline }}</p>
                @endif
            </div>

            <div>
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Explore</h2>
                <ul class="mt-4 space-y-1 text-sm">
                    <li>
                        <a href="{{ route('properties') }}" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                            Properties
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}#about" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                            Why {{ $siteName ?? config('app.name') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                            Contact
                        </a>
                    </li>
                    @if($legalPrivacy ?? null)
                        <li>
                            <a href="{{ route('pages.show', $legalPrivacy->slug) }}" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                                {{ $legalPrivacy->title }}
                            </a>
                        </li>
                    @endif
                    @if($legalTerms ?? null)
                        <li>
                            <a href="{{ route('pages.show', $legalTerms->slug) }}" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                                {{ $legalTerms->title }}
                            </a>
                        </li>
                    @endif
                    @if($legalRefund ?? null)
                        <li>
                            <a href="{{ route('pages.show', $legalRefund->slug) }}" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                                {{ $legalRefund->title }}
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <div>
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Account</h2>
                <ul class="mt-4 space-y-1 text-sm">
                    @guest
                        <li>
                            <a href="{{ route('login.form') }}" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                                Login
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('register.form') }}" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                                Register
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('profile') }}" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                                My Profile
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile') }}#favorites" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                                Saved Properties
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                                    Logout
                                </button>
                            </form>
                        </li>
                    @endguest
                </ul>
            </div>

            <div>
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Contact</h2>
                <ul class="mt-4 space-y-2.5 text-sm text-slate-300">
                    @if(!empty($siteEmail))
                        <li>
                            <a href="mailto:{{ $siteEmail }}" class="transition-colors duration-150 hover:text-white">{{ $siteEmail }}</a>
                        </li>
                    @endif
                    @if(!empty($sitePhone))
                        <li>
                            <a href="tel:{{ preg_replace('/\s+/', '', $sitePhone) }}" class="transition-colors duration-150 hover:text-white">{{ $sitePhone }}</a>
                        </li>
                    @endif
                    @if(!empty($siteHours))
                        <li class="text-slate-400">{{ $siteHours }}</li>
                    @endif
                    @if(!empty($siteAddress))
                        <li class="text-slate-400">{{ $siteAddress }}</li>
                    @endif
                    @if(empty($siteEmail) && empty($sitePhone) && empty($siteHours) && empty($siteAddress))
                        <li class="text-slate-500">{{ __('Use the contact form to reach us.') }}</li>
                    @endif
                </ul>
                <div class="mt-5">
                    <a
                        href="{{ route('contact') }}"
                        class="inline-flex h-10 items-center justify-center rounded-sm bg-fs-brand px-4 text-sm font-semibold text-white transition-colors duration-150 hover:bg-fs-brand-dark focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                    >
                        Send a message
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-10 flex flex-col gap-2 border-t border-white/10 pt-5 text-xs text-slate-400 sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ date('Y') }} {{ $siteLegalName ?? $siteName ?? config('app.name') }}. All rights reserved.</p>
            @include('include.branding', ['brandingClass' => 'text-slate-400', 'strongClass' => 'text-slate-200 font-medium'])
        </div>
    </x-container>
</footer>
