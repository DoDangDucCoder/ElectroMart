<?php

namespace App\Http\Requests\Admin\Legacy\Products;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Facades\Schema;

class StoreProductAttributeRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $productRules = ['required', 'integer'];
        $attributeRules = ['required', 'integer'];
        $nameRules = ['required', 'string', 'max:255'];

        if (Schema::hasTable('tbl_product')) {
            $productRules[] = 'exists:tbl_product,product_id';
        }

        if (Schema::hasTable('tbl_attribute')) {
            $attributeRules[] = 'exists:tbl_attribute,id';
        }

        if (Schema::hasTable('tbl_product_attribute')) {
            $nameRules[] = 'unique:tbl_product_attribute,name';
        }

        return [
            'product_id' => $productRules,
            'name' => $nameRules,
            'price' => ['required', 'numeric', 'min:1'],
            'quantity' => ['required', 'numeric', 'min:1'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'description' => ['required', 'string'],
            'attribute_id' => $attributeRules,
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Thiếu sản phẩm cần thêm thuộc tính.',
            'product_id.exists' => 'Sản phẩm không tồn tại.',
            'name.required' => 'Vui lòng nhập tên thuộc tính sản phẩm.',
            'name.unique' => 'Tên thuộc tính sản phẩm đã tồn tại.',
            'price.required' => 'Vui lòng nhập giá thuộc tính.',
            'price.numeric' => 'Giá thuộc tính không hợp lệ.',
            'price.min' => 'Giá thuộc tính phải lớn hơn 0.',
            'quantity.required' => 'Vui lòng nhập số lượng thuộc tính.',
            'quantity.numeric' => 'Số lượng thuộc tính không hợp lệ.',
            'quantity.min' => 'Số lượng thuộc tính phải lớn hơn 0.',
            'image.required' => 'Vui lòng chọn ảnh thuộc tính.',
            'image.image' => 'Tệp tải lên phải là hình ảnh hợp lệ.',
            'image.mimes' => 'Ảnh thuộc tính phải có định dạng jpeg, png, jpg, gif hoặc webp.',
            'image.max' => 'Ảnh thuộc tính không được vượt quá 2MB.',
            'description.required' => 'Vui lòng nhập mô tả thuộc tính.',
            'attribute_id.required' => 'Vui lòng chọn thuộc tính nền.',
            'attribute_id.exists' => 'Thuộc tính nền không tồn tại.',
        ];
    }
}
