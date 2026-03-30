<?php

namespace App\Http\Requests\Storefront\Product;

use App\Http\Requests\BaseFormRequest;

class InsertRatingRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'min:1', 'exists:products,id'],
            'index' => ['required', 'integer', 'between:1,5'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Thiếu sản phẩm cần đánh giá.',
            'product_id.integer' => 'Mã sản phẩm đánh giá không hợp lệ.',
            'product_id.min' => 'Mã sản phẩm đánh giá không hợp lệ.',
            'product_id.exists' => 'Sản phẩm cần đánh giá không tồn tại.',
            'index.required' => 'Vui lòng chọn số sao đánh giá.',
            'index.integer' => 'Số sao đánh giá không hợp lệ.',
            'index.between' => 'Số sao đánh giá phải nằm trong khoảng từ 1 đến 5.',
        ];
    }
}
