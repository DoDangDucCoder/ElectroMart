<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Legacy\Users\AssignPermissionRequest;
use App\Http\Requests\Admin\Legacy\Users\AssignRoleRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Support\AdminAccess;
use Artisan;

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
			return redirect('/index-spatie')->with('message', 'Không tìm thấy user cần cấp vai trò');
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
			return redirect('/index-spatie')->with('message', 'Không tìm thấy user cần cấp quyền');
		}

		$name_roles = optional($user->roles->first())->name;
		$pers_role = $user->getPermissionsViaRoles();
		return view('admin.roles.create_permission', compact('user', 'name_roles', 'permission', 'pers_role'));
	}
	public function assign_permission($admin_id, AssignPermissionRequest $request)
	{
		$data = $request->validated();

		$user = User::find($admin_id);
		if (! $user || $user->roles->isEmpty()) {
			return redirect()->back()->with('message', 'User chưa có vai trò để gán quyền');
		}

		$role_id = $user->roles->first()->id;
		$role = Role::find($role_id);
		$permissions = Permission::query()
			->where('guard_name', AdminAccess::guardName())
			->whereIn('name', $data['permission'] ?? [])
			->pluck('name')
			->all();

		$role->syncPermissions($permissions);

		// dd($data['permission']);
		return redirect()->back()->with('message', 'Thêm quyền user thành công');
	}
	public function index()
	{
		Artisan::call('cache:clear');
		$user = User::with('roles', 'permissions')->orderby('id', 'desc')->get();
		// dd($user);
		return view('admin.roles.index', compact('user'));
	}

	public function assign_role($admin_id, AssignRoleRequest $request)
	{
		$data = $request->validated();

		$admin = User::find($admin_id);
		if (! $admin) {
			return redirect('/index-spatie')->with('message', 'Không tìm thấy user cần cấp vai trò');
		}

		$roles = Role::query()
			->where('guard_name', AdminAccess::guardName())
			->whereIn('name', (array) ($data['role'] ?? []))
			->pluck('name')
			->all();

		$admin->syncRoles($roles);
		return redirect()->back()->with('message', 'Thêm vai trò user thành công');
	}
}
