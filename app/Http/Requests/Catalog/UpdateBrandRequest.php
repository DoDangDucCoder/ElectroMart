<?php

namespace App\Http\Requests\Catalog;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $brandId = $this->route('brand')->id ?? null;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('brands', 'slug')->ignore($brandId),
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('brands', 'code')->ignore($brandId),
            ],
            'description' => ['nullable', 'string'],
            'logo_path' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'display_order' => ['sometimes', 'integer', 'min:0'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
