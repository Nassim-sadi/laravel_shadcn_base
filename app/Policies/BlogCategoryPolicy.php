<?php

namespace App\Policies;

use App\Models\BlogCategory;
use App\Models\User;

class BlogCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('blogs.view');
    }

    public function view(User $user, BlogCategory $blogCategory): bool
    {
        return $user->hasPermissionTo('blogs.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('blogs.create');
    }

    public function update(User $user, BlogCategory $blogCategory): bool
    {
        return $user->hasPermissionTo('blogs.edit');
    }

    public function delete(User $user, BlogCategory $blogCategory): bool
    {
        return $user->hasPermissionTo('blogs.delete');
    }
}
