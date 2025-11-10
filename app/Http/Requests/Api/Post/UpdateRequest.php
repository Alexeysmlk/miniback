<?php

namespace App\Http\Requests\Api\Post;

use App\Enums\PostStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
        $postId = $this->route('post')->id;

        return [
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('posts', 'title')->ignore($postId, 'id'),
            ],
            'body' => [
                'sometimes',
                'required',
                'string',
            ],
            'status' => [
                'sometimes',
                'required',
                Rule::enum(PostStatusEnum::class),
            ],
            'published_at' => [
                'nullable',
                'date',
            ],
        ];
    }
}
