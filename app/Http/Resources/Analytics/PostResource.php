<?php

namespace App\Http\Resources\Analytics;

use App\Http\Resources\AuthorResource;
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
            'total_comments' => $this->whenCounted('comments'),
            'author' => $this->whenLoaded('author', function () {
                return AuthorResource::make($this->author);
            }),
        ];
    }
}
