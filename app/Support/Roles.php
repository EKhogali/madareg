<?php

namespace App\Support;

use App\Models\User;

class Roles
{
    public const SUPER_ADMIN = 1;
    public const MONITOR     = 2;
    public const SUPERVISOR  = 3;
    public const PARENT      = 4;

    public static function is(User $user, int $role): bool
    {
        return (int) $user->role === $role;
    }

    public static function in(User $user, array $roles): bool
    {
        return in_array((int) $user->role, $roles, true);
    }
}
