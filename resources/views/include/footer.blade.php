@php
    // TODO owner: replace personal Gmail with a business address in Settings (site.email).
    $contactEmail = $siteEmail ?? null;
    $isPersonalEmail = $contactEmail && str_ends_with(strtolower($contactEmail), '@gmail.com');
@endphp

<footer class="mt-0 bg-[var(--color-forest-shadow)] text-[var(--color-ivory)] border-t border-[color-mix(in_srgb,var(--color-gold)_40%,transparent)]">
    <x-container class="py-12 md:py-14">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-x-10">
            <div>
                <a href="{{ route('home') }}" class="inline-flex flex-col items-start rounded-[var(--radius-button)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-gold)]">
                    <x-brand-logo layout="stacked" surface="dark" :width="180" class="[&_img]:!h-[68px] [&_img]:!min-w-[170px] [&_img]:!max-w-[190px]" />
                </a>
                <p class="mt-5 max-w-xs font-[family-name:var(--font-display)] text-[20px] italic font-normal leading-snug text-[var(--color-ivory)]">
                    {{ \App\Support\BrandAssets::TAGLINE }}
                </p>
            </div>

            <div>
                <h2 class="font-[family-name:var(--font-body)] text-xs font-semibold uppercase tracking-[0.16em] text-[var(--color-sage)]">Explore</h2>
                <ul class="mt-4 space-y-0 text-[15px]">
                    @foreach([
                        ['Properties', route('properties')],
                        ['Hotels', route('divisions.hospitality')],
                        ['Living', route('divisions.living')],
                        ['About', route('home').'#about'],
                        ['Contact', route('contact')],
                        ['FAQ', route('home').'#faq'],
                    ] as [$label, $href])
                        <li><a href="{{ $href }}" class="inline-flex min-h-9 items-center text-[var(--color-ivory)] hover:underline">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h2 class="font-[family-name:var(--font-body)] text-xs font-semibold uppercase tracking-[0.16em] text-[var(--color-sage)]">Account</h2>
                <ul class="mt-4 space-y-0 text-[15px]">
                    @guest
                        <li><a href="{{ route('login.form') }}" class="inline-flex min-h-9 items-center text-[var(--color-ivory)] hover:underline">Login</a></li>
                        <li><a href="{{ route('register.form') }}" class="inline-flex min-h-9 items-center text-[var(--color-ivory)] hover:underline">Register</a></li>
                    @else
                        <li><a href="{{ route('profile') }}" class="inline-flex min-h-9 items-center text-[var(--color-ivory)] hover:underline">My Profile</a></li>
                        <li><a href="{{ route('profile') }}#favorites" class="inline-flex min-h-9 items-center text-[var(--color-ivory)] hover:underline">Saved Properties</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="inline-flex min-h-9 items-center text-[var(--color-ivory)] hover:underline">Logout</button>
                            </form>
                        </li>
                    @endguest
                </ul>
            </div>

            <div>
                <h2 class="font-[family-name:var(--font-body)] text-xs font-semibold uppercase tracking-[0.16em] text-[var(--color-sage)]">Contact</h2>
                <ul class="mt-4 space-y-3 text-[15px] text-[var(--color-sage)]">
                    @if(!empty($contactEmail))
                        <li>
                            <span class="block font-[family-name:var(--font-body)] text-xs uppercase tracking-[0.14em] text-[var(--color-sage)] mb-1">Email</span>
                            <a href="mailto:{{ $contactEmail }}" class="text-[var(--color-ivory)] hover:underline">{{ $contactEmail }}</a>
                            @if($isPersonalEmail)
                                {{-- TODO owner: personal Gmail — set a business address in Settings --}}
                            @endif
                        </li>
                    @endif
                    @if(!empty($sitePhone))
                        <li>
                            <span class="block font-[family-name:var(--font-body)] text-xs uppercase tracking-[0.14em] text-[var(--color-sage)] mb-1">Phone</span>
                            <a href="tel:{{ preg_replace('/\s+/', '', $sitePhone) }}" class="text-[var(--color-ivory)] hover:underline">{{ $sitePhone }}</a>
                        </li>
                    @endif
                    @if(!empty($siteHours))
                        <li>
                            <span class="block font-[family-name:var(--font-body)] text-xs uppercase tracking-[0.14em] text-[var(--color-sage)] mb-1">Hours</span>
                            <span class="text-[var(--color-ivory)]">{{ $siteHours }}</span>
                        </li>
                    @endif
                    @if(empty($contactEmail) && empty($sitePhone) && empty($siteHours))
                        <li class="text-[var(--color-sage)]">{{ __('Use the contact form to reach us.') }}</li>
                    @endif
                </ul>
                <div class="mt-5">
                    <x-button variant="primary" href="{{ route('contact') }}" surface="dark" class="fs-btn-motion">Send a message</x-button>
                </div>
            </div>
        </div>

        <div class="mt-10 flex flex-col gap-3 border-t border-white/10 pt-5 text-xs text-[var(--color-sage)] sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ date('Y') }} {{ $siteLegalName ?? $siteName ?? config('app.name') }}. All rights reserved.</p>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                @if($legalPrivacy ?? null)
                    <a href="{{ route('pages.show', $legalPrivacy->slug) }}" class="hover:text-[var(--color-ivory)] hover:underline">Privacy</a>
                @else
                    <a href="{{ url('/pages/privacy-policy') }}" class="hover:text-[var(--color-ivory)] hover:underline">Privacy</a>
                @endif
                @if($legalTerms ?? null)
                    <a href="{{ route('pages.show', $legalTerms->slug) }}" class="hover:text-[var(--color-ivory)] hover:underline">Terms</a>
                @else
                    <a href="{{ url('/pages/terms-of-service') }}" class="hover:text-[var(--color-ivory)] hover:underline">Terms</a>
                @endif
                @include('include.branding', ['brandingClass' => 'text-[var(--color-sage)]', 'strongClass' => 'text-[var(--color-ivory)] font-medium'])
            </div>
        </div>
    </x-container>
</footer>
