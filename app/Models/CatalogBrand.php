<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogBrand extends Model
{
    use HasTranslatedAttributes, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'logo_id',
        'description',
        'website',
        'is_active',
        'order',
    ];

    protected $casts = [
        'name' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function logo()
    {
        return $this->belongsTo(Media::class, 'logo_id');
    }

    public function products()
    {
        return $this->hasMany(CatalogProduct::class, 'brand_id');
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
