<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\User;

class BasePolicy
{
    public function before(User $user, $ability): ?bool
    {
        if ($user->hasRole(RoleEnum::ADMIN)) {
            return true;
        }

        return null;
    }
}
