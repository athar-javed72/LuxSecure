@extends('admin.layout')

@section('title', 'Testimonials')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Content', 'href' => route('admin.cms.index')],
    ['label' => 'Testimonials'],
]" />

<x-page-header title="Testimonials" />

<x-panel class="mb-6 max-w-2xl">
    <h2 class="text-sm font-semibold text-fs-primary mb-3">Add testimonial</h2>
    <form method="POST" action="{{ route('admin.cms.testimonials.store') }}" class="space-y-3">
        @csrf
        <x-input label="Name" name="name" required />
        <x-input label="Role" name="role" />
        <div>
            <label for="quote-new" class="block text-xs font-semibold text-fs-primary mb-1">Quote</label>
            <textarea id="quote-new" name="quote" rows="3" required class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20" placeholder="Quote"></textarea>
        </div>
        <x-button type="submit" variant="primary">Add</x-button>
    </form>
</x-panel>

@if($testimonials->isEmpty())
    <x-empty-state title="No testimonials yet" description="Showcase client feedback on your site." />
@else
    <div class="space-y-3">
        @foreach ($testimonials as $item)
            <x-panel>
                <form method="POST" action="{{ route('admin.cms.testimonials.update', $item) }}" class="space-y-2">
                    @csrf @method('PUT')
                    <x-input label="Name" name="name" :value="$item->name" />
                    <x-input label="Role" name="role" :value="$item->role" />
                    <div>
                        <label class="block text-xs font-semibold text-fs-primary mb-1">Quote</label>
                        <textarea name="quote" rows="3" class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20">{{ $item->quote }}</textarea>
                    </div>
                    <label class="text-sm inline-flex gap-2 items-center min-h-11"><input type="checkbox" name="is_published" value="1" @checked($item->is_published) class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand"> Published</label>
                    <div class="flex flex-wrap gap-2">
                        <x-button type="submit" variant="secondary">Save</x-button>
                        <button formaction="{{ route('admin.cms.testimonials.destroy', $item) }}" formmethod="POST" class="text-fs-danger text-sm hover:underline min-h-11 px-2" onclick="event.preventDefault(); fetch(this.formAction,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'DELETE'}}).then(()=>location.reload())">Delete</button>
                    </div>
                </form>
            </x-panel>
        @endforeach
    </div>
@endif
<div class="mt-4">{{ $testimonials->links() }}</div>
@endsection
