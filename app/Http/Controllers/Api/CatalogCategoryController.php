<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCatalogCategoryRequest;
use App\Http\Requests\Api\UpdateCatalogCategoryRequest;
use App\Http\Resources\CatalogCategoryResource;
use App\Models\CatalogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CatalogCategoryController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', CatalogCategory::class);

        $categories = CatalogCategory::query()
            ->with(['image', 'parent', 'children'])
            ->when($request->search, fn($q, $search) => $q->where('name->'.app()->getLocale(), 'like', "%{$search}%"))
            ->when($request->is_active !== null, fn($q) => $q->where('is_active', $request->is_active))
            ->when($request->parent_id !== null, fn($q) => $q->where('parent_id', $request->parent_id))
            ->orderBy($request->sort_by ?? 'order', $request->sort_order ?? 'asc')
            ->paginate($request->per_page ?? 15);

        return CatalogCategoryResource::collection($categories);
    }

    public function all()
    {
        $categories = CatalogCategory::query()
            ->with(['image', 'children'])
            ->active()
            ->ordered()
            ->get();

        return CatalogCategoryResource::collection($categories);
    }

    public function store(StoreCatalogCategoryRequest $request)
    {
        $this->authorize('create', CatalogCategory::class);

        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name'][app()->getLocale()] ?? $validated['name']['en'] ?? '');

        $category = CatalogCategory::create($validated);

        activity_log('catalog_category.created', [
            'category_id' => $category->id,
            'user_id' => auth()->id(),
        ]);

        return new CatalogCategoryResource($category->load(['image', 'parent']));
    }

    public function show(CatalogCategory $catalogCategory)
    {
        $this->authorize('view', $catalogCategory);

        return new CatalogCategoryResource($catalogCategory->load(['image', 'parent', 'children']));
    }

    public function update(UpdateCatalogCategoryRequest $request, CatalogCategory $catalogCategory)
    {
        $this->authorize('update', $catalogCategory);

        $validated = $request->validated();

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name'][app()->getLocale()] ?? $validated['name']['en'] ?? '');
        }

        $catalogCategory->update($validated);

        activity_log('catalog_category.updated', [
            'category_id' => $catalogCategory->id,
            'user_id' => auth()->id(),
        ]);

        return new CatalogCategoryResource($catalogCategory->load(['image', 'parent']));
    }

    public function destroy(CatalogCategory $catalogCategory)
    {
        $this->authorize('delete', $catalogCategory);

        $productCount = $catalogCategory->products()->count();
        if ($productCount > 0) {
            return response()->json([
                'message' => "Cannot delete category. It has {$productCount} product(s) associated with it.",
            ], 409);
        }

        $catalogCategory->delete();

        activity_log('catalog_category.deleted', [
            'category_id' => $catalogCategory->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Category deleted successfully']);
    }

    public function toggleStatus(CatalogCategory $catalogCategory)
    {
        $this->authorize('update', $catalogCategory);

        $catalogCategory->update(['is_active' => ! $catalogCategory->is_active]);

        activity_log('catalog_category.toggled', [
            'category_id' => $catalogCategory->id,
            'is_active' => $catalogCategory->is_active,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Status updated', 'is_active' => $catalogCategory->is_active]);
    }
}
