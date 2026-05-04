<?php

namespace App\Http\Requests\Admin\Legacy\Coupon;

use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use Illuminate\Validation\Rule;

class UpdateCouponRequest extends StoreCouponRequest
{
    public function rules(): array
    {
        $couponId = (int) $this->route('coupon_id');

        return array_merge(parent::rules(), [
            'coupon_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique((new Coupon())->getTable(), 'code')->ignore($couponId),
            ],
            'min_order_total' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_per_customer' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'is_stackable' => ['nullable', 'boolean'],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'coupon_code.unique' => 'Mã giảm giá này đã tồn tại.',
            'min_order_total.numeric' => 'Giá trị đơn hàng tối thiểu phải là số.',
            'min_order_total.min' => 'Giá trị đơn hàng tối thiểu không được âm.',
            'max_discount.numeric' => 'Giới hạn giảm tối đa phải là số.',
            'max_discount.min' => 'Giới hạn giảm tối đa không được âm.',
            'usage_per_customer.integer' => 'Số lượt dùng mỗi khách phải là số nguyên.',
            'usage_per_customer.min' => 'Số lượt dùng mỗi khách phải lớn hơn 0.',
        ]);
    }
}
