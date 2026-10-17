@extends('admin.layout')

@section('title', 'CMS')

@section('content')
<h1 class="text-xl font-bold mb-6">Content</h1>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <a href="{{ route('admin.cms.posts.index') }}" class="rounded-md bg-white border border-black/5 p-5 hover:border-fs-brand">
        <p class="text-xs uppercase text-fs-muted">Blog posts</p>
        <p class="text-2xl font-bold mt-2">{{ $counts['posts'] }}</p>
    </a>
    <a href="{{ route('admin.cms.faqs.index') }}" class="rounded-md bg-white border border-black/5 p-5 hover:border-fs-brand">
        <p class="text-xs uppercase text-fs-muted">FAQs</p>
        <p class="text-2xl font-bold mt-2">{{ $counts['faqs'] }}</p>
    </a>
    <a href="{{ route('admin.cms.testimonials.index') }}" class="rounded-md bg-white border border-black/5 p-5 hover:border-fs-brand">
        <p class="text-xs uppercase text-fs-muted">Testimonials</p>
        <p class="text-2xl font-bold mt-2">{{ $counts['testimonials'] }}</p>
    </a>
    <a href="{{ route('admin.cms.pages.index') }}" class="rounded-md bg-white border border-black/5 p-5 hover:border-fs-brand">
        <p class="text-xs uppercase text-fs-muted">Pages</p>
        <p class="text-2xl font-bold mt-2">{{ $counts['pages'] }}</p>
    </a>
</div>
@endsection
