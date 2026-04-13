<?php

namespace App\Http\Requests\Admin\Legacy\Products;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Facades\Schema;

class UpdateGalleryNameRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $galleryRules = ['required', 'integer'];

        if (Schema::hasTable('tbl_gallery')) {
            $galleryRules[] = 'exists:tbl_gallery,gallery_id';
        }

        return [
            'gal_id' => $galleryRules,
            'gal_text' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'gal_id.exists' => 'Không tìm thấy ảnh trong thư viện cần cập nhật.',
        ];
    }
}
