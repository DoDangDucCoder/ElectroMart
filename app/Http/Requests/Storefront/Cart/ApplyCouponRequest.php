<?php

namespace App\Http\Requests\Storefront\Cart;

use App\Http\Requests\BaseFormRequest;

class ApplyCouponRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'coupon' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'coupon.required' => 'Vui lòng nhập mã giảm giá.',
            'coupon.max' => 'Mã giảm giá không được vượt quá 50 ký tự.',
        ];
    }
}
