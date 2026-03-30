<?php

namespace App\Http\Requests\Storefront\Product;

use App\Http\Requests\BaseFormRequest;

class QuickviewRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'min:1', 'exists:products,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Thiếu sản phẩm cần xem nhanh.',
            'product_id.integer' => 'Mã sản phẩm xem nhanh không hợp lệ.',
            'product_id.min' => 'Mã sản phẩm xem nhanh không hợp lệ.',
            'product_id.exists' => 'Sản phẩm cần xem nhanh không tồn tại.',
        ];
    }
}
