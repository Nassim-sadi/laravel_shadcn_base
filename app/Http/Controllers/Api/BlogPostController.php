<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBlogPostRequest;
use App\Http\Requests\Api\UpdateBlogPostRequest;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogPostController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', BlogPost::class);

        $posts = BlogPost::with(['category', 'author', 'tags', 'body'])
            ->latest()
            ->paginate(15);

        return BlogPostResource::collection($posts);
    }

    public function store(StoreBlogPostRequest $request)
    {
        $this->authorize('create', BlogPost::class);

        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $post = BlogPost::create($data);

        if (!empty($data['body'])) {
            $post->body()->create(['body' => $data['body']]);
        }

        if (!empty($data['tag_ids'])) {
            $post->tags()->sync($data['tag_ids']);
        }

        return new BlogPostResource($post->load(['category', 'author', 'tags', 'body']));
    }

    public function show(BlogPost $blogPost)
    {
        $this->authorize('view', $blogPost);

        return new BlogPostResource($blogPost->load(['category', 'author', 'tags', 'body']));
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost)
    {
        $this->authorize('update', $blogPost);

        $data = $request->validated();
        $blogPost->update($data);

        if (array_key_exists('body', $data)) {
            $blogPost->body()->updateOrCreate([], ['body' => $data['body']]);
        }

        if (array_key_exists('tag_ids', $data)) {
            $blogPost->tags()->sync($data['tag_ids'] ?? []);
        }

        return new BlogPostResource($blogPost->load(['category', 'author', 'tags', 'body']));
    }

    public function destroy(BlogPost $blogPost)
    {
        $this->authorize('delete', $blogPost);

        $blogPost->delete();

        return response()->json(['message' => 'Deleted successfully.']);
    }
}
