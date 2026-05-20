<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCatalogAttributeRequest;
use App\Http\Requests\Api\UpdateCatalogAttributeRequest;
use App\Http\Resources\CatalogAttributeResource;
use App\Models\CatalogAttribute;
use Illuminate\Support\Str;

class CatalogAttributeController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', CatalogAttribute::class);

        $attributes = CatalogAttribute::query()
            ->with(['values'])
            ->orderBy('name->'.app()->getLocale())
            ->get();

        return CatalogAttributeResource::collection($attributes);
    }

    public function store(StoreCatalogAttributeRequest $request)
    {
        $this->authorize('create', CatalogAttribute::class);

        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name'][app()->getLocale()] ?? $validated['name']['en'] ?? '');

        $attribute = CatalogAttribute::create($validated);

        if (isset($validated['values'])) {
            foreach ($validated['values'] as $index => $value) {
                $attribute->values()->create([
                    'value' => $value,
                    'order' => $index,
                ]);
            }
        }

        activity_log('catalog_attribute.created', [
            'attribute_id' => $attribute->id,
            'user_id' => auth()->id(),
        ]);

        return new CatalogAttributeResource($attribute->load('values'));
    }

    public function update(UpdateCatalogAttributeRequest $request, CatalogAttribute $catalogAttribute)
    {
        $this->authorize('update', $catalogAttribute);

        $validated = $request->validated();

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name'][app()->getLocale()] ?? $validated['name']['en'] ?? '');
        }

        $catalogAttribute->update($validated);

        if (isset($validated['values'])) {
            $catalogAttribute->values()->delete();
            foreach ($validated['values'] as $index => $value) {
                $catalogAttribute->values()->create([
                    'value' => $value,
                    'order' => $index,
                ]);
            }
        }

        activity_log('catalog_attribute.updated', [
            'attribute_id' => $catalogAttribute->id,
            'user_id' => auth()->id(),
        ]);

        return new CatalogAttributeResource($catalogAttribute->load('values'));
    }

    public function destroy(CatalogAttribute $catalogAttribute)
    {
        $this->authorize('delete', $catalogAttribute);

        $catalogAttribute->delete();

        activity_log('catalog_attribute.deleted', [
            'attribute_id' => $catalogAttribute->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Attribute deleted successfully']);
    }
}
