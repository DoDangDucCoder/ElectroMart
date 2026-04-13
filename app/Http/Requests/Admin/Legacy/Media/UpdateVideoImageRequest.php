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

    public function messages(): array
    {
        return [
            'video_id.required' => 'Thiếu mã video cần cập nhật hình ảnh.',
            'video_id.integer' => 'Mã video không hợp lệ.',
            'video_id.min' => 'Mã video không hợp lệ.',
            'file.required' => 'Vui lòng chọn hình ảnh video.',
            'file.image' => 'Tệp tải lên phải là hình ảnh hợp lệ.',
            'file.mimes' => 'Hình ảnh video phải có định dạng jpg, jpeg, png, gif hoặc webp.',
            'file.max' => 'Hình ảnh video không được lớn hơn 5MB.',
        ];
    }
}
