@extends('include.master')

@section('title', $post->meta_title ?: $post->title)
@section('meta_description', $post->meta_description ?: $post->excerpt)

@section('content')
<x-container class="py-10 max-w-3xl">
    <p class="text-xs uppercase text-fs-muted">{{ $post->category }}</p>
    <h1 class="text-3xl font-bold mt-2">{{ $post->title }}</h1>
    <p class="text-sm text-fs-secondary mt-2">{{ optional($post->published_at)->toFormattedDateString() }}</p>
    <div class="mt-6 whitespace-pre-wrap text-fs-secondary">{{ $post->body }}</div>
</x-container>
@endsection
