<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Comment\StoreRequest;
use App\Http\Requests\Api\Comment\UpdateRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService
    ) {}

    public function index(Post $post): JsonResource
    {
        return CommentResource::collection(
            $this->commentService->getCommentsForPost($post)
        );
    }

    public function store(StoreRequest $request, Post $post): JsonResource
    {
        $comment = $this->commentService->createCommentForPost(
            $post,
            $request->user(),
            $request->validated()
        );

        return CommentResource::make($comment);
    }

    public function update(UpdateRequest $request, Comment $comment)
    {
        $comment = $this->commentService->updateComment(
            $comment,
            $request->validated()
        );

        return CommentResource::make($comment);
    }

    public function destroy(Comment $comment)
    {
        $this->commentService->deleteComment($comment);

        return response()
            ->json(null, Response::HTTP_NO_CONTENT);
    }
}
