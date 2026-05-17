<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use HasFactory, HasTranslatedAttributes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name', 
        'slug', 
        'description', 
        'is_published'
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'is_published' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // Relationship: A category has many posts
    public function posts()
    {
        return $this->hasMany(BlogPost::class, 'category_id');
    }
}