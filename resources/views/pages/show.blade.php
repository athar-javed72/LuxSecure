@extends('include.master')

@section('title', $page->meta_title ?: $page->title)
@section('meta_description', $page->meta_description ?: Str::limit(strip_tags($page->body ?? ''), 150))

@section('content')
<x-container class="py-10 max-w-3xl">
    <h1 class="text-3xl font-bold text-fs-primary">{{ $page->title }}</h1>
    <div class="mt-6 prose prose-sm max-w-none text-fs-secondary whitespace-pre-wrap">{{ $page->body }}</div>
</x-container>
@endsection
