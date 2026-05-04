<?php

namespace App\Http\Requests\Admin\Legacy\Delivery;

use App\Application\Shared\Services\NumericInputNormalizer;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Facades\Schema;

class UpdateDeliveryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $feeIdRules = ['required', 'integer'];

        if (Schema::hasTable('shipping_fees')) {
            $feeIdRules[] = 'exists:shipping_fees,id';
        }

        return [
            'feeship_id' => $feeIdRules,
            'fee_value' => ['required', 'string', 'regex:/^[0-9\.\,\s]+$/'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'fee_value' => (string) app(NumericInputNormalizer::class)->digitsOnly($this->input('fee_value')),
        ]);
    }
}
