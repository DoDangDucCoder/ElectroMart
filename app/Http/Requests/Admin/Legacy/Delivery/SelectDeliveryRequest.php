<?php

namespace App\Http\Requests\Admin\Legacy\Delivery;

use App\Http\Requests\BaseFormRequest;

class SelectDeliveryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'action' => ['required', 'in:city,province'],
            'ma_id' => ['required', 'integer'],
        ];
    }
}
