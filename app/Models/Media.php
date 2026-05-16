<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Media extends Model
{
    protected $fillable = [
        'name',
        'file_name',
        'original_name',
        'mime_type',
        'extension',
        'size',
        'disk',
        'path',
        'thumbnail_path',
        'alt_text',
        'caption',
        'description',
        'folder',
        'width',
        'height',
        'created_by',
    ];

    protected $casts = [
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    protected $appends = ['url', 'thumbnail_url'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getUrlAttribute(): ?string
    {
        if (! $this->path) {
            return null;
        }

        if (Str::startsWith($this->path, ['http://', 'https://', '/'])) {
            return $this->path;
        }

        return Storage::disk($this->disk)->url($this->path);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (! $this->thumbnail_path) {
            return $this->url;
        }

        if (Str::startsWith($this->thumbnail_path, ['http://', 'https://', '/'])) {
            return $this->thumbnail_path;
        }

        return Storage::disk($this->disk)->url($this->thumbnail_path);
    }

    public function isImage(): bool
    {
        return Str::startsWith($this->mime_type, 'image/');
    }

    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    public function scopeByType($query, string $mimeType)
    {
        return $query->where('mime_type', 'like', "{$mimeType}/%");
    }

    public function scopeInFolder($query, string $folder)
    {
        return $query->where('folder', $folder);
    }
}
