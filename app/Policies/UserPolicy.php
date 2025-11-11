<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\User;

class UserPolicy extends BasePolicy
{
    public function updateRole(User $user, User $targetUser): bool
    {
        return $user->can(PermissionEnum::MANAGE_ROLES)
            && $user->id !== $targetUser->id;
    }
}
