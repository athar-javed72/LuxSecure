@extends('include.master')

@section('header_mode', 'light')
@section('title', 'Contact')
@section('meta_description', 'Contact '.($siteName ?? config('app.name')).' for property inquiries, listing help, or support.')

@section('content')
<x-container class="py-8 md:py-12">
    <div class="max-w-3xl mx-auto">
        <x-page-header
            title="Contact us"
            subtitle="Send a message and our team will respond shortly."
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
            <x-panel>
                <form id="inquiry-form" method="POST" action="{{ route('contact.store') }}" class="space-y-4 scroll-mt-[calc(var(--header-h)+16px)]">
                    @csrf
                    <x-input label="Name" name="name" :value="old('name')" placeholder="Your name" required />
                    <x-input label="Email" name="email" type="email" :value="old('email')" placeholder="you@example.com" required />
                    <div>
                        <label for="message" class="block text-xs font-semibold text-fs-primary mb-1">Message</label>
                        <textarea
                            id="message"
                            name="message"
                            @if(request('type') === 'inquiry') autofocus @endif
                            rows="5"
                            required
                            placeholder="How can we help?"
                            class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary placeholder:text-fs-muted focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20"
                        >{{ old('message') }}</textarea>
                        @error('message')<p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>@enderror
                    </div>
                    <x-button type="submit" variant="primary" class="w-full">Send message</x-button>
                </form>
            </x-panel>

            <x-panel class="bg-fs-surface/60 space-y-4">
                <h2 class="fs-display text-lg text-fs-primary">Get in touch</h2>
                @if(!empty($siteAddress))
                    <p class="text-sm text-fs-secondary">{{ $siteAddress }}</p>
                @endif
                @if(!empty($sitePhone))
                    <p class="text-sm text-fs-secondary">
                        <a href="tel:{{ preg_replace('/\s+/', '', $sitePhone) }}" class="hover:text-fs-brand transition-colors duration-150">{{ $sitePhone }}</a>
                    </p>
                @endif
                @if(!empty($siteEmail))
                    <p class="text-sm text-fs-secondary">
                        <a href="mailto:{{ $siteEmail }}" class="hover:text-fs-brand transition-colors duration-150">{{ $siteEmail }}</a>
                    </p>
                @endif
                @if(!empty($siteHours))
                    <p class="text-xs text-fs-muted">{{ $siteHours }}</p>
                @endif
                @if(empty($siteAddress) && empty($sitePhone) && empty($siteEmail) && empty($siteHours))
                    <p class="text-sm text-fs-muted">{{ __('Use the contact form to reach us.') }}</p>
                @endif
            </x-panel>
        </div>
    </div>
</x-container>
@endsection
