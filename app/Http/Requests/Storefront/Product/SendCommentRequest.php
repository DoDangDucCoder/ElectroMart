<?php

namespace App\Http\Requests\Storefront\Product;

use App\Http\Requests\BaseFormRequest;

class SendCommentRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'min:1', 'exists:products,id'],
            'comment_name' => ['required', 'string', 'max:100'],
            'comment_content' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Thiếu sản phẩm cần bình luận.',
            'product_id.integer' => 'Mã sản phẩm bình luận không hợp lệ.',
            'product_id.min' => 'Mã sản phẩm bình luận không hợp lệ.',
            'product_id.exists' => 'Sản phẩm cần bình luận không tồn tại.',
            'comment_name.required' => 'Vui lòng nhập tên của bạn.',
            'comment_name.max' => 'Tên bình luận không được vượt quá 100 ký tự.',
            'comment_content.required' => 'Vui lòng nhập nội dung bình luận.',
            'comment_content.max' => 'Nội dung bình luận không được vượt quá 2000 ký tự.',
        ];
    }
}
