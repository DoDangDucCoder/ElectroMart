<?php

namespace App\Http\Requests\Storefront\Home;

use App\Http\Requests\BaseFormRequest;

class LoadMoreProductsRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['nullable', 'integer', 'min:1', 'exists:products,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.integer' => 'Mốc tải thêm sản phẩm không hợp lệ.',
            'id.min' => 'Mốc tải thêm sản phẩm không hợp lệ.',
            'id.exists' => 'Mốc tải thêm sản phẩm không tồn tại.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $value = $this->input('id');

        if (is_string($value) && trim($value) === '') {
            $value = null;
        }

        $this->merge([
            'id' => $value,
        ]);
    }
}
