<?php

namespace App\Http\Requests\Admin\Legacy\Media;

use App\Http\Requests\BaseFormRequest;

class DeleteVideoRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'video_id' => ['required', 'integer', 'min:1'],
        ];
    }
}
