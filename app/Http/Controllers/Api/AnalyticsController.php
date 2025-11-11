<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Analytics\PostStatsRequest;
use App\Http\Resources\Analytics\PostResource;
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
                $request->validated('period', 'week')
            ),
            'average_comments_per_post' => $this->analyticsService->getAverageCommentsPerPost(),
            'top_5_posts_by_comments' => PostResource::collection(
                $this->analyticsService->getTopCommentedPosts(5)
            ),
        ]);
    }

    public function comments(Request $request)
    {
        return response()->json([
            'message' => 'Comments analytics retrieved successfully',
        ]);
    }

    public function users(Request $request)
    {
        return response()->json([
            'message' => 'Users analytics retrieved successfully',
        ]);
    }
}
