<?php

namespace App\Policies;

use App\Models\CatalogTag;
use App\Models\User;

class CatalogTagPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('catalog.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('catalog.create');
    }

    public function delete(User $user, CatalogTag $tag): bool
    {
        return $user->hasPermissionTo('catalog.delete');
    }
}
