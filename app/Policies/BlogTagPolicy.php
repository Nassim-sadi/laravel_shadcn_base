<?php

namespace App\Policies;

use App\Models\BlogTag;
use App\Models\User;

class BlogTagPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('blogs.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('blogs.create');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('blogs.delete');
    }
}
