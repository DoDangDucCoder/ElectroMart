<?php

namespace App\Http\Requests\Admin\Legacy\Users;

use App\Http\Requests\BaseFormRequest;
use App\Support\AdminAccess;
use Illuminate\Validation\Rule;

class AssignRoleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'role' => ['required', 'array', 'min:1', 'max:1'],
            'role.*' => [
                'string',
                Rule::exists('roles', 'name')->where(fn ($query) => $query->where('guard_name', AdminAccess::guardName())),
            ],
        ];
    }
}
