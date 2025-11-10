<?php

namespace App\Http\Requests\Api\Post;

use App\DTO\Post\IndexOptionsDTO;
use App\Enums\PostStatusEnum;
use App\Services\PostService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sort_by' => [
                'sometimes',
                'string',
                Rule::in(PostService::ALLOWED_SORT_FIELDS),
            ],
            'sort_dir' => [
                'sometimes',
                'string',
                Rule::in(['asc', 'desc']),
            ],
            'q' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'status' => [
                'sometimes',
                'string',
                Rule::enum(PostStatusEnum::class),
            ],
            'published_at' => [
                'sometimes',
                'array',
            ],
            'published_at.from' => [
                'nullable',
                'date',
            ],
            'published_at.to' => [
                'nullable',
                'date',
            ],
        ];
    }

    public function toDTO(): IndexOptionsDTO
    {
        $validated = $this->validated();
        $status = $validated['status'] ?? null;

        return new IndexOptionsDTO(
            sortBy: $validated['sort_by'] ?? PostService::DEFAULT_SORT_BY,
            sortDir: $validated['sort_dir'] ?? PostService::DEFAULT_SORT_DIR,
            searchTerm: $validated['q'] ?? null,
            status: $status ? PostStatusEnum::from($status) : null,
            publishedFrom: $validated['published_at']['from'] ?? null,
            publishedTo: $validated['published_at']['to'] ?? null,
        );

    }
}
