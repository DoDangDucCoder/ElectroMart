<?php

namespace App\Http\Requests\Storefront\Checkout;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class RegisterCustomerRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255', Rule::unique('customers', 'email')],
            'customer_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
            ],
            'customer_phone' => ['required', 'string', 'max:30', Rule::unique('customers', 'phone')],
            'agree_terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Vui lòng nhập họ và tên.',
            'customer_name.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'customer_email.required' => 'Vui lòng nhập email.',
            'customer_email.email' => 'Email không hợp lệ.',
            'customer_email.max' => 'Email không được vượt quá 255 ký tự.',
            'customer_email.unique' => 'Email này đã được sử dụng.',
            'customer_password.required' => 'Vui lòng nhập mật khẩu.',
            'customer_password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'customer_password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'customer_password.regex' => 'Mật khẩu phải bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại.',
            'customer_phone.max' => 'Số điện thoại không được vượt quá 30 ký tự.',
            'customer_phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'agree_terms.accepted' => 'Bạn cần đồng ý với điều khoản sử dụng và chính sách bảo mật.',
        ];
    }
}
