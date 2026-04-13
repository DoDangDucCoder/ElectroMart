<?php

namespace App\Http\Requests\Admin\Profile;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class UpdateAdminProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user('web');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email:rfc',
                'max:255',
                Rule::unique((new User())->getTable(), 'email')->ignore($user?->getAuthIdentifier()),
            ],
            'current_password' => ['nullable', 'required_with:password,password_confirmation', 'current_password:web'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['nullable', 'string', 'min:8'],
        ];

        if (Schema::hasColumn((new User())->getTable(), 'phone')) {
            $rules['phone'] = ['nullable', 'string', 'max:30'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên hiển thị quản trị là bắt buộc.',
            'email.required' => 'Email đăng nhập quản trị là bắt buộc.',
            'email.email' => 'Email quản trị không hợp lệ.',
            'email.unique' => 'Email này đã được dùng cho tài khoản khác.',
            'current_password.required_with' => 'Hãy nhập mật khẩu hiện tại để đổi mật khẩu mới.',
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'password.min' => 'Mật khẩu mới cần có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới chưa khớp.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'email' => trim((string) $this->input('email')),
            'phone' => $this->has('phone') ? trim((string) $this->input('phone')) : null,
        ]);
    }
}
