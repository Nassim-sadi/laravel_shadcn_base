<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use HasTranslatedAttributes, SoftDeletes;

    protected $fillable = [
        'name',
        'position',
        'company',
        'content',
        'image_id',
        'rating',
        'is_active',
        'order',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'name' => 'array',
        'position' => 'array',
        'company' => 'array',
        'content' => 'array',
        'seo_title' => 'array',
        'seo_description' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
        'rating' => 'integer',
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