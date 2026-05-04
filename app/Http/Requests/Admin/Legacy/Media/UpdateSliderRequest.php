<?php

namespace App\Http\Requests\Admin\Legacy\Media;

class UpdateSliderRequest extends StoreSliderRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'slider_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ]);
    }
}
