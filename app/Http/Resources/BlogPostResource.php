<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->translated('title'),
            'title_translations' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->translated('excerpt'),
            'excerpt_translations' => $this->excerpt,
            'body' => $this->when($this->relationLoaded('body'), $this->body?->translated('body')),
            'body_translations' => $this->when($this->relationLoaded('body'), $this->body?->body),
            'is_published' => $this->is_published,
            'featured' => $this->featured,
            'category' => new BlogCategoryResource($this->whenLoaded('category')),
            'category_id' => $this->category_id,
            'tags' => BlogTagResource::collection($this->whenLoaded('tags')),
            'tag_ids' => $this->when($this->relationLoaded('tags'), $this->tags->pluck('id')),
            'image' => $this->image,
            'image_id' => $this->image_id,
            'image_url' => $this->relationLoaded('image') ? $this->getRelation('image')?->url : null,
            'image_thumbnail_url' => $this->relationLoaded('image') ? $this->getRelation('image')?->thumbnail_url : null,
            'author' => $this->whenLoaded('author', fn () => [
                'id' => $this->author->id,
                'name' => $this->author->name,
            ]),
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
