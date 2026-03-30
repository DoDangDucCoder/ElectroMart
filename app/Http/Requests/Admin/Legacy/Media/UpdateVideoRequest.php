<?php

namespace App\Http\Requests\Admin\Legacy\Media;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateVideoRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'video_id' => ['required', 'integer', 'min:1'],
            'video_edit' => ['required', 'string', 'max:5000'],
            'video_check' => ['required', Rule::in(['video_title', 'video_desc', 'video_link', 'video_slug'])],
        ];
    }
}
