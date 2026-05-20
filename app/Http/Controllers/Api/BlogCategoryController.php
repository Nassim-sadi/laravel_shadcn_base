<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBlogCategoryRequest;
use App\Http\Requests\Api\UpdateBlogCategoryRequest;
use App\Http\Resources\BlogCategoryResource;
use App\Models\BlogCategory;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', BlogCategory::class);

        $categories = BlogCategory::withCount('posts')->latest()->paginate(15);

        return BlogCategoryResource::collection($categories);
    }

    public function store(StoreBlogCategoryRequest $request)
    {
        $this->authorize('create', BlogCategory::class);

        $category = BlogCategory::create($request->validated());

        activity_log('blog_category.created', [
            'category_id' => $category->id,
            'user_id' => auth()->id(),
        ]);

        return new BlogCategoryResource($category);
    }

    public function show(BlogCategory $blogCategory)
    {
        $this->authorize('view', $blogCategory);

        return new BlogCategoryResource($blogCategory->loadCount('posts'));
    }

    public function update(UpdateBlogCategoryRequest $request, BlogCategory $blogCategory)
    {
        $this->authorize('update', $blogCategory);

        $blogCategory->update($request->validated());

        activity_log('blog_category.updated', [
            'category_id' => $blogCategory->id,
            'user_id' => auth()->id(),
        ]);

        return new BlogCategoryResource($blogCategory);
    }

    public function destroy(BlogCategory $blogCategory)
    {
        $this->authorize('delete', $blogCategory);

        $postCount = $blogCategory->posts()->count();
        if ($postCount > 0) {
            return response()->json([
                'message' => "Cannot delete category. It has {$postCount} post(s) associated with it.",
            ], 409);
        }

        $blogCategory->delete();

        activity_log('blog_category.deleted', [
            'category_id' => $blogCategory->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Deleted successfully.']);
    }
}
