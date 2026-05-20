<?php

namespace App\Policies;

use App\Models\CatalogCategory;
use App\Models\User;

class CatalogCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('catalog.view');
    }

    public function view(User $user, CatalogCategory $category): bool
    {
        return $user->hasPermissionTo('catalog.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('catalog.create');
    }

    public function update(User $user, CatalogCategory $category): bool
    {
        return $user->hasPermissionTo('catalog.edit');
    }

    public function delete(User $user, CatalogCategory $category): bool
    {
        return $user->hasPermissionTo('catalog.delete');
    }
}
