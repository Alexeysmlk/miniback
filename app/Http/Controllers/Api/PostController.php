<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Post\IndexRequest;
use App\Http\Requests\Api\Post\StoreRequest;
use App\Http\Requests\Api\Post\UpdateRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    ) {}

    public function index(IndexRequest $request): JsonResource
    {
        $posts = $this->postService->getPaginatedPosts($request->toDTO());

        return PostResource::collection($posts);
    }

    public function store(StoreRequest $request): JsonResource
    {
        $post = $this->postService->createPost(
            $request->validated(),
            $request->user()
        );

        return PostResource::make($post);
    }

    public function show(Post $post): JsonResource
    {
        return PostResource::make(
            $this->postService->prepareForShow($post)
        );
    }

    public function update(UpdateRequest $request, Post $post): JsonResource
    {
        $post = $this->postService->updatePost(
            $post,
            $request->validated(),
        );

        return PostResource::make($post);
    }

    public function destroy(Post $post): JsonResponse
    {
        $this->postService->deletePost($post);

        return response()
            ->json(null, Response::HTTP_NO_CONTENT);
    }
}
