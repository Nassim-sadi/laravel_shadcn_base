<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPostBody extends Model
{
    use HasFactory, HasTranslatedAttributes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'blog_post_id', 
        'body'
    ];

    protected $casts = [
        'body' => 'array',
    ];

    /**
     * Relationship: A body belongs to a blog post.
     */
    public function blogPost()
    {
        return $this->belongsTo(BlogPost::class);
    }
}