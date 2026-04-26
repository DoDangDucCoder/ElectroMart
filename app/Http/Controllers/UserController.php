<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Legacy\Users\StoreAdminUserRequest;
use App\Http\Requests\Admin\Legacy\Users\SyncAdminRolesRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Auth;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admin = User::with('roles')->orderBy('id', 'DESC')->paginate(5);
        $roles = Role::query()
            ->where('guard_name', AdminAccess::guardName())
            ->orderBy('name')
            ->get();

        return view('admin.users.all_users')->with(compact('admin', 'roles'));
    }

    public function add_users()
    {
        return view('admin.users.add_users');
    }
    public function delete_user_roles($admin_id)
    {
        if ((int) Auth::id() === (int) $admin_id) {
            return redirect()->back()->with('message', 'Bạn không được quyền xóa chính mình');
        }
        $admin = User::find($admin_id);

        if (! $admin) {
            return redirect()->back()->with('error', 'Không tìm thấy người dùng cần xóa');
        }

        $admin->roles()->detach();
        $admin->delete();

        return redirect()->back()->with('message', 'Xóa user thành công');
    }
    public function assign_roles(SyncAdminRolesRequest $request)
    {
        $validated = $request->validated();

        if ((int) Auth::id() === (int) $validated['admin_id']) {
            return redirect()->back()->with('message', 'Bạn không được phân quyền chính mình');
        }

        $user = User::find($validated['admin_id']);

        if (!$user) {
            return redirect()->back()->with('error', 'Không tìm thấy người dùng cần cập nhật vai trò');
        }

        $roleNames = collect($validated['roles'] ?? [])
            ->filter()
            ->values()
            ->all();

        $validRoleNames = Role::query()
            ->where('guard_name', AdminAccess::guardName())
            ->whereIn('name', $roleNames)
            ->pluck('name')
            ->all();

        $user->syncRoles($validRoleNames);

        return redirect()->back()->with('message', 'Cấp quyền thành công');
    }

    public function store_users(StoreAdminUserRequest $request)
    {
        $data = $request->validated();

        $admin = new User();
        $admin->name = $data['admin_name'];
        if (Schema::hasColumn($admin->getTable(), 'phone')) {
            $admin->phone = $data['admin_phone'] ?? null;
        }
        $admin->email = $data['admin_email'];
        $admin->password = Hash::make($data['admin_password']);
        $admin->save();

        $defaultRole = $this->resolveProvisioningRole();

        if ($defaultRole) {
            $admin->assignRole($defaultRole);
        }

        Session::put('message', $defaultRole
            ? 'Thêm users thành công và đã gán vai trò ' . $defaultRole->name
            : 'Thêm users thành công');
        return Redirect::to('users');
    }

    protected function resolveProvisioningRole(): ?Role
    {
        $configuredRole = (string) config('admin-auth.provisioning.default_role', 'Staff');
        $roleName = AdminAccess::normalizeRoleName($configuredRole);

        if (! in_array($roleName, AdminAccess::canonicalRoleNames(), true)) {
            $roleName = 'Staff';
        }

        foreach (AdminPermissions::all() as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => AdminAccess::guardName(),
            ]);
        }

        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => AdminAccess::guardName(),
        ]);

        $missingPermissions = array_diff(
            AdminPermissions::defaultsForRole($roleName),
            $role->permissions()
                ->where('guard_name', AdminAccess::guardName())
                ->pluck('name')
                ->all()
        );

        if ($missingPermissions !== []) {
            $role->givePermissionTo($missingPermissions);
        }

        return $role;
    }
}
