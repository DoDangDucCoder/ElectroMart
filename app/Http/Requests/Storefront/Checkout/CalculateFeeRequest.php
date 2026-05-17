<?php

namespace App\Http\Requests\Storefront\Checkout;

use App\Http\Requests\BaseFormRequest;
use App\Infrastructure\Persistence\Eloquent\Geo\VietnamWard;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CalculateFeeRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'vietnam_province_code' => $this->vietnamProvinceRules(),
            'vietnam_ward_code' => $this->vietnamWardRules(),
            'city_id' => $this->locationRules('city'),
            'province_id' => $this->locationRules('province'),
            'ward_id' => $this->locationRules('ward'),
        ];
    }

    public function messages(): array
    {
        return [
            'city_id.required' => 'Vui lòng chọn thành phố.',
            'city_id.integer' => 'Mã thành phố không hợp lệ.',
            'city_id.min' => 'Mã thành phố không hợp lệ.',
            'city_id.exists' => 'Thành phố đã chọn không tồn tại.',
            'province_id.required' => 'Vui lòng chọn quận/huyện.',
            'province_id.integer' => 'Mã quận/huyện không hợp lệ.',
            'province_id.min' => 'Mã quận/huyện không hợp lệ.',
            'province_id.exists' => 'Quận/huyện đã chọn không tồn tại.',
            'ward_id.required' => 'Vui lòng chọn phường/xã.',
            'ward_id.integer' => 'Mã phường/xã không hợp lệ.',
            'ward_id.min' => 'Mã phường/xã không hợp lệ.',
            'ward_id.exists' => 'Phường/xã đã chọn không tồn tại.',
            'vietnam_province_code.required' => 'Vui lòng chọn tỉnh/thành phố.',
            'vietnam_province_code.exists' => 'Tỉnh/thành phố đã chọn không tồn tại.',
            'vietnam_ward_code.required' => 'Vui lòng chọn xã/phường/đặc khu.',
            'vietnam_ward_code.exists' => 'Xã/phường/đặc khu đã chọn không tồn tại.',
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
            'vietnam_province_code' => $this->normalizeNullableString($this->input('vietnam_province_code')),
            'vietnam_ward_code' => $this->normalizeNullableString($this->input('vietnam_ward_code')),
            'city_id' => $this->normalizeNullableInteger($this->input('city_id', $this->input('matp'))),
            'province_id' => $this->normalizeNullableInteger($this->input('province_id', $this->input('maqh'))),
            'ward_id' => $this->normalizeNullableInteger($this->input('ward_id', $this->input('xaid'))),
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
