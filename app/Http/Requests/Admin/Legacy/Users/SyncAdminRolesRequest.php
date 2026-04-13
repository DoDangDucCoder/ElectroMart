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
            'roles' => ['nullable', 'array', 'max:1'],
            'roles.*' => [
                'string',
                Rule::exists('roles', 'name')->where(fn ($query) => $query->where('guard_name', AdminAccess::guardName())),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'admin_id.required' => 'Thiếu người dùng cần cập nhật vai trò.',
            'admin_id.integer' => 'Người dùng được chọn không hợp lệ.',
            'admin_id.exists' => 'Không tìm thấy người dùng cần cập nhật vai trò.',
            'roles.array' => 'Dữ liệu vai trò gửi lên không hợp lệ.',
            'roles.max' => 'Mỗi tài khoản chỉ đồng bộ một vai trò chính.',
            'roles.*.exists' => 'Vai trò được chọn không tồn tại trong hệ thống.',
        ];
    }
}
