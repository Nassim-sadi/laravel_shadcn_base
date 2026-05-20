<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCatalogProductRequest;
use App\Http\Requests\Api\UpdateCatalogProductRequest;
use App\Http\Resources\CatalogProductResource;
use App\Models\CatalogProduct;
use App\Models\CatalogProductMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogProductController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', CatalogProduct::class);

        $products = CatalogProduct::query()
            ->with(['category', 'brand', 'media.media', 'tags'])
            ->when($request->search, fn($q, $search) => $q->where('name->'.app()->getLocale(), 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"))
            ->when($request->category_id, fn($q, $id) => $q->where('category_id', $id))
            ->when($request->brand_id, fn($q, $id) => $q->where('brand_id', $id))
            ->when($request->is_active !== null, fn($q) => $q->where('is_active', $request->is_active))
            ->when($request->tag, fn($q, $tag) => $q->whereHas('tags', fn($tq) => $tq->where('slug', $tag)))
            ->orderBy($request->sort_by ?? 'order', $request->sort_order ?? 'asc')
            ->paginate($request->per_page ?? 15);

        return CatalogProductResource::collection($products);
    }

    public function store(StoreCatalogProductRequest $request)
    {
        $this->authorize('create', CatalogProduct::class);

        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name'][app()->getLocale()] ?? $validated['name']['en'] ?? '');

        $product = CatalogProduct::create($validated);

        $this->syncMedia($product, $validated['media'] ?? []);
        $this->syncTags($product, $validated['tag_ids'] ?? []);
        $this->syncAttributes($product, $validated['attributes'] ?? []);

        activity_log('catalog_product.created', [
            'product_id' => $product->id,
            'user_id' => auth()->id(),
        ]);

        return new CatalogProductResource($product->load(['category', 'media.media', 'tags']));
    }

    public function show(CatalogProduct $catalogProduct)
    {
        $this->authorize('view', $catalogProduct);

        return new CatalogProductResource($catalogProduct->load(['category', 'media.media', 'tags', 'attributes.values']));
    }

    public function update(UpdateCatalogProductRequest $request, CatalogProduct $catalogProduct)
    {
        $this->authorize('update', $catalogProduct);

        $validated = $request->validated();

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name'][app()->getLocale()] ?? $validated['name']['en'] ?? '');
        }

        $catalogProduct->update($validated);

        if (isset($validated['media'])) {
            $this->syncMedia($catalogProduct, $validated['media']);
        }
        if (isset($validated['tag_ids'])) {
            $this->syncTags($catalogProduct, $validated['tag_ids']);
        }
        if (isset($validated['attributes'])) {
            $this->syncAttributes($catalogProduct, $validated['attributes']);
        }

        activity_log('catalog_product.updated', [
            'product_id' => $catalogProduct->id,
            'user_id' => auth()->id(),
        ]);

        return new CatalogProductResource($catalogProduct->load(['category', 'media.media', 'tags']));
    }

    public function destroy(CatalogProduct $catalogProduct)
    {
        $this->authorize('delete', $catalogProduct);

        $catalogProduct->media()->each(function ($mediaItem) {
            if ($mediaItem->thumbnail_path && Storage::disk('public')->exists($mediaItem->thumbnail_path)) {
                Storage::disk('public')->delete($mediaItem->thumbnail_path);
            }
            $mediaItem->delete();
        });

        $catalogProduct->delete();

        activity_log('catalog_product.deleted', [
            'product_id' => $catalogProduct->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function toggleStatus(CatalogProduct $catalogProduct)
    {
        $this->authorize('update', $catalogProduct);

        $catalogProduct->update(['is_active' => ! $catalogProduct->is_active]);

        activity_log('catalog_product.toggled', [
            'product_id' => $catalogProduct->id,
            'is_active' => $catalogProduct->is_active,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Status updated', 'is_active' => $catalogProduct->is_active]);
    }

    protected function syncMedia(CatalogProduct $product, array $mediaItems): void
    {
        $product->media()->delete();

        foreach ($mediaItems as $index => $item) {
            CatalogProductMedia::create([
                'product_id' => $product->id,
                'media_id' => $item['media_id'] ?? null,
                'type' => $item['type'] ?? 'image',
                'video_url' => $item['video_url'] ?? null,
                'thumbnail_path' => $item['thumbnail_path'] ?? null,
                'order' => $index,
            ]);
        }
    }

    protected function syncTags(CatalogProduct $product, array $tagIds): void
    {
        $product->tags()->sync($tagIds);
    }

    protected function syncAttributes(CatalogProduct $product, array $attributes): void
    {
        $product->attributes()->detach();

        foreach ($attributes as $attr) {
            $product->attributes()->attach($attr['attribute_id'], [
                'attribute_value_id' => $attr['attribute_value_id'] ?? null,
                'custom_text' => $attr['custom_text'] ?? null,
            ]);
        }
    }
}
