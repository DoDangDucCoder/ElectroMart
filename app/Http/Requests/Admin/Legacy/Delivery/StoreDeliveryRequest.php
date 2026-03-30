<?php

namespace App\Http\Requests\Admin\Legacy\Delivery;

use App\Application\Shared\Services\NumericInputNormalizer;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Facades\Schema;

class StoreDeliveryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $cityRules = ['required', 'integer'];
        $provinceRules = ['required', 'integer'];
        $wardRules = ['required', 'integer'];

        if (Schema::hasTable('tbl_tinhthanhpho')) {
            $cityRules[] = 'exists:tbl_tinhthanhpho,matp';
        }

        if (Schema::hasTable('tbl_quanhuyen')) {
            $provinceRules[] = 'exists:tbl_quanhuyen,maqh';
        }

        if (Schema::hasTable('tbl_xaphuongthitran')) {
            $wardRules[] = 'exists:tbl_xaphuongthitran,xaid';
        }

        return [
            'city' => $cityRules,
            'province' => $provinceRules,
            'wards' => $wardRules,
            'fee_ship' => ['required', 'string', 'regex:/^[0-9\.\,\s]+$/'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'fee_ship' => (string) app(NumericInputNormalizer::class)->digitsOnly($this->input('fee_ship')),
        ]);
    }
}
