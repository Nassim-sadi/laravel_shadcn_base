<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogBrandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name'),
            'name_translations' => $this->name,
            'slug' => $this->slug,
            'logo_id' => $this->logo_id,
            'logo_url' => $this->whenLoaded('logo', fn () => $this->logo?->url),
            'description' => $this->description,
            'website' => $this->website,
            'is_active' => $this->is_active,
            'order' => $this->order,
            'products_count' => $this->whenCounted('products'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
