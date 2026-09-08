<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $target): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $target): bool
    {
        return $user->isAdmin() && ! $target->isProtectedAdmin();
    }

    public function toggleActive(User $user, User $target): bool
    {
        return $user->isAdmin()
            && ! $target->isProtectedAdmin()
            && $user->isNot($target);
    }

    public function toggleAdmin(User $user, User $target): bool
    {
        return $user->isAdmin()
            && ! $target->isProtectedAdmin()
            && $user->isNot($target);
    }

    public function delete(User $user, User $target): bool
    {
        return $user->isAdmin()
            && ! $target->isProtectedAdmin()
            && $user->isNot($target);
    }
}
