@extends('admin.layout')

@section('title', 'Testimonials')

@section('content')
<h1 class="text-xl font-bold mb-4">Testimonials</h1>
<form method="POST" action="{{ route('admin.cms.testimonials.store') }}" class="mb-6 space-y-3 rounded-md bg-white border border-black/5 p-4 max-w-2xl">
    @csrf
    <x-input label="Name" name="name" required />
    <x-input label="Role" name="role" />
    <textarea name="quote" rows="3" required class="w-full px-4 py-3 rounded-sm border border-slate-200 text-sm" placeholder="Quote"></textarea>
    <x-button type="submit" variant="primary">Add</x-button>
</form>
<div class="space-y-3">
    @foreach ($testimonials as $item)
        <form method="POST" action="{{ route('admin.cms.testimonials.update', $item) }}" class="rounded-md bg-white border border-black/5 p-4 space-y-2">
            @csrf @method('PUT')
            <x-input label="Name" name="name" :value="$item->name" />
            <x-input label="Role" name="role" :value="$item->role" />
            <textarea name="quote" rows="3" class="w-full px-4 py-3 rounded-sm border border-slate-200 text-sm">{{ $item->quote }}</textarea>
            <label class="text-sm inline-flex gap-2"><input type="checkbox" name="is_published" value="1" @checked($item->is_published)> Published</label>
            <div class="flex gap-2">
                <x-button type="submit" variant="secondary">Save</x-button>
                <button formaction="{{ route('admin.cms.testimonials.destroy', $item) }}" formmethod="POST" class="text-fs-danger text-sm" onclick="event.preventDefault(); fetch(this.formAction,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'DELETE'}}).then(()=>location.reload())">Delete</button>
            </div>
        </form>
    @endforeach
</div>
<div class="mt-4">{{ $testimonials->links() }}</div>
@endsection
