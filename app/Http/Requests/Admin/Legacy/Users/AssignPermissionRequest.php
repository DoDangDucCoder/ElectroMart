<?php

namespace App\Http\Requests\Admin\Legacy\Users;

use App\Http\Requests\BaseFormRequest;
use App\Support\AdminAccess;
use Illuminate\Validation\Rule;

class AssignPermissionRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'permission' => ['nullable', 'array'],
            'permission.*' => [
                'string',
                Rule::exists('permissions', 'name')->where(fn ($query) => $query->where('guard_name', AdminAccess::guardName())),
            ],
        ];
    }
}
