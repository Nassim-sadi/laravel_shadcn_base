<?php

namespace App\Policies;

use App\Models\User;

class CatalogBrandPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('catalog.view');
    }

    public function view(User $user): bool
    {
        return $user->hasPermissionTo('catalog.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('catalog.create');
    }

    public function edit(User $user): bool
    {
        return $user->hasPermissionTo('catalog.edit');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('catalog.delete');
    }
}
