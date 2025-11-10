<?php

declare(strict_types=1);

namespace App\Enums;

enum PostStatusEnum: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}
