<?php

namespace App\Http\Requests\Admin\Legacy\Products;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Facades\Schema;

class DeleteGalleryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'gal_id' => ['required', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'gal_id.exists' => 'Không tìm thấy ảnh trong thư viện cần xóa.',
        ];
    }
}
