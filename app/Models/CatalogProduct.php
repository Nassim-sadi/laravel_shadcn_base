<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogProduct extends Model
{
    use HasTranslatedAttributes, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'body',
        'sku',
        'price_display',
        'badges',
        'category_id',
        'brand_id',
        'is_active',
        'order',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'body' => 'array',
        'badges' => 'array',
        'price_display' => 'decimal:2',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(CatalogCategory::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(CatalogBrand::class, 'brand_id');
    }

    public function media()
    {
        return $this->hasMany(CatalogProductMedia::class, 'product_id')->orderBy('order');
    }

    public function tags()
    {
        return $this->belongsToMany(CatalogTag::class, 'catalog_product_tag', 'product_id', 'tag_id')->withTimestamps();
    }

    public function attributes()
    {
        return $this->belongsToMany(CatalogAttribute::class, 'catalog_product_attribute')
            ->withPivot('attribute_value_id', 'custom_text')
            ->withTimestamps();
    }

    public function quoteRequests()
    {
        return $this->hasMany(QuoteRequest::class, 'product_id');
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
