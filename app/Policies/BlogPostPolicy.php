<?php

namespace App\Policies;

use App\Models\BlogPost;
use App\Models\User;

class BlogPostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('blogs.view');
    }

    public function view(User $user, BlogPost $blogPost): bool
    {
        return $user->hasPermissionTo('blogs.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('blogs.create');
    }

    public function update(User $user, BlogPost $blogPost): bool
    {
        return $user->hasPermissionTo('blogs.edit');
    }

    public function delete(User $user, BlogPost $blogPost): bool
    {
        return $user->hasPermissionTo('blogs.delete');
    }
}
