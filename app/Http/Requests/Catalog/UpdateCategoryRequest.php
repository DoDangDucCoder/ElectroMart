<?php

namespace App\Http\Requests\Catalog;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $categoryId = $this->route('category')->id ?? null;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('product_categories', 'slug')->ignore($categoryId),
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('product_categories', 'code')->ignore($categoryId),
            ],
            'parent_id' => ['nullable', 'integer', 'exists:product_categories,id', 'not_in:'.$categoryId],
            'description' => ['nullable', 'string'],
            'thumbnail_path' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'display_order' => ['sometimes', 'integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
