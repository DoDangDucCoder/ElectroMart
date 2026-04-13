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

    public function messages(): array
    {
        return [
            'video_id.required' => 'Thiếu mã video cần cập nhật.',
            'video_id.integer' => 'Mã video không hợp lệ.',
            'video_id.min' => 'Mã video không hợp lệ.',
            'video_edit.required' => 'Vui lòng nhập nội dung cần cập nhật.',
            'video_edit.max' => 'Nội dung cập nhật không được vượt quá 5000 ký tự.',
            'video_check.required' => 'Thiếu trường video cần cập nhật.',
            'video_check.in' => 'Trường video cần cập nhật không hợp lệ.',
        ];
    }
}
