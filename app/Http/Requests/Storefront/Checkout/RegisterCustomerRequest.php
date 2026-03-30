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
            'customer_password' => ['required', 'string', 'min:6'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
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
            'customer_password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'customer_phone.max' => 'Số điện thoại không được vượt quá 30 ký tự.',
        ];
    }
}
