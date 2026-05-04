<?php

namespace App\Http\Requests\Storefront\Checkout;

use App\Http\Requests\BaseFormRequest;

class ResetPasswordRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'token' => ['required', 'string', 'min:10', 'max:255'],
            'password_account' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Thiếu email khôi phục mật khẩu.',
            'email.email' => 'Email khôi phục không hợp lệ.',
            'email.max' => 'Email khôi phục không được vượt quá 255 ký tự.',
            'token.required' => 'Thiếu mã xác thực đặt lại mật khẩu.',
            'token.min' => 'Mã xác thực đặt lại mật khẩu không hợp lệ.',
            'token.max' => 'Mã xác thực đặt lại mật khẩu không hợp lệ.',
            'password_account.required' => 'Vui lòng nhập mật khẩu mới.',
            'password_account.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password_account.max' => 'Mật khẩu mới không được vượt quá 255 ký tự.',
            'password_account.confirmed' => 'Xác nhận mật khẩu mới chưa khớp.',
        ];
    }
}
