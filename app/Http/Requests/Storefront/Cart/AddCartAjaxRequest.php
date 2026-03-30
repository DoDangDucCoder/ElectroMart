<?php

namespace App\Http\Requests\Storefront\Cart;

use App\Http\Requests\BaseFormRequest;

class AddCartAjaxRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'id_product' => ['required', 'integer'],
            'id_attribute' => ['nullable', 'integer'],
            'cart_product_qty' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_product.required' => 'Thiếu sản phẩm cần thêm vào giỏ hàng.',
            'id_product.integer' => 'Mã sản phẩm không hợp lệ.',
            'id_attribute.integer' => 'Mã thuộc tính không hợp lệ.',
            'cart_product_qty.integer' => 'Số lượng sản phẩm không hợp lệ.',
            'cart_product_qty.min' => 'Số lượng sản phẩm phải lớn hơn 0.',
        ];
    }
}
