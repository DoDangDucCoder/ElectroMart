<?php

namespace App\Http\Requests\Admin\Legacy\Media;

use App\Http\Requests\BaseFormRequest;

class StoreContactInfoRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'info_contact' => ['required', 'string'],
            'info_map' => ['nullable', 'string'],
            'info_fanpage' => ['nullable', 'string'],
            'info_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:5120'],
        ];
    }
}
