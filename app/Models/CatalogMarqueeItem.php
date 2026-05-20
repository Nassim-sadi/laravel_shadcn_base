<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogMarqueeItem extends Model
{
    use HasTranslatedAttributes, SoftDeletes;

    protected $fillable = [
        'image_id',
        'text',
        'position',
        'order',
        'is_active',
    ];

    protected $casts = [
        'text' => 'array',
        'position' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
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
