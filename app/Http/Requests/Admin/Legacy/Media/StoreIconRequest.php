<?php

namespace App\Http\Requests\Admin\Legacy\Media;

use App\Http\Requests\BaseFormRequest;

class StoreIconRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'link' => ['nullable', 'string', 'max:500'],
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ];
    }
}
