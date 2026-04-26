<?php

namespace App\Http\Requests\Admin\Legacy\Products;

use App\Http\Requests\BaseFormRequest;
class SelectGalleryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'pro_id' => ['required', 'integer'],
        ];
    }
}
