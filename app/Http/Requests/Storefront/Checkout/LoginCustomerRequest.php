<?php

namespace App\Http\Requests\Storefront\Checkout;

use App\Http\Requests\BaseFormRequest;

class LoginCustomerRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email_account' => ['required', 'email'],
            'password_account' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email_account.required' => 'Vui lòng nhập email đăng nhập.',
            'email_account.email' => 'Email đăng nhập không hợp lệ.',
            'password_account.required' => 'Vui lòng nhập mật khẩu.',
        ];
    }
}
