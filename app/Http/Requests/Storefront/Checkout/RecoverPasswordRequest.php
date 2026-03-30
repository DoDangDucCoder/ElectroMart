<?php

namespace App\Http\Requests\Storefront\Checkout;

use App\Http\Requests\BaseFormRequest;

class RecoverPasswordRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email_account' => ['required', 'email', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'email_account.required' => 'Vui lòng nhập email để khôi phục mật khẩu.',
            'email_account.email' => 'Email khôi phục không hợp lệ.',
            'email_account.max' => 'Email khôi phục không được vượt quá 255 ký tự.',
        ];
    }
}
