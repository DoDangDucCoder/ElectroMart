<?php

namespace App\Http\Requests\Admin\Legacy\Media;

use App\Http\Requests\BaseFormRequest;

class UpdateVideoImageRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'video_id' => ['required', 'integer', 'min:1'],
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ];
    }
}
