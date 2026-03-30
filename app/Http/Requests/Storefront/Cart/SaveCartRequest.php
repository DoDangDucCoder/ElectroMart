<?php

namespace App\Http\Requests\Storefront\Cart;

use App\Http\Requests\BaseFormRequest;

class SaveCartRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'productid_hidden' => ['required', 'integer'],
            'qty' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'productid_hidden.required' => 'Thiếu sản phẩm cần thêm vào giỏ hàng.',
            'productid_hidden.integer' => 'Mã sản phẩm không hợp lệ.',
            'qty.required' => 'Vui lòng nhập số lượng sản phẩm.',
            'qty.integer' => 'Số lượng sản phẩm không hợp lệ.',
            'qty.min' => 'Số lượng sản phẩm phải lớn hơn 0.',
        ];
    }
}
