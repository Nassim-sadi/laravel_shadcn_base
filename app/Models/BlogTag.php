<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogTag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'slug'];

    protected $casts = ['deleted_at' => 'datetime'];

    public function posts()
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_tag')->withTimestamps();
    }
}