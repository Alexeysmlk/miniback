<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function __construct() {}

    public function updateUserRole(User $user, string $role): User
    {
        $user->syncRoles([$role]);

        return $user;
    }
}
