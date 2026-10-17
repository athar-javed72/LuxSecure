<footer class="mt-0 border-t border-white/10 bg-fs-primary text-white">
    <x-container class="py-12 md:py-14">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-x-10 lg:gap-y-0">
            {{-- Brand --}}
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 rounded-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-sm bg-fs-brand text-sm font-bold text-white tracking-wide">LS</span>
                    <span class="fs-display text-xl font-semibold tracking-wide">
                        {{ $siteName ?? 'LuxSecure' }}
                    </span>
                </a>
                <p class="mt-4 max-w-xs text-sm leading-relaxed text-slate-300">
                    Secure, modern real estate across Pakistan’s most trusted communities.
                </p>
            </div>

            {{-- Explore — useful site links only (no redundant Home) --}}
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
                            Why LuxSecure
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                            Contact
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.show', 'privacy-policy') }}" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                            Privacy Policy
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.show', 'terms-of-service') }}" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                            Terms of Service
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.show', 'refund-and-cancellation-policy') }}" class="inline-flex min-h-10 items-center text-slate-200 transition-colors duration-150 hover:text-white">
                            Refund &amp; Cancellation
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Account — guest vs signed-in --}}
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

            {{-- Contact — real info (no fake newsletter) --}}
            <div>
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Contact</h2>
                <ul class="mt-4 space-y-2.5 text-sm text-slate-300">
                    <li>
                        <a href="mailto:{{ $siteEmail ?? '[PLACEHOLDER: CONTACT_EMAIL]' }}" class="transition-colors duration-150 hover:text-white">
                            {{ $siteEmail ?? '[PLACEHOLDER: CONTACT_EMAIL]' }}
                        </a>
                    </li>
                    <li>
                        @php
                            $phoneDisplay = $sitePhone ?? '[PLACEHOLDER: CONTACT_PHONE]';
                        @endphp
                        @if(str_starts_with($phoneDisplay, '[PLACEHOLDER'))
                            <span class="text-slate-400">{{ $phoneDisplay }}</span>
                        @else
                            <a href="tel:{{ preg_replace('/\s+/', '', $phoneDisplay) }}" class="transition-colors duration-150 hover:text-white">
                                {{ $phoneDisplay }}
                            </a>
                        @endif
                    </li>
                    <li class="text-slate-400">{{ $siteHours ?? '[PLACEHOLDER: BUSINESS_HOURS]' }}</li>
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
            <p>© {{ date('Y') }} {{ $siteName ?? 'LuxSecure' }}. All rights reserved.</p>
            @include('include.branding', ['brandingClass' => 'text-slate-400', 'strongClass' => 'text-slate-200 font-medium'])
        </div>
    </x-container>
</footer>
