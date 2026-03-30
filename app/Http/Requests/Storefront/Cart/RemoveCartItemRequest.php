<?php

namespace App\Http\Requests\Storefront\Cart;

use App\Http\Requests\BaseFormRequest;

class RemoveCartItemRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Thiếu sản phẩm cần xóa khỏi giỏ hàng.',
            'id.integer' => 'Mã sản phẩm cần xóa không hợp lệ.',
        ];
    }
}
