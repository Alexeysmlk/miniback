<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contentCreators = User::role([RoleEnum::ADMIN, RoleEnum::EDITOR])->get();

        if ($contentCreators->isEmpty()) {
            $this->command->warn('No users with admin or editor roles found. Skipping PostSeeder.');

            return;
        }

        foreach ($contentCreators as $author) {
            Post::factory()
                ->count(rand(3, 10))
                ->for($author, 'author')
                ->create();
        }
    }
}
