<?php

namespace App\Http\Requests\Storefront\Cart;

use App\Http\Requests\BaseFormRequest;

class UpdateQuickCartRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'session_id' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'session_id.required' => 'Thiếu dòng sản phẩm cần cập nhật.',
            'quantity.required' => 'Vui lòng nhập số lượng cần cập nhật.',
            'quantity.integer' => 'Số lượng cập nhật không hợp lệ.',
            'quantity.min' => 'Số lượng cập nhật phải lớn hơn 0.',
        ];
    }
}
