<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasTranslatedAttributes, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'client',
        'image',
        'image_id',
        'url',
        'technologies',
        'order',
        'is_active',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'client' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
        'technologies' => 'array',
        'seo_title' => 'array',
        'seo_description' => 'array',
        'seo_keywords' => 'array',
    ];

    public function image()
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at');
    }
}
