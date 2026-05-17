<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogCategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->translated('name'),
            'name_translations' => $this->name,
            'slug' => $this->slug,
            'description' => $this->translated('description'),
            'description_translations' => $this->description,
            'is_published' => $this->is_published,
            'posts_count' => $this->when($this->relationLoaded('posts'), $this->posts_count ?? $this->posts->count()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
