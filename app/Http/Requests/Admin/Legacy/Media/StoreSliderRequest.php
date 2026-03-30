<?php

namespace App\Http\Requests\Admin\Legacy\Media;

use App\Http\Requests\BaseFormRequest;

class StoreSliderRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'slider_name' => ['required', 'string', 'max:255'],
            'slider_alt' => ['nullable', 'string', 'max:255'],
            'slider_status' => ['required', 'in:0,1'],
            'slider_desc' => ['nullable', 'string'],
            'slider_tone' => ['nullable', 'in:auto,mobile,gaming,lifestyle,accessory'],
            'slider_image' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ];
    }
}
