<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmailTemplateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'name' => $this->translated('name'),
            'name_translations' => $this->name,
            'subject' => $this->translated('subject'),
            'subject_translations' => $this->subject,
            'body' => $this->translated('body'),
            'body_translations' => $this->body,
            'variables' => $this->variables,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
