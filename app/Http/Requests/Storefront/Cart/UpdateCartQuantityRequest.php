<?php

namespace App\Http\Requests\Storefront\Cart;

use App\Http\Requests\BaseFormRequest;

class UpdateCartQuantityRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'rowId_cart' => ['required', 'string'],
            'cart_quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'rowId_cart.required' => 'Thiếu dòng sản phẩm cần cập nhật.',
            'cart_quantity.required' => 'Vui lòng nhập số lượng sản phẩm.',
            'cart_quantity.integer' => 'Số lượng sản phẩm không hợp lệ.',
            'cart_quantity.min' => 'Số lượng sản phẩm phải lớn hơn 0.',
        ];
    }
}
