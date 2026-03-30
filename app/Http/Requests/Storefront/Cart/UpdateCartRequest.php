<?php

namespace App\Http\Requests\Storefront\Cart;

use App\Http\Requests\BaseFormRequest;

class UpdateCartRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'cart_qty' => ['required', 'array', 'min:1'],
            'cart_qty.*' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'cart_qty.required' => 'Không có dữ liệu giỏ hàng để cập nhật.',
            'cart_qty.array' => 'Dữ liệu cập nhật giỏ hàng không hợp lệ.',
            'cart_qty.min' => 'Không có dòng sản phẩm nào để cập nhật.',
            'cart_qty.*.required' => 'Số lượng sản phẩm không được để trống.',
            'cart_qty.*.integer' => 'Số lượng sản phẩm phải là số nguyên.',
            'cart_qty.*.min' => 'Số lượng sản phẩm không được nhỏ hơn 0.',
        ];
    }
}
