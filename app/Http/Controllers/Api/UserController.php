<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\UpdateRoleRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    public function updateRole(UpdateRoleRequest $request, User $user): UserResource
    {
        $user = $this->userService->updateUserRole(
            $user,
            $request->validated('role')
        );

        return UserResource::make($user);
    }
}
