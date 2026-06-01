<?php

namespace App\Policies;

use App\Models\User;

class BookingServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('booking_services.view');
    }

    public function view(User $user): bool
    {
        return $user->hasPermissionTo('booking_services.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('booking_services.create');
    }

    public function edit(User $user): bool
    {
        return $user->hasPermissionTo('booking_services.edit');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('booking_services.delete');
    }
}
