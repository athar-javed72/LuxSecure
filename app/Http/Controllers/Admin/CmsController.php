<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CmsController extends Controller
{
    public function dashboard(): View
    {
        Gate::authorize('viewAny', BlogPost::class);

        return view('admin.cms.index', [
            'counts' => [
                'posts' => BlogPost::count(),
                'faqs' => Faq::count(),
                'testimonials' => Testimonial::count(),
                'pages' => Page::count(),
            ],
        ]);
    }

    // —— Blog ——
    public function posts(): View
    {
        Gate::authorize('viewAny', BlogPost::class);

        return view('admin.cms.posts.index', [
            'posts' => BlogPost::latest()->paginate(15),
        ]);
    }

    public function createPost(): View
    {
        Gate::authorize('create', BlogPost::class);

        return view('admin.cms.posts.form', ['post' => new BlogPost]);
    }

    public function storePost(Request $request): RedirectResponse
    {
        Gate::authorize('create', BlogPost::class);
        $data = $this->postRules($request);
        $data['author_id'] = $request->user()->id;
        $data['slug'] = BlogPost::uniqueSlug($data['title']);
        if (($data['status'] ?? 'draft') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
        BlogPost::create($data);

        return redirect()->route('admin.cms.posts.index')->with('success', 'Post created.');
    }

    public function editPost(BlogPost $post): View
    {
        Gate::authorize('update', $post);

        return view('admin.cms.posts.form', compact('post'));
    }

    public function updatePost(Request $request, BlogPost $post): RedirectResponse
    {
        Gate::authorize('update', $post);
        $data = $this->postRules($request);
        if (filled($data['slug'] ?? null)) {
            $data['slug'] = BlogPost::uniqueSlug($data['slug'], $post->id);
        }
        $post->update($data);

        return redirect()->route('admin.cms.posts.index')->with('success', 'Post updated.');
    }

    public function destroyPost(BlogPost $post): RedirectResponse
    {
        Gate::authorize('delete', $post);
        $post->delete();

        return back()->with('success', 'Post deleted.');
    }

    // —— FAQs ——
    public function faqs(): View
    {
        Gate::authorize('viewAny', Faq::class);

        return view('admin.cms.faqs.index', ['faqs' => Faq::orderBy('sort_order')->paginate(30)]);
    }

    public function storeFaq(Request $request): RedirectResponse
    {
        Gate::authorize('create', Faq::class);
        Faq::create($request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:300'],
        ]) + ['is_published' => $request->boolean('is_published', true)]);

        return back()->with('success', 'FAQ created.');
    }

    public function updateFaq(Request $request, Faq $faq): RedirectResponse
    {
        Gate::authorize('update', $faq);
        $faq->update($request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:300'],
        ]) + ['is_published' => $request->boolean('is_published')]);

        return back()->with('success', 'FAQ updated.');
    }

    public function destroyFaq(Faq $faq): RedirectResponse
    {
        Gate::authorize('delete', $faq);
        $faq->delete();

        return back()->with('success', 'FAQ deleted.');
    }

    // —— Testimonials ——
    public function testimonials(): View
    {
        Gate::authorize('viewAny', Testimonial::class);

        return view('admin.cms.testimonials.index', [
            'testimonials' => Testimonial::orderBy('sort_order')->paginate(20),
        ]);
    }

    public function storeTestimonial(Request $request): RedirectResponse
    {
        Gate::authorize('create', Testimonial::class);
        Testimonial::create($request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:160'],
            'quote' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
        ]) + ['is_published' => $request->boolean('is_published', true)]);

        return back()->with('success', 'Testimonial created.');
    }

    public function updateTestimonial(Request $request, Testimonial $testimonial): RedirectResponse
    {
        Gate::authorize('update', $testimonial);
        $testimonial->update($request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:160'],
            'quote' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
        ]) + ['is_published' => $request->boolean('is_published')]);

        return back()->with('success', 'Testimonial updated.');
    }

    public function destroyTestimonial(Testimonial $testimonial): RedirectResponse
    {
        Gate::authorize('delete', $testimonial);
        $testimonial->delete();

        return back()->with('success', 'Testimonial deleted.');
    }

    // —— Pages ——
    public function pages(): View
    {
        Gate::authorize('viewAny', Page::class);

        return view('admin.cms.pages.index', ['pages' => Page::latest()->paginate(20)]);
    }

    public function storePage(Request $request): RedirectResponse
    {
        Gate::authorize('create', Page::class);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200'],
            'body' => ['nullable', 'string'],
            'is_published' => ['sometimes', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:300'],
        ]);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['is_published'] = $request->boolean('is_published');
        Page::create($data);

        return back()->with('success', 'Page created.');
    }

    public function updatePage(Request $request, Page $page): RedirectResponse
    {
        Gate::authorize('update', $page);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['required', 'string', 'max:200'],
            'body' => ['nullable', 'string'],
            'is_published' => ['sometimes', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:300'],
        ]);
        $data['is_published'] = $request->boolean('is_published');
        $page->update($data);

        return back()->with('success', 'Page updated.');
    }

    public function destroyPage(Page $page): RedirectResponse
    {
        Gate::authorize('delete', $page);
        $page->delete();

        return back()->with('success', 'Page deleted.');
    }

    /** @return array<string, mixed> */
    private function postRules(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:80'],
            'featured_image' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:300'],
        ]);
    }
}
