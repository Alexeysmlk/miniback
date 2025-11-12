<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
        ]);
        $admin->assignRole(RoleEnum::ADMIN);

        $editors = User::factory()->count(3)->create();
        $editors->each(function (User $editor) {
            $editor->assignRole(RoleEnum::EDITOR);
        });

        $viewers = User::factory()->count(10)->create();
        $viewers->each(function (User $viewer) {
            $viewer->assignRole(RoleEnum::VIEWER);
        });
    }
}
