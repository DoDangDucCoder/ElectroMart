<?php

namespace App\Http\Requests\Catalog;

use App\Http\Requests\BaseFormRequest;

class ReorderCategoryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'ordered_ids' => ['required', 'array', 'min:1'],
            'ordered_ids.*' => ['integer', 'exists:product_categories,id'],
        ];
    }
}
