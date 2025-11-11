<?php

namespace App\DTO\Post;

use App\Enums\PostStatusEnum;

readonly class IndexOptionsDTO
{
    public function __construct(
        public string $sortBy,
        public string $sortDir,
        public ?string $searchTerm,
        public ?PostStatusEnum $status,
        public ?string $publishedFrom,
        public ?string $publishedTo,
    ) {}
}
