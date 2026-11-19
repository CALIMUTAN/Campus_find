<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function suspend(User $actor, User $target): bool
    {
        return $actor->isAdmin()
            && $actor->isNot($target)
            && ! $this->isLastActiveAdmin($target);
    }

    public function activate(User $actor, User $target): bool
    {
        return $actor->isAdmin();
    }

    public function changeRole(User $actor, User $target): bool
    {
        return $actor->isAdmin()
            && $actor->isNot($target)
            && ! $this->isLastActiveAdmin($target);
    }

    private function isLastActiveAdmin(User $target): bool
    {
        if (! $target->isAdmin()) {
            return false;
        }

        return User::where('role', 'admin')->whereNull('suspended_at')->count() <= 1;
    }
}