<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogMarqueeItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image_url' => $this->image?->url,
            'image_thumbnail_url' => $this->image?->thumbnail_url,
            'text' => $this->text,
            'position' => $this->position,
            'order' => $this->order,
            'is_active' => $this->is_active,
        ];
    }
}
