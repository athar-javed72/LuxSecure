@extends('include.master')

@section('title', 'Blog')

@section('content')
<x-container class="py-10">
    <x-page-header
        title="Blog"
        subtitle="Market insights, buying guides, and updates from the LuxSecure team."
    />

    @if ($posts->isNotEmpty())
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
    @else
        <x-empty-state
            title="No posts published yet"
            description="Check back soon for new articles and market updates."
            action-label="Back to home"
            :action-href="route('home')"
        />
    @endif

    <div class="mt-6">{{ $posts->links() }}</div>
</x-container>
@endsection
