<?php

namespace App\Http\Requests\Admin\Legacy\Delivery;

use App\Application\Shared\Services\NumericInputNormalizer;
use App\Http\Requests\BaseFormRequest;
use App\Infrastructure\Persistence\Eloquent\Geo\VietnamWard;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreDeliveryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $provinceRules = ['required', 'string', 'max:20'];
        $wardRules = ['required', 'string', 'max:20'];

        if (Schema::hasTable('vietnam_provinces')) {
            $provinceRules[] = Rule::exists('vietnam_provinces', 'code');
        }

        if (Schema::hasTable('vietnam_wards')) {
            $wardRules[] = Rule::exists('vietnam_wards', 'code');
        }

        return [
            'province_code' => $provinceRules,
            'ward_code' => $wardRules,
            'fee_ship' => ['required', 'string', 'regex:/^[0-9\.\,\s]+$/'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! Schema::hasTable('vietnam_wards')) {
                return;
            }

            $exists = VietnamWard::query()
                ->where('code', (string) $this->input('ward_code'))
                ->where('province_code', (string) $this->input('province_code'))
                ->exists();

            if (! $exists) {
                $validator->errors()->add('ward_code', 'Xã/phường/đặc khu đã chọn không thuộc tỉnh/thành phố.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'province_code' => (string) $this->input('province_code', $this->input('city')),
            'ward_code' => (string) $this->input('ward_code', $this->input('wards')),
            'fee_ship' => (string) app(NumericInputNormalizer::class)->digitsOnly($this->input('fee_ship')),
        ]);
    }
}
