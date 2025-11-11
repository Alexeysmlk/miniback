<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (RoleEnum::cases() as $role) {
            User::factory()->create([
                'name' => Str::ucfirst($role->value).' User',
                'email' => $role->value.'@example.com',
            ])->assignRole($role);
        }
    }
}
