<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::query()
            ->published()
            ->with(['category', 'image', 'tags'])
            ->latest()
            ->paginate(12);

        return view('pages.blog.index', compact('posts'));
    }

    public function show(BlogPost $blogPost): View
    {
        if (! $blogPost->is_published) {
            abort(404);
        }

        $blogPost->load(['category', 'image', 'tags', 'author', 'body']);

        return view('pages.blog.show', compact('blogPost'));
    }
}
