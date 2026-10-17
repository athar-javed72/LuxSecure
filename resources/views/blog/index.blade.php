@extends('include.master')

@section('title', 'Blog')

@section('content')
<x-container class="py-10">
    <h1 class="text-3xl font-bold mb-6">Blog</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($posts as $post)
            <x-blog-card
                :title="$post->title"
                :excerpt="$post->excerpt"
                :image="$post->featured_image ?: 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80'"
                :date="optional($post->published_at)->toDateString()"
                :category="$post->category"
                :href="route('blog.show', $post->slug)"
            />
        @endforeach
    </div>
    <div class="mt-6">{{ $posts->links() }}</div>
</x-container>
@endsection
