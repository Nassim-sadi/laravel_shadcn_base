<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Model;

class CatalogAttributeValue extends Model
{
    use HasTranslatedAttributes;

    protected $fillable = [
        'attribute_id',
        'value',
        'order',
    ];

    protected $casts = [
        'value' => 'array',
        'order' => 'integer',
    ];

    public function attribute()
    {
        return $this->belongsTo(CatalogAttribute::class, 'attribute_id');
    }
}
