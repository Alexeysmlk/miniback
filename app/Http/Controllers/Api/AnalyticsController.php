<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Analytics\CommentStatRequest;
use App\Http\Requests\Api\Analytics\PostStatsRequest;
use App\Http\Resources\Analytics\PostResource;
use App\Http\Resources\Analytics\RoleResource;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsService $analyticsService,
    ) {}

    public function posts(PostStatsRequest $request)
    {
        return response()->json([
            'count_by_status' => $this->analyticsService->getPostCountByStatus(),
            'count_by_period' => $this->analyticsService->getPostCountByPeriod(
                $request->validated('period')
            ),
            'average_comments_per_post' => $this->analyticsService->getAverageCommentsPerPost(),
            'top_5_posts_by_comments' => PostResource::collection(
                $this->analyticsService->getTopCommentedPosts(5)
            ),
        ]);
    }

    public function comments(CommentStatRequest $request)
    {
        return response()->json([
            'comments' => $this->analyticsService->getTotalCommentsCount(),
            'comments_by_period' => $this->analyticsService->getCommentCountByPeriod(
                $request->validated('period')
            ),
            'activity' => $this->analyticsService->getCommentActivity(
                $request->validated('group_by')
            ),
        ]);
    }

    public function users(Request $request)
    {
        return response()->json([
            'count_by_roles' => RoleResource::collection(
                $this->analyticsService->getUsersCountByRoles()
            ),
            'top_5_active_authors' => $this->analyticsService->getTopAuthors(5),
            'top_5_active_commenters' => $this->analyticsService->getTopCommenters(5),
        ]);
    }
}
