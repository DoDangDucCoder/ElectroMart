<?php

namespace App\Http\Requests\Admin\Legacy\Users;

use App\Http\Requests\BaseFormRequest;
use App\Support\AdminAccess;
use Illuminate\Validation\Rule;

class SyncAdminRolesRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'admin_id' => ['required', 'integer', 'exists:users,id'],
            'roles' => ['nullable', 'array'],
            'roles.*' => [
                'string',
                Rule::exists('roles', 'name')->where(fn ($query) => $query->where('guard_name', AdminAccess::guardName())),
            ],
        ];
    }
}
