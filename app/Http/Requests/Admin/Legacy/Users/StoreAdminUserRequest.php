<?php

namespace App\Http\Requests\Admin\Legacy\Users;

use App\Http\Requests\BaseFormRequest;

class StoreAdminUserRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'admin_phone' => ['nullable', 'string', 'max:30'],
            'admin_password' => ['required', 'string', 'min:8'],
        ];
    }
}
