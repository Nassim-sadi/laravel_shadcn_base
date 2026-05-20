<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image_url' => $this->image?->url,
            'image_thumbnail_url' => $this->image?->thumbnail_url,
            'parent_id' => $this->parent_id,
            'parent' => $this->whenLoaded('parent', fn() => new self($this->parent)),
            'children' => $this->whenLoaded('children', fn() => self::collection($this->children)),
            'order' => $this->order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
