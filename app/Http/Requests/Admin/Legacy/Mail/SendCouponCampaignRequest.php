<?php

namespace App\Http\Requests\Admin\Legacy\Mail;

use App\Http\Requests\BaseFormRequest;

class SendCouponCampaignRequest extends BaseFormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'coupon_time' => $this->route('coupon_time'),
            'coupon_condition' => $this->route('coupon_condition'),
            'coupon_number' => $this->route('coupon_number'),
            'coupon_code' => $this->route('coupon_code'),
        ]);
    }

    public function rules(): array
    {
        return [
            'coupon_time' => ['required', 'integer', 'min:1'],
            'coupon_condition' => ['required', 'in:1,2'],
            'coupon_number' => ['required', 'numeric', 'min:0'],
            'coupon_code' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'coupon_time.required' => 'Thiếu số lượt sử dụng của mã khuyến mãi.',
            'coupon_time.integer' => 'Số lượt sử dụng phải là số nguyên.',
            'coupon_time.min' => 'Số lượt sử dụng phải lớn hơn 0.',
            'coupon_condition.required' => 'Thiếu loại mã khuyến mãi.',
            'coupon_condition.in' => 'Loại mã khuyến mãi không hợp lệ.',
            'coupon_number.required' => 'Thiếu giá trị mã khuyến mãi.',
            'coupon_number.numeric' => 'Giá trị mã khuyến mãi phải là số.',
            'coupon_number.min' => 'Giá trị mã khuyến mãi không được âm.',
            'coupon_code.required' => 'Thiếu mã khuyến mãi.',
            'coupon_code.max' => 'Mã khuyến mãi không được vượt quá 50 ký tự.',
        ];
    }
}
