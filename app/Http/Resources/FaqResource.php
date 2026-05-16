<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'question' => $this->translated('question'),
            'question_translations' => $this->question,
            'answer' => $this->translated('answer'),
            'answer_translations' => $this->answer,
            'category' => $this->category,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'seo_title' => $this->translated('seo_title'),
            'seo_title_translations' => $this->seo_title,
            'seo_description' => $this->translated('seo_description'),
            'seo_description_translations' => $this->seo_description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
