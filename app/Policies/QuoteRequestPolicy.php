<?php

namespace App\Policies;

use App\Models\QuoteRequest;
use App\Models\User;

class QuoteRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('catalog.view');
    }

    public function view(User $user, QuoteRequest $quote): bool
    {
        return $user->hasPermissionTo('catalog.view');
    }

    public function update(User $user, QuoteRequest $quote): bool
    {
        return $user->hasPermissionTo('catalog.edit');
    }

    public function delete(User $user, QuoteRequest $quote): bool
    {
        return $user->hasPermissionTo('catalog.delete');
    }
}
