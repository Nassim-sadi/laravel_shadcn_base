<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogCategory extends Model
{
    use HasTranslatedAttributes, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_id',
        'parent_id',
        'order',
        'is_active',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function image()
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    public function parent()
    {
        return $this->belongsTo(CatalogCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CatalogCategory::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(CatalogProduct::class, 'category_id');
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
