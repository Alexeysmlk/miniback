<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $userData): array
    {
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
        ]);

        $user->assignRole(RoleEnum::VIEWER);

        return [
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken,
        ];
    }

    public function login(array $userData): ?array
    {
        $user = User::where('email', $userData['email'])->first();

        if (! $user || ! Hash::check($userData['password'], $user->password)) {
            return null;
        }

        $user->tokens()->delete();

        return [
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
