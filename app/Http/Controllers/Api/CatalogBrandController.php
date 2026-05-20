<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCatalogBrandRequest;
use App\Http\Requests\Api\UpdateCatalogBrandRequest;
use App\Http\Resources\CatalogBrandResource;
use App\Models\CatalogBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CatalogBrandController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', CatalogBrand::class);

        $brands = CatalogBrand::query()
            ->withCount('products')
            ->with('logo')
            ->ordered()
            ->get();

        return CatalogBrandResource::collection($brands);
    }

    public function all()
    {
        $brands = CatalogBrand::query()
            ->active()
            ->ordered()
            ->get(['id', 'name', 'slug']);

        return CatalogBrandResource::collection($brands);
    }

    public function store(StoreCatalogBrandRequest $request)
    {
        $this->authorize('create', CatalogBrand::class);

        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name'][app()->getLocale()] ?? $validated['name']['en'] ?? '');

        $brand = CatalogBrand::create($validated);

        activity_log('catalog_brand.created', [
            'brand_id' => $brand->id,
            'user_id' => auth()->id(),
        ]);

        return new CatalogBrandResource($brand);
    }

    public function show(CatalogBrand $catalogBrand)
    {
        $this->authorize('view', CatalogBrand::class);

        return new CatalogBrandResource($catalogBrand);
    }

    public function update(UpdateCatalogBrandRequest $request, CatalogBrand $catalogBrand)
    {
        $this->authorize('edit', CatalogBrand::class);

        $validated = $request->validated();

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name'][app()->getLocale()] ?? $validated['name']['en'] ?? '');
        }

        $catalogBrand->update($validated);

        activity_log('catalog_brand.updated', [
            'brand_id' => $catalogBrand->id,
            'user_id' => auth()->id(),
        ]);

        return new CatalogBrandResource($catalogBrand);
    }

    public function destroy(CatalogBrand $catalogBrand)
    {
        $this->authorize('delete', CatalogBrand::class);

        if ($catalogBrand->products()->exists()) {
            return response()->json(['message' => 'Cannot delete brand with existing products'], 422);
        }

        $catalogBrand->delete();

        activity_log('catalog_brand.deleted', [
            'brand_id' => $catalogBrand->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Brand deleted successfully']);
    }

    public function toggleStatus(CatalogBrand $catalogBrand)
    {
        $this->authorize('edit', CatalogBrand::class);

        $catalogBrand->update(['is_active' => !$catalogBrand->is_active]);

        activity_log('catalog_brand.toggled', [
            'brand_id' => $catalogBrand->id,
            'is_active' => $catalogBrand->is_active,
            'user_id' => auth()->id(),
        ]);

        return new CatalogBrandResource($catalogBrand);
    }
}
