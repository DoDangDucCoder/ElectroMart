<?php

namespace App\Http\Requests\Storefront\Checkout;

use App\Http\Requests\BaseFormRequest;
use App\Infrastructure\Persistence\Eloquent\Geo\VietnamWard;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ConfirmOrderRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'checkout_idempotency_key' => ['nullable', 'string', 'max:64'],
            'shipping_email' => ['required', 'email', 'max:255'],
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_phone' => ['required', 'string', 'max:30', 'regex:/^(0|\+84)(3[2-9]|5[6-9]|7[0|6-9]|8[0-9]|9[0-9])[0-9]{7}$/'],
            'shipping_notes' => ['nullable', 'string', 'max:500'],
            'order_note' => ['nullable', 'string', 'max:500'],
            // Chỉ chấp nhận COD — các phương thức khác chưa được triển khai
            'shipping_method' => ['nullable', 'string', 'max:50', Rule::in(['cod', 'cash'])],
            'payment_method'  => ['nullable', 'string', 'max:50', Rule::in(['cod', 'cash'])],
            'vietnam_province_code' => $this->vietnamProvinceRules(),
            'vietnam_ward_code' => $this->vietnamWardRules(),
            'city_id'         => $this->locationRules('city'),
            'province_id'     => $this->locationRules('province'),
            'ward_id'         => $this->locationRules('ward'),
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_email.required' => 'Vui lòng nhập email nhận hàng.',
            'shipping_email.email' => 'Email nhận hàng không hợp lệ.',
            'shipping_email.max' => 'Email nhận hàng không được vượt quá 255 ký tự.',
            'shipping_name.required' => 'Vui lòng nhập tên người nhận.',
            'shipping_name.max' => 'Tên người nhận không được vượt quá 255 ký tự.',
            'shipping_address.required' => 'Vui lòng nhập địa chỉ nhận hàng.',
            'shipping_address.max' => 'Địa chỉ nhận hàng không được vượt quá 500 ký tự.',
            'shipping_phone.required' => 'Vui lòng nhập số điện thoại nhận hàng.',
            'shipping_phone.max' => 'Số điện thoại nhận hàng không được vượt quá 30 ký tự.',
            'shipping_phone.regex' => 'Số điện thoại không hợp lệ. Vui lòng nhập số di động Việt Nam (VD: 0901234567).',
            'shipping_notes.max' => 'Ghi chú giao hàng không được vượt quá 500 ký tự.',
            'order_note.max' => 'Ghi chú đơn hàng không được vượt quá 500 ký tự.',
            'shipping_method.in' => 'Phương thức thanh toán này chưa được hỗ trợ. Vui lòng chọn thanh toán khi nhận hàng (COD).',
            'payment_method.in' => 'Phương thức thanh toán này chưa được hỗ trợ. Vui lòng chọn thanh toán khi nhận hàng (COD).',
            'city_id.integer' => 'Mã thành phố giao hàng không hợp lệ.',
            'city_id.min' => 'Mã thành phố giao hàng không hợp lệ.',
            'city_id.exists' => 'Thành phố giao hàng không tồn tại.',
            'province_id.integer' => 'Mã quận/huyện giao hàng không hợp lệ.',
            'province_id.min' => 'Mã quận/huyện giao hàng không hợp lệ.',
            'province_id.exists' => 'Quận/huyện giao hàng không tồn tại.',
            'ward_id.integer' => 'Mã phường/xã giao hàng không hợp lệ.',
            'ward_id.min' => 'Mã phường/xã giao hàng không hợp lệ.',
            'ward_id.exists' => 'Phường/xã giao hàng không tồn tại.',
            'vietnam_province_code.required' => 'Vui lòng chọn tỉnh/thành phố giao hàng.',
            'vietnam_province_code.exists' => 'Tỉnh/thành phố giao hàng không tồn tại.',
            'vietnam_ward_code.required' => 'Vui lòng chọn xã/phường/đặc khu giao hàng.',
            'vietnam_ward_code.exists' => 'Xã/phường/đặc khu giao hàng không tồn tại.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $provinceCode = (string) $this->input('vietnam_province_code');
            $wardCode = (string) $this->input('vietnam_ward_code');

            if ($provinceCode === '' || $wardCode === '' || ! Schema::hasTable('vietnam_wards')) {
                return;
            }

            $exists = VietnamWard::query()
                ->where('code', $wardCode)
                ->where('province_code', $provinceCode)
                ->exists();

            if (! $exists) {
                $validator->errors()->add('vietnam_ward_code', 'Xã/phường/đặc khu đã chọn không thuộc tỉnh/thành phố.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'shipping_method' => $this->normalizeNullableString($this->input('shipping_method')),
            'vietnam_province_code' => $this->normalizeNullableString($this->input('vietnam_province_code')),
            'vietnam_ward_code' => $this->normalizeNullableString($this->input('vietnam_ward_code')),
            'city_id' => $this->normalizeNullableInteger($this->input('city_id')),
            'province_id' => $this->normalizeNullableInteger($this->input('province_id')),
            'ward_id' => $this->normalizeNullableInteger($this->input('ward_id')),
        ]);
    }

    protected function normalizeNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function normalizeNullableInteger(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $value = trim($value);

            if ($value === '') {
                return null;
            }
        }

        return $value;
    }

    protected function locationRules(string $type): array
    {
        $rules = ['nullable', 'integer', 'min:1'];

        if (Schema::hasTable('locations')) {
            $rules[] = Rule::exists('locations', 'id')
                ->where(fn ($query) => $query->where('type', $type));
        }

        return $rules;
    }

    protected function vietnamProvinceRules(): array
    {
        $rules = ['required', 'string', 'max:20'];

        if (Schema::hasTable('vietnam_provinces')) {
            $rules[] = Rule::exists('vietnam_provinces', 'code');
        }

        return $rules;
    }

    protected function vietnamWardRules(): array
    {
        $rules = ['required', 'string', 'max:20'];

        if (Schema::hasTable('vietnam_wards')) {
            $rules[] = Rule::exists('vietnam_wards', 'code');
        }

        return $rules;
    }
}
