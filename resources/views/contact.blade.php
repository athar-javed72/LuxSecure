@extends('include.master')

@section('title', 'Contact')
@section('meta_description', 'Contact LuxSecure for property inquiries, listing help, or support.')

@section('content')
<x-container class="py-12">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Contact us</h1>
        <p class="mt-2 text-sm text-fs-secondary">Send a message and our team will respond shortly.</p>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="rounded-md bg-white shadow-sm border border-slate-100 p-6">
                @if (session('success'))
                    <div class="mb-4 p-4 rounded-sm bg-fs-brand-light text-fs-brand text-sm">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 p-4 rounded-sm bg-red-50 text-fs-danger text-sm">
                        <ul class="space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="space-y-4">
                    @csrf
                    <x-input label="Name" name="name" :value="old('name')" placeholder="Your name" required />
                    <x-input label="Email" name="email" type="email" :value="old('email')" placeholder="you@example.com" required />
                    <div>
                        <label for="message" class="block text-xs font-semibold text-fs-primary mb-1">Message</label>
                        <textarea id="message" name="message" rows="5" required placeholder="How can we help?"
                                  class="w-full px-4 py-3 rounded-sm border border-slate-200 bg-white text-sm text-fs-primary placeholder:text-fs-muted focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20">{{ old('message') }}</textarea>
                        @error('message')<p class="mt-1 text-xs text-fs-danger">{{ $message }}</p>@enderror
                    </div>
                    <x-button type="submit" variant="primary" class="w-full">Send message</x-button>
                </form>
            </div>

            <div class="rounded-md bg-slate-50 border border-slate-100 p-6 space-y-4">
                <h2 class="text-base font-semibold text-fs-primary">Get in touch</h2>
                <p class="text-sm text-fs-secondary">123 Estate Avenue, City, Pakistan</p>
                <p class="text-sm text-fs-secondary">+92 300 0000000</p>
                <p class="text-sm text-fs-secondary">support@LuxSecure.pk</p>
                <p class="text-xs text-fs-muted">Mon–Sat, 10:00 AM – 7:00 PM (PKT)</p>
            </div>
        </div>
    </div>
</x-container>
@endsection
