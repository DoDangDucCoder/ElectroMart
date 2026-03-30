<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminAuthenticator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login_auth()
    {
        return view('admin.custom_auth.login_auth');
    }

    public function logout_auth(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin')->with('message', 'Đăng xuất thành công');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $remember = $request->boolean('remember');
        $user = AdminAuthenticator::resolveUser($credentials['email'], $credentials['password']);

        if ($user) {
            Auth::guard('web')->login($user, $remember);
            $request->session()->regenerate();

            return $this->completeAdminLogin($request);
        }

        return redirect('/admin')
            ->withErrors([
                'email' => 'Thông tin đăng nhập không hợp lệ hoặc tài khoản không có quyền truy cập admin.',
            ])
            ->withInput($request->only('email'));
    }

    protected function completeAdminLogin(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        if (AdminAccess::canAccess($user)) {
            return redirect()->route('admin.dashboard');
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin')->withErrors([
            'email' => 'Tài khoản này không có quyền truy cập admin.',
        ]);
    }
}
