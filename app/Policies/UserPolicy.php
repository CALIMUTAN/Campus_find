<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function suspend(User $actor, User $target): bool
    {
        return $actor->isAdmin()
            && $actor->isNot($target)
            && ! $target->isSuperAdmin()
            && ($actor->isSuperAdmin() || ! $target->isAdmin())
            && ! $this->isLastActiveAdmin($target);
    }

    public function activate(User $actor, User $target): bool
    {
        return $actor->isAdmin()
            && ($actor->isSuperAdmin() || ! $target->isAdmin());
    }

    public function changeRole(User $actor, User $target): bool
    {
        return $actor->isAdmin()
            && $actor->isNot($target)
            && ! $target->isSuperAdmin()
            && ! $this->isLastActiveAdmin($target);
    }

    private function isLastActiveAdmin(User $target): bool
    {
        if (! $target->isAdmin()) {
            return false;
        }

        return User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])
            ->whereNull('suspended_at')
            ->count() <= 1;
    }
}