<?php

namespace App\Policies;

use App\Models\EmailTemplate;
use App\Models\User;

class EmailTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('email-templates.view');
    }

    public function view(User $user, EmailTemplate $emailTemplate): bool
    {
        return $user->hasPermissionTo('email-templates.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('email-templates.create');
    }

    public function update(User $user, EmailTemplate $emailTemplate): bool
    {
        return $user->hasPermissionTo('email-templates.edit');
    }

    public function delete(User $user, EmailTemplate $emailTemplate): bool
    {
        return $user->hasPermissionTo('email-templates.delete');
    }
}
