<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isStaff(); // superadmin/monitor/supervisor can see users list
    }

    public function view(User $user, User $target): bool
    {
        return $this->update($user, $target);
    }

    public function create(User $user): bool
    {
        // only super admin creates users (parents/supervisors/monitors)
        return $user->isSuperAdmin();
    }

    public function update(User $user, User $target): bool
    {
        // Rule: Supervisors can ONLY edit themselves
        // if ($target->isSupervisor()) {
        //     return $user->id === $target->id;
        // }

        // Rule: Super admin cannot edit other super admins
        if ($target->isSuperAdmin()) {
            return $user->id === $target->id;
        }

        // For everyone else (monitor/parent): only super admin
        return $user->isSuperAdmin();
    }

    public function delete(User $user, User $target): bool
    {
        // same logic as update
        return $this->update($user, $target);
    }
}
