@extends('admin.layout')

@section('title', 'FAQs')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Content', 'href' => route('admin.cms.index')],
    ['label' => 'FAQs'],
]" />

<x-page-header title="FAQs" />

<x-panel class="mb-6 max-w-2xl">
    <h2 class="text-sm font-semibold text-fs-primary mb-3">Add FAQ</h2>
    <form method="POST" action="{{ route('admin.cms.faqs.store') }}" class="space-y-3">
        @csrf
        <x-input label="Question" name="question" required />
        <div>
            <label for="faq-answer-new" class="block text-xs font-semibold text-fs-primary mb-1">Answer</label>
            <textarea id="faq-answer-new" name="answer" rows="3" required class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20" placeholder="Answer"></textarea>
        </div>
        <x-input label="Sort" name="sort_order" type="number" value="0" />
        <x-input label="Meta title" name="meta_title" />
        <x-input label="Meta description" name="meta_description" />
        <label class="text-sm inline-flex gap-2 items-center min-h-11"><input type="checkbox" name="is_published" value="1" checked class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand"> Published</label>
        <x-button type="submit" variant="primary">Add FAQ</x-button>
    </form>
</x-panel>

@if($faqs->isEmpty())
    <x-empty-state title="No FAQs yet" description="Add common questions for your visitors." />
@else
    <div class="space-y-4">
        @foreach ($faqs as $faq)
            <x-panel>
                <form method="POST" action="{{ route('admin.cms.faqs.update', $faq) }}" class="space-y-2">
                    @csrf @method('PUT')
                    <x-input label="Question" name="question" :value="$faq->question" />
                    <div>
                        <label class="block text-xs font-semibold text-fs-primary mb-1">Answer</label>
                        <textarea name="answer" rows="3" class="w-full px-4 py-3 rounded-sm border border-black/10 bg-white text-sm text-fs-primary focus:border-fs-brand focus:ring-2 focus:ring-fs-brand/20">{{ $faq->answer }}</textarea>
                    </div>
                    <div class="flex flex-wrap gap-2 items-end">
                        <x-input label="Sort" name="sort_order" type="number" :value="$faq->sort_order" />
                        <label class="text-sm inline-flex gap-2 items-center min-h-11"><input type="checkbox" name="is_published" value="1" @checked($faq->is_published) class="rounded-sm border-black/20 text-fs-brand focus:ring-fs-brand"> Published</label>
                        <x-button type="submit" variant="secondary">Save</x-button>
                        <button formaction="{{ route('admin.cms.faqs.destroy', $faq) }}" formmethod="POST" onclick="this.form.querySelector('[name=_method]')?.remove(); let m=document.createElement('input');m.type='hidden';m.name='_method';m.value='DELETE';this.form.appendChild(m);" class="text-fs-danger text-sm hover:underline min-h-11 px-2">Delete</button>
                    </div>
                </form>
            </x-panel>
        @endforeach
    </div>
@endif
<div class="mt-4">{{ $faqs->links() }}</div>
@endsection
