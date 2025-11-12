<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = Post::all();

        $commentAuthors = User::role([RoleEnum::ADMIN->value, RoleEnum::EDITOR->value])->get();

        if ($posts->isEmpty() || $commentAuthors->isEmpty()) {
            $this->command->warn('No posts or potential comment authors found. Skipping CommentSeeder.');

            return;
        }

        foreach ($posts as $post) {
            Comment::factory()
                ->count(rand(0, 20))
                ->for($post)
                ->for($commentAuthors->random(), 'author')
                ->create();
        }
    }
}
