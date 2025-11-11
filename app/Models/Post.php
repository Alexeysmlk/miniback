<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PostStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'body',
        'author_id',
        'published_at',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'status' => PostStatusEnum::class,
        ];
    }

    public function scopeSearch(Builder $query, string $term): void
    {
        $term = '%'.$term.'%';

        $query->where(function (Builder $query) use ($term) {
            $query->whereLike('title', $term)
                ->orWhereLike('body', $term);
        });
    }

    public function scopeStatus(Builder $query, PostStatusEnum $status): void
    {
        $query->where('status', $status);
    }

    public function scopeFilterByDateRange(Builder $query, ?string $from, ?string $to): void
    {
        $query->when($from, function (Builder $query) use ($from) {
            $query->whereDate('published_at', '>=', $from);
        })->when($to, function (Builder $query) use ($to) {
            $query->whereDate('published_at', '<=', $to);
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function latestComment(): HasOne
    {
        return $this->hasOne(Comment::class)->latestOfMany();
    }
}
