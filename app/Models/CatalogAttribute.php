<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Model;

class CatalogAttribute extends Model
{
    use HasTranslatedAttributes;

    protected $fillable = [
        'name',
        'slug',
        'type',
    ];

    protected $casts = [
        'name' => 'array',
    ];

    public function values()
    {
        return $this->hasMany(CatalogAttributeValue::class, 'attribute_id')->orderBy('order');
    }

    public function products()
    {
        return $this->belongsToMany(CatalogProduct::class, 'catalog_product_attribute')
            ->withPivot('attribute_value_id', 'custom_text')
            ->withTimestamps();
    }
}
