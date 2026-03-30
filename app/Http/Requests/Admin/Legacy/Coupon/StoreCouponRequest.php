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
