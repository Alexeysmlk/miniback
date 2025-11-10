<?php

namespace App\Services;

use App\DTO\Post\IndexOptionsDTO;
use App\Models\Post;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    public const DEFAULT_SORT_BY = 'published_at';

    public const DEFAULT_SORT_DIR = 'desc';

    public const ALLOWED_SORT_FIELDS = [
        'published_at',
        'title',
        'created_at',
    ];

    public const POSTS_PER_PAGE = 10;

    public function getPaginatedPosts(IndexOptionsDTO $optionsDTO, ?int $perPage = null): LengthAwarePaginator
    {
        return Post::query()
            ->when($optionsDTO->searchTerm, fn ($query) => $query->search($optionsDTO->searchTerm))
            ->when($optionsDTO->status, fn ($query) => $query->status($optionsDTO->status))
            ->when(
                $optionsDTO->publishedFrom || $optionsDTO->publishedTo,
                fn ($query) => $query->filterByDateRange($optionsDTO->publishedFrom, $optionsDTO->publishedTo)
            )
            ->with('author', 'latestComment.author')
            ->withCount('comments')
            ->orderBy($optionsDTO->sortBy, $optionsDTO->sortDir)
            ->paginate($perPage ?? static::POSTS_PER_PAGE);
    }

    public function createPost(array $data, User $author): Post
    {
        return Post::create(array_merge($data, [
            'author_id' => $author->id,
        ]));
    }

    public function updatePost(Post $post, array $data): Post
    {
        $post->update($data);

        return $post;
    }

    public function deletePost(Post $post): void
    {
        $post->delete();
    }

    public function prepareForShow(Post $post): Post
    {
        $post->loadCount('comments');
        $post->load('author');

        return $post;
    }
}
