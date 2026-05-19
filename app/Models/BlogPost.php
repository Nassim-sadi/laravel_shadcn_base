<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use HasFactory, HasTranslatedAttributes, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title', 'slug', 'excerpt', 'is_published', 'featured', 'user_id', 'category_id', 'image_id',
    ];

    protected $casts = [
        'title' => 'array',
        'excerpt' => 'array',
        'is_published' => 'boolean',
        'featured' => 'boolean',
        'category_id' => 'integer',
        'image_id' => 'integer',
        'deleted_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function image()
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    public function tags()
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag')->withTimestamps();
    }

    public function body()
    {
        return $this->hasOne(BlogPostBody::class);
    }
}