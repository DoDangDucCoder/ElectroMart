<?php

namespace App\Http\Requests\Admin\Legacy\Media;

use App\Http\Requests\BaseFormRequest;

class StoreVideoRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'video_title' => ['required', 'string', 'max:255'],
            'video_slug' => ['nullable', 'string', 'max:255'],
            'video_desc' => ['nullable', 'string'],
            'video_link' => ['required', 'string', 'max:2048'],
            'file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ];
    }
}
