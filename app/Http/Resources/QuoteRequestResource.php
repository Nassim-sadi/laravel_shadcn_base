<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'message' => $this->message,
            'product_id' => $this->product_id,
            'product' => $this->whenLoaded('product', fn() => new CatalogProductResource($this->product)),
            'is_read' => $this->is_read,
            'replied_at' => $this->replied_at?->toIso8601String(),
            'reply' => $this->reply,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
