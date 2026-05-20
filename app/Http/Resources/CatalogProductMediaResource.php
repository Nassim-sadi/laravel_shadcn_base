<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogProductMediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'media_id' => $this->media_id,
            'type' => $this->type,
            'video_url' => $this->video_url,
            'thumbnail_url' => $this->thumbnail_url,
            'image_url' => $this->media?->url,
            'image_thumbnail_url' => $this->media?->thumbnail_url,
            'order' => $this->order,
        ];
    }
}
