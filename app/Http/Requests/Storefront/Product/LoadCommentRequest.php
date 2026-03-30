<?php

namespace App\Http\Requests\Storefront\Product;

use App\Http\Requests\BaseFormRequest;

class LoadCommentRequest extends BaseFormRequest
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
            'product_id.required' => 'Thiếu sản phẩm cần tải bình luận.',
            'product_id.integer' => 'Mã sản phẩm bình luận không hợp lệ.',
            'product_id.min' => 'Mã sản phẩm bình luận không hợp lệ.',
            'product_id.exists' => 'Sản phẩm cần tải bình luận không tồn tại.',
        ];
    }
}
