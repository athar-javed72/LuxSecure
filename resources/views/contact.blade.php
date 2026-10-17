@extends('include.master')

@section('title', 'Contact')
@section('meta_description', 'Contact LuxSecure for property inquiries, listing help, or support.')

@section('content')
<x-container class="py-8 md:py-12">
    <div class="max-w-3xl mx-auto">
        <x-page-header
            title="Contact us"
            subtitle="Send a message and our team will respond shortly."
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
            <x-panel>
                @if ($errors->any())
                    <x-alert tone="error" class="mb-4">
                        <ul class="space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </x-alert>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="space-y-4">
                    @csrf
                    <x-input label="Name" name="name" :value="old('name')" placeholder="Your name" required />
                    <x-input label="Email" name="email" type="email" :value="old('email')" placeholder="you@example.com" required />
                    <div>
                        <label for="message" class="block text-xs font-semibold text-fs-primary mb-1">Message</label>
                        <textarea
                            id="message"
                            name="message"
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
                <p class="text-sm text-fs-secondary">{{ $siteAddress ?? 'Islamabad · Lahore · Karachi' }}</p>
                <p class="text-sm text-fs-secondary">
                    <a href="tel:{{ preg_replace('/\s+/', '', $sitePhone ?? '+923000000000') }}" class="hover:text-fs-brand transition-colors duration-150">
                        {{ $sitePhone ?? '+92 300 0000000' }}
                    </a>
                </p>
                <p class="text-sm text-fs-secondary">
                    <a href="mailto:{{ $siteEmail ?? 'hello@luxsecure.example' }}" class="hover:text-fs-brand transition-colors duration-150">
                        {{ $siteEmail ?? 'hello@luxsecure.example' }}
                    </a>
                </p>
                <p class="text-xs text-fs-muted">Mon–Sat, 10:00 AM – 7:00 PM (PKT)</p>
            </x-panel>
        </div>
    </div>
</x-container>
@endsection
