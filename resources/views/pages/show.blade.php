@extends('include.master')

@section('title', $page->meta_title ?: $page->title)
@section('meta_description', $page->meta_description ?: Str::limit(strip_tags($page->body ?? ''), 150))

@section('content')
<x-container class="py-10 max-w-3xl">
    <x-page-header :title="$page->title" />
    <x-panel>
        <div class="text-sm leading-relaxed text-fs-secondary whitespace-pre-wrap">{{ $page->body }}</div>
    </x-panel>
</x-container>
@endsection
