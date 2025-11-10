<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'author' => $this->whenLoaded('author', function () {
                return AuthorResource::make($this->author);
            }),
            'comments_count' => $this->whenCounted('comments'),
            'latest_comment' => $this->whenLoaded('latestComment', function () {
                return CommentResource::make($this->latestComment);
            }),
        ];
    }
}
