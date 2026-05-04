<?php

namespace App\Http\Requests\Admin\Document;

use App\Http\Requests\BaseFormRequest;

class StoreDocumentRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'document' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,txt,csv,xls,xlsx,jpg,jpeg,png,webp',
                'max:5120',
            ],
            'category' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'document.required' => 'Vui lòng chọn tài liệu cần tải lên.',
            'document.file' => 'Tệp tải lên không hợp lệ.',
            'document.mimes' => 'Tài liệu chỉ hỗ trợ pdf, doc, docx, txt, csv, xls, xlsx, jpg, jpeg, png hoặc webp.',
            'document.max' => 'Tài liệu không được vượt quá 5MB.',
            'category.max' => 'Nhóm tài liệu không được vượt quá 100 ký tự.',
        ];
    }
}
