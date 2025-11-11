<?php

namespace App\Enums;

enum PermissionEnum: string
{
    case VIEW_POSTS = 'view posts';
    case CREATE_POSTS = 'create posts';
    case EDIT_POSTS = 'edit posts';
    case DELETE_POSTS = 'delete posts';

    case VIEW_COMMENTS = 'view comments';
    case CREATE_COMMENTS = 'create comments';
    case EDIT_COMMENTS = 'edit comments';
    case DELETE_COMMENTS = 'delete comments';

    case MANAGE_ROLES = 'manage roles';
}
