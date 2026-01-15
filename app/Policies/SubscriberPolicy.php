<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Subscriber;

class SubscriberPolicy
{
public function viewAny(User $user): bool
{
    if ($user->isSuperAdmin()) {
        return true;
    }

    return (int) $user->status === 1 && $user->isStaff();
}




    public function view(User $user, Subscriber $subscriber): bool
    {
        return true; //$user->isStaff() || $subscriber->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isStaff(); // or allow member if needed
    }

    public function update(User $user, Subscriber $subscriber): bool
    {
        // Many systems let member update only limited fields; you can restrict later in Filament form.
        return $user->isStaff() || $subscriber->user_id === $user->id;
    }

    public function delete(User $user, Subscriber $subscriber): bool
    {
        return $user->isStaff();
    }
}
