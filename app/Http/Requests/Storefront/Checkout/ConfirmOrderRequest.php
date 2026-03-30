<?php

namespace App\Http\Requests\Storefront\Checkout;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class ConfirmOrderRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'shipping_email' => ['required', 'email', 'max:255'],
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_phone' => ['required', 'string', 'max:30'],
            'shipping_notes' => ['nullable', 'string', 'max:500'],
            'order_note' => ['nullable', 'string', 'max:500'],
            'shipping_method' => ['nullable', 'string', 'max:50', Rule::in(['cod', 'bank_transfer', 'cash', 'card', 'paypal'])],
            'city_id' => ['nullable', 'integer', 'min:1', 'exists:locations,id'],
            'province_id' => ['nullable', 'integer', 'min:1', 'exists:locations,id'],
            'ward_id' => ['nullable', 'integer', 'min:1', 'exists:locations,id'],
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
            'shipping_notes.max' => 'Ghi chú giao hàng không được vượt quá 500 ký tự.',
            'order_note.max' => 'Ghi chú đơn hàng không được vượt quá 500 ký tự.',
            'shipping_method.in' => 'Phương thức thanh toán không hợp lệ.',
            'city_id.integer' => 'Mã thành phố giao hàng không hợp lệ.',
            'city_id.min' => 'Mã thành phố giao hàng không hợp lệ.',
            'city_id.exists' => 'Thành phố giao hàng không tồn tại.',
            'province_id.integer' => 'Mã quận/huyện giao hàng không hợp lệ.',
            'province_id.min' => 'Mã quận/huyện giao hàng không hợp lệ.',
            'province_id.exists' => 'Quận/huyện giao hàng không tồn tại.',
            'ward_id.integer' => 'Mã phường/xã giao hàng không hợp lệ.',
            'ward_id.min' => 'Mã phường/xã giao hàng không hợp lệ.',
            'ward_id.exists' => 'Phường/xã giao hàng không tồn tại.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'shipping_method' => $this->normalizeNullableString($this->input('shipping_method')),
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
}
