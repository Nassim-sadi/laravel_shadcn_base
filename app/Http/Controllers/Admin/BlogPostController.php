<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\BlogPostBody;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BlogPostController extends Controller
{
    /**
     * Display a listing of the blog posts.
     */
    public function index()
    {
        $posts = BlogPost::with('category', 'author', 'tags')->latest()->paginate(10);
        return view('admin.blog.index', compact('posts'));
    }

    /**
     * Show the form for creating or editing a blog post.
     */
    public function create()
    {
        $categories = \App\Models\BlogCategory::all();
        $tags = \App\Models\BlogTag::all();
        return view('admin.blog.form', compact('categories', 'tags'));
    }

    /**
     * Store a newly created blog post in storage.
     */
    public function store(Request $request)
    {
        // Validation and logic will be handled by FormRequests/Services later, 
        // but the controller method signature is needed here.
        $data = $request->validated();

        // Example: Create Post Model
        $post = BlogPost::create([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'excerpt' => $data['excerpt'] ?? null,
            'is_published' => $data['is_published'],
            'featured' => $data['featured'],
            'user_id' => Auth::id(),
            'category_id' => $data['category_id'] ?? null,
        ]);

        // Example: Create Body Content Model (using the post ID)
        $post->body()->create([
            'body' => $data['body'] ?? ''
        ]);
        
        // Example: Attach Tags
        if (isset($data['tag_ids']) && is_array($data['tag_ids'])) {
            $post->tags()->attach($data['tag_ids']);
        }

        return redirect()->route('admin.blog.index')->with('success', 'Blog post created successfully.');
    }

    /**
     * Show the specified resource (edit form).
     */
    public function edit(BlogPost $post)
    {
        $categories = \App\Models\BlogCategory::all();
        $tags = \App\Models\BlogTag::all();
        return view('admin.blog.form', compact('categories', 'tags', 'post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlogPost $post)
    {
        // Logic to update post details and body content
        $data = $request->validated();
        $post->update([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'excerpt' => $data['excerpt'] ?? null,
            'is_published' => $data['is_published'],
            'featured' => $data['featured'],
            // user_id and category_id should usually remain unless changing ownership/context
        ]);

        // Update Body Content (if necessary)
        $post->body()->updateOrCreate([], ['body' => $data['body'] ?? null]);

        // Re-attach Tags
        if (isset($data['tag_ids']) && is_array($data['tag_ids'])) {
            $post->tags()->sync($data['tag_ids']);
        }

        return redirect()->route('admin.blog.index')->with('success', 'Blog post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogPost $post)
    {
        // Deleting the parent blog post should cascade delete its body content due to on('cascade') constraint.
        $post->delete();
        return redirect()->route('admin.blog.index')->with('success', 'Blog post deleted successfully.');
    }
}