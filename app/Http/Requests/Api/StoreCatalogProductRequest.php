<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'name.*' => 'string|max:255',
            'description' => 'nullable|array',
            'description.*' => 'string|max:1000',
            'body' => 'nullable|array',
            'body.*' => 'string',
            'sku' => 'nullable|string|max:100',
            'price_display' => 'nullable|numeric|min:0',
            'badges' => 'nullable|array',
            'badges.*' => 'string',
            'category_id' => 'nullable|integer|exists:catalog_categories,id',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'media' => 'nullable|array',
            'media.*.media_id' => 'nullable|integer|exists:media,id',
            'media.*.type' => 'required_with:media|string|in:image,video',
            'media.*.video_url' => 'nullable|url',
            'media.*.thumbnail_path' => 'nullable|string|max:500',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'integer|exists:catalog_tags,id',
            'attributes' => 'nullable|array',
            'attributes.*.attribute_id' => 'required|integer|exists:catalog_attributes,id',
            'attributes.*.attribute_value_id' => 'nullable|integer|exists:catalog_attribute_values,id',
            'attributes.*.custom_text' => 'nullable|string|max:500',
        ];
    }
}
