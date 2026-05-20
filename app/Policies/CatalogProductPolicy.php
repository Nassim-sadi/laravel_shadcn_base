<?php

namespace App\Policies;

use App\Models\CatalogProduct;
use App\Models\User;

class CatalogProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('catalog.view');
    }

    public function view(User $user, CatalogProduct $product): bool
    {
        return $user->hasPermissionTo('catalog.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('catalog.create');
    }

    public function update(User $user, CatalogProduct $product): bool
    {
        return $user->hasPermissionTo('catalog.edit');
    }

    public function delete(User $user, CatalogProduct $product): bool
    {
        return $user->hasPermissionTo('catalog.delete');
    }
}
