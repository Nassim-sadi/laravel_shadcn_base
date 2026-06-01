<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('booking.view');
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->hasPermissionTo('booking.view');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function edit(User $user): bool
    {
        return $user->hasPermissionTo('booking.edit');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('booking.delete');
    }

    public function confirm(User $user): bool
    {
        return $user->hasPermissionTo('booking.confirm');
    }
}
