<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Legacy\Users\StoreAdminUserRequest;
use App\Http\Requests\Admin\Legacy\Users\SyncAdminRolesRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Support\AdminAccess;
use Auth;
use Session;
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

        if ($admin) {
            $admin->roles()->detach();
            $admin->delete();
        }
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
            return redirect()->back()->with('error', 'User not found');
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
        $admin->phone = $data['admin_phone'] ?? null;
        $admin->email = $data['admin_email'];
        $admin->password = Hash::make($data['admin_password']);
        $admin->save();

        $defaultRole = Role::query()
            ->where('guard_name', AdminAccess::guardName())
            ->where('name', 'user')
            ->first();

        if ($defaultRole) {
            $admin->assignRole($defaultRole);
        }

        Session::put('message', 'Thêm users thành công');
        return Redirect::to('users');
    }
}
