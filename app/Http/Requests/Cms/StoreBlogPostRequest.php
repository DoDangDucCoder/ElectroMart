<?php

namespace App\Http\Requests\Cms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'post_title' => ['required', 'string', 'max:255'],
            'post_slug' => ['nullable', 'string', 'max:255', Rule::unique('blog_posts', 'slug')],
            'post_desc' => ['nullable', 'string'],
            'post_content' => ['nullable', 'string'],
            'post_meta_desc' => ['nullable', 'string'],
            'post_meta_keywords' => ['nullable', 'string', 'max:255'],
            'cate_post_id' => ['nullable', 'integer', 'exists:blog_categories,id'],
            'post_status' => ['nullable', 'in:0,1,published,draft,scheduled,archived'],
            'post_image' => ['nullable', 'image', 'max:4096'],
            'is_featured' => ['nullable', 'boolean'],
        ];
    }
}
