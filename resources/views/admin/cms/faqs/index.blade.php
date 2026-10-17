@extends('admin.layout')

@section('title', 'FAQs')

@section('content')
<h1 class="text-xl font-bold mb-4">FAQs</h1>
<form method="POST" action="{{ route('admin.cms.faqs.store') }}" class="mb-6 space-y-3 rounded-md bg-white border border-black/5 p-4 max-w-2xl">
    @csrf
    <x-input label="Question" name="question" required />
    <textarea name="answer" rows="3" required class="w-full px-4 py-3 rounded-sm border border-slate-200 text-sm" placeholder="Answer"></textarea>
    <x-input label="Sort" name="sort_order" type="number" value="0" />
    <x-input label="Meta title" name="meta_title" />
    <x-input label="Meta description" name="meta_description" />
    <label class="text-sm inline-flex gap-2"><input type="checkbox" name="is_published" value="1" checked> Published</label>
    <x-button type="submit" variant="primary">Add FAQ</x-button>
</form>
<div class="space-y-4">
    @foreach ($faqs as $faq)
        <form method="POST" action="{{ route('admin.cms.faqs.update', $faq) }}" class="rounded-md bg-white border border-black/5 p-4 space-y-2">
            @csrf @method('PUT')
            <x-input label="Question" name="question" :value="$faq->question" />
            <textarea name="answer" rows="3" class="w-full px-4 py-3 rounded-sm border border-slate-200 text-sm">{{ $faq->answer }}</textarea>
            <div class="flex flex-wrap gap-2 items-center">
                <x-input label="Sort" name="sort_order" type="number" :value="$faq->sort_order" />
                <label class="text-sm inline-flex gap-2 mt-5"><input type="checkbox" name="is_published" value="1" @checked($faq->is_published)> Published</label>
                <x-button type="submit" variant="secondary">Save</x-button>
                <button formaction="{{ route('admin.cms.faqs.destroy', $faq) }}" formmethod="POST" onclick="this.form.querySelector('[name=_method]')?.remove(); let m=document.createElement('input');m.type='hidden';m.name='_method';m.value='DELETE';this.form.appendChild(m);" class="text-fs-danger text-sm">Delete</button>
            </div>
        </form>
    @endforeach
</div>
<div class="mt-4">{{ $faqs->links() }}</div>
@endsection
