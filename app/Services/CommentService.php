<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class CommentService
{
    private const DEFAULT_LIMIT = 10;

    public function __construct() {}

    public function getCommentsForPost(Post $post, ?int $limit = null): LengthAwarePaginator
    {
        return $post->comments()
            ->with('author')
            ->latest()
            ->paginate($limit ?? static::DEFAULT_LIMIT);
    }

    public function createCommentForPost(Post $post, User $user, array $data): Comment
    {
        return $post->comments()->create([
            'body' => $data['body'],
            'author_id' => $user->id,
        ]);
    }

    public function updateComment(Comment $comment, array $data): Comment
    {
        $comment->update($data);

        return $comment;
    }

    public function deleteComment(Comment $comment): void
    {
        $comment->delete();
    }
}
