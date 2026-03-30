<?php

namespace App\Http\Requests\Admin\Legacy\Products;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Facades\Schema;

class SelectGalleryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $productRules = ['required', 'integer'];

        if (Schema::hasTable('tbl_product')) {
            $productRules[] = 'exists:tbl_product,product_id';
        }

        return [
            'pro_id' => $productRules,
        ];
    }
}
