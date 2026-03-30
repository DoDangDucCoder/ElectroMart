<?php

namespace App\Http\Requests\Admin\Legacy\Products;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Facades\Schema;

class StoreGalleryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $productRules = ['required', 'integer'];

        if (Schema::hasTable('tbl_product')) {
            $productRules[] = 'exists:tbl_product,product_id';
        }

        return [
            'pro_id' => $productRules,
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
