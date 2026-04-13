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

    public function messages(): array
    {
        return [
            'video_id.required' => 'Thiếu mã video cần xóa.',
            'video_id.integer' => 'Mã video không hợp lệ.',
            'video_id.min' => 'Mã video không hợp lệ.',
        ];
    }
}
