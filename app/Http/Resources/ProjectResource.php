<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->translated('title'),
            'title_translations' => $this->title,
            'description' => $this->translated('description'),
            'description_translations' => $this->description,
            'client' => $this->translated('client'),
            'client_translations' => $this->client,
            'image' => $this->image,
            'image_id' => $this->image_id,
            'image_url' => $this->relationLoaded('image') ? $this->getRelation('image')?->url : null,
            'image_thumbnail_url' => $this->relationLoaded('image') ? $this->getRelation('image')?->thumbnail_url : null,
            'url' => $this->url,
            'technologies' => $this->technologies,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'seo_title' => $this->translated('seo_title'),
            'seo_title_translations' => $this->seo_title,
            'seo_description' => $this->translated('seo_description'),
            'seo_description_translations' => $this->seo_description,
            'seo_keywords' => $this->translated('seo_keywords'),
            'seo_keywords_translations' => $this->seo_keywords,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
