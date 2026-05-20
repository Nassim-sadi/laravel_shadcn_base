<?php

namespace App\Policies;

use App\Models\CatalogAttribute;
use App\Models\User;

class CatalogAttributePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('catalog.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('catalog.create');
    }

    public function update(User $user, CatalogAttribute $attribute): bool
    {
        return $user->hasPermissionTo('catalog.edit');
    }

    public function delete(User $user, CatalogAttribute $attribute): bool
    {
        return $user->hasPermissionTo('catalog.delete');
    }
}
