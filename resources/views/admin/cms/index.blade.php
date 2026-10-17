@extends('admin.layout')

@section('title', 'CMS')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Content'],
]" />

<x-page-header title="Content" subtitle="Manage site pages, blog, FAQs, and testimonials" />

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <x-stat-card label="Blog posts" :value="$counts['posts']" :href="route('admin.cms.posts.index')" />
    <x-stat-card label="FAQs" :value="$counts['faqs']" :href="route('admin.cms.faqs.index')" />
    <x-stat-card label="Testimonials" :value="$counts['testimonials']" :href="route('admin.cms.testimonials.index')" />
    <x-stat-card label="Pages" :value="$counts['pages']" :href="route('admin.cms.pages.index')" />
</div>
@endsection
