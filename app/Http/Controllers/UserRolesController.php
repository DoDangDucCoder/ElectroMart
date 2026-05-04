<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Legacy\Users\AssignPermissionRequest;
use App\Http\Requests\Admin\Legacy\Users\AssignRoleRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Support\AdminAccess;
use Illuminate\Support\Facades\Auth;

class UserRolesController extends Controller
{
	// public function __construct()
	// {

	// 	$this->middleware('permission:list user', ['only' => ['index']]);
	// }
	public function create($admin_id)
	{
		$roles = Role::query()
			->where('guard_name', AdminAccess::guardName())
			->orderBy('name')
			->get();

		$user = User::find($admin_id);
		if (! $user) {
			return redirect('/index-spatie')->with('message', 'Không tìm thấy người dùng cần cấp vai trò');
		}

		$user_roles = $user->roles->first();
		return view('admin.roles.create_role', compact('user', 'roles', 'user_roles'));
	}
	public function create_permission($admin_id)
	{
		$permission = Permission::query()
			->where('guard_name', AdminAccess::guardName())
			->orderBy('name')
			->get();
		$user = User::find($admin_id);
		if (! $user) {
			return redirect('/index-spatie')->with('message', 'Không tìm thấy người dùng cần cấp quyền');
		}

		$role = $user->roles->first();
		$name_roles = optional($role)->name;
		$pers_role = $user->getPermissionsViaRoles();
		$hasRole = $role !== null;

		return view('admin.roles.create_permission', compact('user', 'name_roles', 'permission', 'pers_role', 'hasRole'));
	}
	public function assign_permission($admin_id, AssignPermissionRequest $request)
	{
		$data = $request->validated();

		if ((int) Auth::id() === (int) $admin_id) {
			return redirect()->back()->with('message', 'Bạn không được cập nhật quyền của chính mình');
		}

		$user = User::find($admin_id);
		if (! $user || $user->roles->isEmpty()) {
			return redirect()->back()->with('message', 'Tài khoản này chưa có vai trò để cập nhật quyền');
		}

		$role_id = $user->roles->first()->id;
		$role = Role::find($role_id);
		if (! $role) {
			return redirect()->back()->with('message', 'Không tìm thấy vai trò hiện tại để cập nhật quyền');
		}

		$permissions = Permission::query()
			->where('guard_name', AdminAccess::guardName())
			->whereIn('name', $data['permission'] ?? [])
			->pluck('name')
			->all();

		$role->syncPermissions($permissions);

		// dd($data['permission']);
		return redirect()->back()->with('message', 'Đã cập nhật quyền cho vai trò hiện tại');
	}
	public function index()
	{
		$user = User::with('roles', 'permissions')->orderBy('id', 'desc')->paginate(10);

		return view('admin.roles.index', compact('user'));
	}

	public function assign_role($admin_id, AssignRoleRequest $request)
	{
		$data = $request->validated();

		if ((int) Auth::id() === (int) $admin_id) {
			return redirect()->back()->with('message', 'Bạn không được cập nhật vai trò của chính mình');
		}

		$admin = User::find($admin_id);
		if (! $admin) {
			return redirect('/index-spatie')->with('message', 'Không tìm thấy người dùng cần cấp vai trò');
		}

		$roles = Role::query()
			->where('guard_name', AdminAccess::guardName())
			->whereIn('name', (array) ($data['role'] ?? []))
			->pluck('name')
			->all();

		$admin->syncRoles($roles);
		return redirect()->back()->with('message', 'Đã cập nhật vai trò cho người dùng');
	}
}
