<?php

namespace App\Policies;

use App\Models\CatalogMarqueeItem;
use App\Models\User;

class CatalogMarqueeItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('catalog.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('catalog.create');
    }

    public function update(User $user, CatalogMarqueeItem $item): bool
    {
        return $user->hasPermissionTo('catalog.edit');
    }

    public function delete(User $user, CatalogMarqueeItem $item): bool
    {
        return $user->hasPermissionTo('catalog.delete');
    }
}
