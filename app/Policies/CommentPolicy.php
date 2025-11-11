<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\Comment;
use App\Models\User;

class CommentPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::VIEW_COMMENTS);
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::CREATE_COMMENTS);
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->can(PermissionEnum::EDIT_COMMENTS)
            && $user->id === $comment->author_id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->can(PermissionEnum::DELETE_COMMENTS)
            && $user->id === $comment->author_id;
    }
}
