<?php

namespace App\Http\Requests\Storefront\Home;

use App\Http\Requests\BaseFormRequest;

class ProductTabsRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'cate_id' => ['required', 'integer', 'min:1', 'exists:product_categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'cate_id.required' => 'Thiếu danh mục cần tải sản phẩm.',
            'cate_id.integer' => 'Mã danh mục tab sản phẩm không hợp lệ.',
            'cate_id.min' => 'Mã danh mục tab sản phẩm không hợp lệ.',
            'cate_id.exists' => 'Danh mục tab sản phẩm không tồn tại.',
        ];
    }
}
