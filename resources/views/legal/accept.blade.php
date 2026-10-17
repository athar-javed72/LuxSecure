@extends('include.master')

@section('title', 'Accept updated terms')

@section('content')
<x-container class="py-10 max-w-xl">
    <x-page-header
        title="Updated legal terms"
        subtitle="A new version of our Terms or Privacy Policy is in effect. Please review and accept to continue."
    />

    <x-panel>
        <form method="POST" action="{{ route('legal.accept.store') }}" class="space-y-4">
            @csrf
            <x-legal-acceptances :terms="$terms" :privacy="$privacy" />
            <x-button type="submit" variant="primary" class="w-full">Accept and continue</x-button>
        </form>
    </x-panel>
</x-container>
@endsection
