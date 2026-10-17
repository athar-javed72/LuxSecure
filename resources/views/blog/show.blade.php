@extends('include.master')

@section('title', $post->meta_title ?: $post->title)
@section('meta_description', $post->meta_description ?: $post->excerpt)

@section('content')
<x-container class="py-10 max-w-3xl">
    <x-breadcrumbs :items="[
        ['label' => 'Blog', 'href' => route('blog.index')],
        ['label' => $post->title],
    ]" />

    @if(filled($post->category))
        <p class="text-xs font-semibold uppercase tracking-wide text-fs-muted">{{ $post->category }}</p>
    @endif
    <h1 class="fs-display text-2xl md:text-3xl text-fs-primary mt-2">{{ $post->title }}</h1>
    <p class="text-sm text-fs-secondary mt-2">{{ optional($post->published_at)->toFormattedDateString() }}</p>
    <div class="mt-6 whitespace-pre-wrap text-sm leading-relaxed text-fs-secondary">{{ $post->body }}</div>
</x-container>
@endsection
