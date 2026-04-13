<?php

namespace App\Http\Requests\Admin\Legacy\Coupon;

use App\Http\Requests\BaseFormRequest;
use Carbon\Carbon;

class StoreCouponRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'coupon_name' => ['required', 'string', 'max:255'],
            'coupon_date_start' => ['required', 'date_format:d/m/Y'],
            'coupon_date_end' => ['required', 'date_format:d/m/Y'],
            'coupon_code' => ['required', 'string', 'max:50'],
            'coupon_time' => ['required', 'integer', 'min:1'],
            'coupon_condition' => ['required', 'in:1,2'],
            'coupon_number' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'coupon_name.required' => 'Vui lòng nhập tên mã giảm giá.',
            'coupon_name.max' => 'Tên mã giảm giá không được vượt quá 255 ký tự.',
            'coupon_date_start.required' => 'Vui lòng nhập ngày bắt đầu.',
            'coupon_date_start.date_format' => 'Ngày bắt đầu phải theo định dạng DD/MM/YYYY.',
            'coupon_date_end.required' => 'Vui lòng nhập ngày kết thúc.',
            'coupon_date_end.date_format' => 'Ngày kết thúc phải theo định dạng DD/MM/YYYY.',
            'coupon_code.required' => 'Vui lòng nhập mã giảm giá.',
            'coupon_code.max' => 'Mã giảm giá không được vượt quá 50 ký tự.',
            'coupon_time.required' => 'Vui lòng nhập số lượng mã.',
            'coupon_time.integer' => 'Số lượng mã phải là số nguyên.',
            'coupon_time.min' => 'Số lượng mã phải lớn hơn 0.',
            'coupon_condition.required' => 'Vui lòng chọn loại mã giảm giá.',
            'coupon_condition.in' => 'Loại mã giảm giá không hợp lệ.',
            'coupon_number.required' => 'Vui lòng nhập giá trị giảm.',
            'coupon_number.numeric' => 'Giá trị giảm phải là số.',
            'coupon_number.min' => 'Giá trị giảm không được âm.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $startDate = Carbon::createFromFormat('d/m/Y', (string) $this->input('coupon_date_start'));
            $endDate = Carbon::createFromFormat('d/m/Y', (string) $this->input('coupon_date_end'));

            if ($endDate->lt($startDate)) {
                $validator->errors()->add('coupon_date_end', 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.');
            }
        });
    }
}
