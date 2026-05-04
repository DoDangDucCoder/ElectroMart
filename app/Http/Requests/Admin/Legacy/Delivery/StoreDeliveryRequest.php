<?php

namespace App\Http\Requests\Admin\Legacy\Delivery;

use App\Application\Shared\Services\NumericInputNormalizer;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class StoreDeliveryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $cityRules = ['required', 'integer'];
        $provinceRules = ['required', 'integer'];
        $wardRules = ['required', 'integer'];

        if (Schema::hasTable('locations')) {
            $cityRules[] = Rule::exists('locations', 'id')
                ->where(fn ($query) => $query->where('type', 'city'));
        }

        if (Schema::hasTable('locations')) {
            $provinceRules[] = Rule::exists('locations', 'id')
                ->where(fn ($query) => $query->where('type', 'province'));
        }

        if (Schema::hasTable('locations')) {
            $wardRules[] = Rule::exists('locations', 'id')
                ->where(fn ($query) => $query->where('type', 'ward'));
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
