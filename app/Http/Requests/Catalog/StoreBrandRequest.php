<?php

namespace App\Http\Requests\Catalog;

use App\Http\Requests\BaseFormRequest;

class StoreBrandRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:brands,slug'],
            'code' => ['nullable', 'string', 'max:50', 'unique:brands,code'],
            'description' => ['nullable', 'string'],
            'logo_path' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'display_order' => ['sometimes', 'integer', 'min:0'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
