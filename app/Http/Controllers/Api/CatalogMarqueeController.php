<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCatalogMarqueeItemRequest;
use App\Http\Requests\Api\UpdateCatalogMarqueeItemRequest;
use App\Http\Resources\CatalogMarqueeItemResource;
use App\Models\CatalogMarqueeItem;
use Illuminate\Http\Request;

class CatalogMarqueeController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', CatalogMarqueeItem::class);

        $items = CatalogMarqueeItem::query()
            ->with('image')
            ->when($request->position, fn($q, $pos) => $q->where('position', $pos))
            ->when($request->is_active !== null, fn($q) => $q->where('is_active', $request->is_active))
            ->ordered()
            ->get();

        return CatalogMarqueeItemResource::collection($items);
    }

    public function store(StoreCatalogMarqueeItemRequest $request)
    {
        $this->authorize('create', CatalogMarqueeItem::class);

        $validated = $request->validated();

        $item = CatalogMarqueeItem::create($validated);

        activity_log('catalog_marquee.created', [
            'item_id' => $item->id,
            'user_id' => auth()->id(),
        ]);

        return new CatalogMarqueeItemResource($item->load('image'));
    }

    public function update(UpdateCatalogMarqueeItemRequest $request, CatalogMarqueeItem $catalogMarqueeItem)
    {
        $this->authorize('update', $catalogMarqueeItem);

        $validated = $request->validated();

        $catalogMarqueeItem->update($validated);

        activity_log('catalog_marquee.updated', [
            'item_id' => $catalogMarqueeItem->id,
            'user_id' => auth()->id(),
        ]);

        return new CatalogMarqueeItemResource($catalogMarqueeItem->load('image'));
    }

    public function destroy(CatalogMarqueeItem $catalogMarqueeItem)
    {
        $this->authorize('delete', $catalogMarqueeItem);

        $catalogMarqueeItem->delete();

        activity_log('catalog_marquee.deleted', [
            'item_id' => $catalogMarqueeItem->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Marquee item deleted successfully']);
    }

    public function publicIndex()
    {
        $items = CatalogMarqueeItem::query()
            ->with('image')
            ->active()
            ->ordered()
            ->get();

        return CatalogMarqueeItemResource::collection($items);
    }
}
