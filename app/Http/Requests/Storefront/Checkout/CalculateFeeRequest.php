<?php

namespace App\Http\Requests\Storefront\Checkout;

use App\Http\Requests\BaseFormRequest;

class CalculateFeeRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'city_id' => ['nullable', 'integer', 'min:1', 'exists:locations,id'],
            'province_id' => ['nullable', 'integer', 'min:1', 'exists:locations,id'],
            'ward_id' => ['nullable', 'integer', 'min:1', 'exists:locations,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'city_id.integer' => 'Mã thành phố không hợp lệ.',
            'city_id.min' => 'Mã thành phố không hợp lệ.',
            'city_id.exists' => 'Thành phố đã chọn không tồn tại.',
            'province_id.integer' => 'Mã quận/huyện không hợp lệ.',
            'province_id.min' => 'Mã quận/huyện không hợp lệ.',
            'province_id.exists' => 'Quận/huyện đã chọn không tồn tại.',
            'ward_id.integer' => 'Mã phường/xã không hợp lệ.',
            'ward_id.min' => 'Mã phường/xã không hợp lệ.',
            'ward_id.exists' => 'Phường/xã đã chọn không tồn tại.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'city_id' => $this->normalizeNullableInteger($this->input('city_id', $this->input('matp'))),
            'province_id' => $this->normalizeNullableInteger($this->input('province_id', $this->input('maqh'))),
            'ward_id' => $this->normalizeNullableInteger($this->input('ward_id', $this->input('xaid'))),
        ]);
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
