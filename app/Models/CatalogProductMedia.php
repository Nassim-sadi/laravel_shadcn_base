<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CatalogProductMedia extends Model
{
    protected $fillable = [
        'product_id',
        'media_id',
        'type',
        'video_url',
        'thumbnail_path',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(CatalogProduct::class, 'product_id');
    }

    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (! $this->thumbnail_path) {
            return $this->media?->thumbnail_url ?? $this->media?->url;
        }

        if (str_starts_with($this->thumbnail_path, ['http://', 'https://', '/'])) {
            return $this->thumbnail_path;
        }

        return Storage::disk('public')->url($this->thumbnail_path);
    }
}
