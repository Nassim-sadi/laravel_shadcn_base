<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('services.view');
    }

    public function view(User $user, Service $service): bool
    {
        return $user->hasPermissionTo('services.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('services.create');
    }

    public function update(User $user, Service $service): bool
    {
        return $user->hasPermissionTo('services.edit');
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->hasPermissionTo('services.delete');
    }
}
