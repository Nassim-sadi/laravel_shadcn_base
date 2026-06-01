<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;

class SettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('settings.view');
    }

    public function view(User $user, Setting $setting): bool
    {
        return $user->hasPermissionTo('settings.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('settings.edit');
    }

    public function update(User $user, Setting $setting = null): bool
    {
        return $user->hasPermissionTo('settings.edit');
    }

    public function delete(User $user, Setting $setting = null): bool
    {
        return $user->hasPermissionTo('settings.edit');
    }
}
