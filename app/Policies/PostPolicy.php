<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\Post;
use App\Models\User;

class PostPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::VIEW_POSTS);
    }

    public function view(User $user, Post $post): bool
    {
        return $user->can(PermissionEnum::VIEW_POSTS);
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::CREATE_POSTS->value);
    }

    public function update(User $user, Post $post): bool
    {
        return $user->can(PermissionEnum::EDIT_POSTS)
            && $user->id === $post->author_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->can(PermissionEnum::DELETE_POSTS)
            && $user->id === $post->author_id;
    }
}
