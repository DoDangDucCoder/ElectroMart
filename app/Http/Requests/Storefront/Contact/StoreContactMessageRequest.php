<?php

namespace App\Http\Requests\Storefront\Contact;

use App\Http\Requests\BaseFormRequest;

class StoreContactMessageRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+().\s-]+$/'],
            'subject' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
            'message.required' => 'Vui lòng nhập nội dung liên hệ.',
        ];
    }
}
