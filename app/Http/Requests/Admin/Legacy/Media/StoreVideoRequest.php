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

    public function messages(): array
    {
        return [
            'video_title.required' => 'Vui lòng nhập tên video.',
            'video_title.max' => 'Tên video không được vượt quá 255 ký tự.',
            'video_slug.max' => 'Slug video không được vượt quá 255 ký tự.',
            'video_desc.string' => 'Mô tả video không hợp lệ.',
            'video_link.required' => 'Vui lòng nhập link video.',
            'video_link.max' => 'Link video không được vượt quá 2048 ký tự.',
            'file.image' => 'Tệp tải lên phải là hình ảnh video hợp lệ.',
            'file.mimes' => 'Hình ảnh video phải có định dạng jpg, jpeg, png, gif hoặc webp.',
            'file.max' => 'Hình ảnh video không được lớn hơn 5MB.',
        ];
    }
}
