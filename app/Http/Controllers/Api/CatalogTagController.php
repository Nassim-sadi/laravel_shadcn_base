<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCatalogTagRequest;
use App\Http\Resources\CatalogTagResource;
use App\Models\CatalogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CatalogTagController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', CatalogTag::class);

        $tags = CatalogTag::query()
            ->withCount('products')
            ->orderBy('name->'.app()->getLocale())
            ->get();

        return CatalogTagResource::collection($tags);
    }

    public function store(StoreCatalogTagRequest $request)
    {
        $this->authorize('create', CatalogTag::class);

        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name'][app()->getLocale()] ?? $validated['name']['en'] ?? '');

        $tag = CatalogTag::create($validated);

        activity_log('catalog_tag.created', [
            'tag_id' => $tag->id,
            'user_id' => auth()->id(),
        ]);

        return new CatalogTagResource($tag);
    }

    public function destroy(CatalogTag $catalogTag)
    {
        $this->authorize('delete', CatalogTag::class);

        $catalogTag->delete();

        activity_log('catalog_tag.deleted', [
            'tag_id' => $catalogTag->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Tag deleted successfully']);
    }
}
