<?php

namespace Tests\Feature\Post;

use App\Enums\RoleEnum;
use App\Models\Post;
use App\Models\User;
use App\Services\PostService;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostIndexTest extends TestCase
{
    use RefreshDatabase;

    private User $editor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);

        $this->editor = User::factory()->create();
        $this->editor->assignRole(RoleEnum::EDITOR);

        Sanctum::actingAs($this->editor);
    }

    public function test_it_returns_a_paginated_list_of_post(): void
    {
        Post::factory()->count(20)->for($this->editor, 'author')->create();

        $response = $this->getJson(route('posts.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'body',
                    'author',
                ],
            ],
            'links',
            'meta',
        ]);
        $response->assertJsonCount(PostService::POSTS_PER_PAGE, 'data');
        $response->assertJsonPath('meta.total', 20);
    }

    public function test_it_can_filter_posts_by_status(): void
    {
        Post::factory()->count(5)->for($this->editor, 'author')->create([
            'status' => 'published',
        ]);
        Post::factory()->count(3)->for($this->editor, 'author')->create([
            'status' => 'draft',
        ]);

        $response = $this->getJson(route('posts.index', ['status' => 'published']));

        $response->assertOk();
        $response->assertJsonCount(5, 'data');
        $response->assertJsonPath('meta.total', 5);
        $response->assertJsonMissing(['status' => 'draft']);
    }

    public function test_it_can_search_posts_by_query(): void
    {
        $postToFind = Post::factory()->for($this->editor, 'author')->create([
            'title' => 'Unique Post Title',
            'body' => 'This is a unique body content for testing search functionality.',
        ]);
        Post::factory()->for($this->editor, 'author')->create([
            'title' => 'Another Post',
            'body' => 'Some other content that does not match the search query.',
        ]);

        $response = $this->getJson(route('posts.index', ['q' => 'unique']));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $postToFind->id);
    }

    public function test_it_sorts_posts_by_published_at_descending_by_default(): void
    {
        $oldPost = Post::factory()->for($this->editor, 'author')->create([
            'published_at' => now()->subDay(),
        ]);
        $newPost = Post::factory()->for($this->editor, 'author')->create([
            'published_at' => now(),
        ]);

        $response = $this->getJson(route('posts.index'));

        $response->assertOk();
        $response->assertJsonPath('data.0.id', $newPost->id);
        $response->assertJsonPath('data.1.id', $oldPost->id);
    }

    public function test_it_can_sort_posts_by_title_ascending(): void
    {
        $postB = Post::factory()->for($this->editor, 'author')->create(['title' => 'B Post']);
        $postA = Post::factory()->for($this->editor, 'author')->create(['title' => 'A Post']);

        $response = $this->getJson(route('posts.index', ['sort_by' => 'title', 'sort_dir' => 'asc']));

        $response->assertOk();
        $response->assertJsonPath('data.0.id', $postA->id);
        $response->assertJsonPath('data.1.id', $postB->id);
    }
}
