<?php

namespace App\Http\Requests\Admin\Legacy\Products;

use App\Http\Requests\BaseFormRequest;
class StoreGalleryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'pro_id' => ['required', 'integer'],
            'file' => ['required', 'array', 'min:1'],
            'file.*' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'pro_id' => $this->route('pro_id'),
        ]);
    }
}
