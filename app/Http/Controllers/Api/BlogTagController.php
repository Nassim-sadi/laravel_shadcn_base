<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogTagResource;
use App\Models\BlogTag;
use Illuminate\Http\Request;

class BlogTagController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', BlogTag::class);

        $tags = BlogTag::withCount('posts')->latest()->get();

        return BlogTagResource::collection($tags);
    }

    public function store(Request $request)
    {
        $this->authorize('create', BlogTag::class);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_tags,slug',
        ]);

        $tag = BlogTag::create($data);

        activity_log('blog_tag.created', [
            'tag_id' => $tag->id,
            'user_id' => auth()->id(),
        ]);

        return new BlogTagResource($tag);
    }

    public function destroy(BlogTag $blogTag)
    {
        $this->authorize('delete', BlogTag::class);

        $blogTag->delete();

        activity_log('blog_tag.deleted', [
            'tag_id' => $blogTag->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Deleted successfully.']);
    }
}
