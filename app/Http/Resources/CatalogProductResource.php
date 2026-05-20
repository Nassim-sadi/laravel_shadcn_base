<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'body' => $this->body,
            'sku' => $this->sku,
            'price_display' => $this->price_display,
            'badges' => $this->badges ?? [],
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', fn() => new CatalogCategoryResource($this->category)),
            'brand_id' => $this->brand_id,
            'brand' => $this->whenLoaded('brand', fn() => new CatalogBrandResource($this->brand)),
            'media' => $this->whenLoaded('media', fn() => CatalogProductMediaResource::collection($this->media)),
            'tags' => $this->whenLoaded('tags', fn() => CatalogTagResource::collection($this->tags)),
            'attributes' => $this->whenLoaded('attributes', function () {
                return $this->attributes->map(function ($attribute) {
                    return [
                        'id' => $attribute->id,
                        'name' => $attribute->name,
                        'slug' => $attribute->slug,
                        'type' => $attribute->type,
                        'pivot' => [
                            'attribute_value_id' => $attribute->pivot->attribute_value_id,
                            'custom_text' => $attribute->pivot->custom_text,
                        ],
                        'values' => $attribute->whenLoaded('values', fn() => $attribute->values->map(fn($v) => [
                            'id' => $v->id,
                            'value' => $v->value,
                        ])),
                    ];
                });
            }),
            'is_active' => $this->is_active,
            'order' => $this->order,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
