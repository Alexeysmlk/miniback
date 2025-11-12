<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    private const CACHE_KEY_POSTS_BY_STATUS = 'analytics:posts:count_by_status';

    private const CACHE_KEY_POSTS_BY_PERIOD = 'analytics:posts:count_by_period';

    private const CACHE_KEY_AVG_COMMENTS_PER_POST = 'analytics:posts:average_comments_per_post';

    private const CACHE_KEY_TOP_COMMENTED_POSTS = 'analytics:posts:top_commented_posts';

    private const CACHE_KEY_COUNT_TOTAL_COMMENTS = 'analytics:comments:total_count';

    private const CACHE_KEY_COUNT_COMMENTS_BY_PERIOD = 'analytics:comments:count_by_period';

    private const CACHE_KEY_COMMENT_ACTIVITY_BY_DAY = 'analytics:comments:activity_by_day_of_week';

    private const CACHE_KEY_COMMENT_ACTIVITY_BY_HOUR = 'analytics:comments:activity_by_hour';

    private const CACHE_TTL = 600;

    public function getPostCountByStatus(): Collection
    {
        return Cache::remember(
            static::CACHE_KEY_POSTS_BY_STATUS,
            static::CACHE_TTL,
            function () {
                return Post::query()
                    ->select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->get();
            }
        );
    }

    public function getPostCountByPeriod(string $period = 'week'): array
    {
        return Cache::remember(
            static::CACHE_KEY_POSTS_BY_PERIOD.':'.$period,
            static::CACHE_TTL,
            function () use ($period) {
                return [
                    'period' => $period,
                    'count' => Post::query()
                        ->where('created_at', '>=', $this->getStartDateFromPeriod($period))
                        ->count(),
                ];
            }
        );
    }

    public function getAverageCommentsPerPost()
    {
        return Cache::remember(
            static::CACHE_KEY_AVG_COMMENTS_PER_POST,
            static::CACHE_TTL,
            function () {
                $postsCount = Post::query()->count();

                if ($postsCount === 0) {
                    return 0;
                }

                return round(Comment::query()->count() / $postsCount, 2);
            }
        );
    }

    public function getTopCommentedPosts(int $limit = 5): Collection
    {
        return Cache::remember(
            static::CACHE_KEY_TOP_COMMENTED_POSTS.':'.$limit,
            static::CACHE_TTL,
            function () use ($limit) {
                return Post::query()
                    ->with('author:id,name,email')
                    ->withCount('comments')
                    ->orderByDesc('comments_count')
                    ->limit($limit)
                    ->get();
            }
        );
    }

    public function getTotalCommentsCount(): int
    {
        return Cache::remember(
            static::CACHE_KEY_COUNT_TOTAL_COMMENTS,
            static::CACHE_TTL,
            function () {
                return Comment::query()->count();
            }
        );
    }

    public function getCommentCountByPeriod(string $period = 'week'): array
    {
        return Cache::remember(
            static::CACHE_KEY_COUNT_COMMENTS_BY_PERIOD.':'.$period,
            static::CACHE_TTL,
            function () use ($period) {
                return [
                    'period' => $period,
                    'count' => Comment::query()
                        ->where('created_at', '>=', $this->getStartDateFromPeriod($period))
                        ->count(),
                ];
            }
        );
    }

    private function getCommentActivityByDayOfWeek(): Collection
    {
        return Cache::remember(
            static::CACHE_KEY_COMMENT_ACTIVITY_BY_DAY,
            static::CACHE_TTL,
            function () {
                $comments = Comment::query()
                    ->where('created_at', '>=', $this->getStartDateFromPeriod('week'))
                    ->select('created_at')
                    ->get();

                return $comments
                    ->groupBy(function ($comment) {
                        return $comment->created_at->format('N');
                    })
                    ->map(function ($group) {
                        return $group->count();
                    })
                    ->sortKeys();
            }
        );
    }

    private function getCommentActivityByHour(): Collection
    {
        return Cache::remember(
            static::CACHE_KEY_COMMENT_ACTIVITY_BY_HOUR,
            static::CACHE_TTL,
            function () {
                $comments = Comment::query()
                    ->where('created_at', '>=', $this->getStartDateFromPeriod('day'))
                    ->select('created_at')
                    ->get();

                return $comments
                    ->groupBy(function ($comment) {
                        return $comment->created_at->format('G');
                    })
                    ->map(function ($group) {
                        return $group->count();
                    })
                    ->sortKeys();
            }
        );
    }

    public function getCommentActivity(string $groupBy): Collection
    {
        return match ($groupBy) {
            'hour' => $this->getCommentActivityByHour(),
            'day' => $this->getCommentActivityByDayOfWeek(),
        };
    }

    private function getStartDateFromPeriod(string $period): Carbon
    {
        return match ($period) {
            'day' => now()->subDay(),
            'month' => now()->subMonth(),
            default => now()->subWeek(),
        };
    }
}
