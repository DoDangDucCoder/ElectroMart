<?php

namespace App\Http\Requests\Storefront\Checkout;

use App\Http\Requests\BaseFormRequest;

class LoginCustomerRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email_account' => ['required', 'string', 'max:255'],
            'password_account' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email_account.required' => 'Vui lòng nhập email hoặc số điện thoại đăng nhập.',
            'email_account.max' => 'Email hoặc số điện thoại không được vượt quá 255 ký tự.',
            'password_account.required' => 'Vui lòng nhập mật khẩu.',
        ];
    }
}
