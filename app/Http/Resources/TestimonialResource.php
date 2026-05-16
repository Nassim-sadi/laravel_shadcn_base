<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->translated('name'),
            'name_translations' => $this->name,
            'position' => $this->translated('position'),
            'position_translations' => $this->position,
            'company' => $this->translated('company'),
            'company_translations' => $this->company,
            'content' => $this->translated('content'),
            'content_translations' => $this->content,
            'image' => $this->image,
            'image_id' => $this->image_id,
            'image_url' => $this->relationLoaded('image') ? $this->getRelation('image')?->url : null,
            'image_thumbnail_url' => $this->relationLoaded('image') ? $this->getRelation('image')?->thumbnail_url : null,
            'rating' => $this->rating,
            'is_active' => $this->is_active,
            'order' => $this->order,
            'seo_title' => $this->translated('seo_title'),
            'seo_title_translations' => $this->seo_title,
            'seo_description' => $this->translated('seo_description'),
            'seo_description_translations' => $this->seo_description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
