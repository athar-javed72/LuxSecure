<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $typeMap = [
            'privacy-policy' => 'privacy',
            'terms-of-service' => 'terms',
            'refund-and-cancellation-policy' => 'refund',
            'cookie-policy' => 'cookies',
            'about' => 'about',
        ];

        if (isset($typeMap[$slug])) {
            $page = Page::resolveLegal($typeMap[$slug]);
            abort_unless($page, 404);
        } else {
            $page = Page::published()->where('slug', $slug)->firstOrFail();
        }

        return view('pages.show', compact('page'));
    }

    public function legal(string $type): View
    {
        $page = Page::resolveLegal($type);
        abort_unless($page, 404);

        return view('pages.show', compact('page'));
    }

    public function blogIndex(): View
    {
        $posts = BlogPost::published()->latest('published_at')->paginate(9);

        return view('blog.index', compact('posts'));
    }

    public function blogShow(string $slug): View
    {
        $post = BlogPost::published()->where('slug', $slug)->firstOrFail();

        return view('blog.show', compact('post'));
    }
}
