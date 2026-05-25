<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Auth\ForgotAdminPasswordRequest;
use App\Http\Requests\Admin\Auth\RegisterAdminRequest;
use App\Http\Requests\Admin\Auth\ResetAdminPasswordRequest;
use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminAuthenticator;
use App\Support\AdminPermissions;
use App\Support\DatabaseRuntime;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

class AuthController extends Controller
{
    public function login_auth()
    {
        return view('admin.custom_auth.login_auth', $this->adminAuthViewData());
    }

    public function showRegisterForm()
    {
        return view('admin.custom_auth.register', $this->adminAuthViewData());
    }

    public function register(RegisterAdminRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (DatabaseRuntime::unavailable()) {
            return $this->redirectForDatabaseUnavailable(
                'admin.register',
                $request->except('password', 'password_confirmation', 'registration_code')
            );
        }

        try {
            $bootstrapMode = ! $this->adminAccountExists();

            if (! $this->registrationOpenForState($bootstrapMode)) {
                return redirect()->route('admin.login')->withErrors([
                    'email' => $bootstrapMode
                        ? 'Đăng ký tài khoản quản trị hiện đang tạm tắt.'
                        : 'Đăng ký tài khoản quản trị bổ sung cần được mở bằng mã cấp quyền trong cấu hình hệ thống.',
                ]);
            }

            if ($this->registrationCodeRequired($bootstrapMode) && ! $this->hasValidRegistrationCode($request)) {
                return redirect()->route('admin.register')
                    ->withErrors(['registration_code' => 'Mã cấp quyền đăng ký quản trị không hợp lệ.'])
                    ->withInput($request->except('password', 'password_confirmation', 'registration_code'));
            }

            if (User::where('email', $data['email'])->exists()) {
                return redirect()->route('admin.register')
                    ->withErrors(['email' => 'Email này đã tồn tại trên hệ thống.'])
                    ->withInput($request->except('password', 'password_confirmation', 'registration_code'));
            }

            $roleName = $this->resolveRegistrationRole($bootstrapMode);

            $this->ensureAdminRoleSetup($roleName);

            $user = User::create($this->buildAdminUserPayload([
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]));

            $user->assignRole($roleName);

            return redirect()->route('admin.login')->with('message', $bootstrapMode
                ? 'Tạo tài khoản quản trị đầu tiên thành công. Bạn có thể đăng nhập ngay.'
                : 'Đăng ký tài khoản quản trị thành công. Vui lòng đăng nhập để tiếp tục.');
        } catch (Throwable $e) {
            return $this->handleDatabaseFailure(
                $e,
                'admin.register',
                $request->except('password', 'password_confirmation', 'registration_code')
            );
        }
    }

    public function showForgotPasswordForm()
    {
        return view('admin.custom_auth.forgot_password', $this->adminAuthViewData());
    }

    public function sendResetLink(ForgotAdminPasswordRequest $request): RedirectResponse
    {
        $email = (string) $request->validated('email');

        if (DatabaseRuntime::unavailable()) {
            return $this->redirectForDatabaseUnavailable('admin.password.request', ['email' => $email]);
        }

        try {
            $user = User::where('email', $email)->first();

            if ($user && AdminAccess::canAccess($user)) {
                $status = Password::broker('users')->sendResetLink(['email' => $email]);

                if ($status === Password::RESET_THROTTLED) {
                    return redirect()->route('admin.password.request')->withErrors([
                        'email' => 'Bạn vừa yêu cầu đặt lại mật khẩu gần đây. Hãy thử lại sau ít phút.',
                    ]);
                }
            }

            return redirect()->route('admin.password.request')
                ->with('status', 'Nếu email quản trị tồn tại trên hệ thống, chúng tôi đã gửi liên kết đặt lại mật khẩu.');
        } catch (Throwable $e) {
            return $this->handleDatabaseFailure($e, 'admin.password.request', ['email' => $email]);
        }
    }

    public function showResetPasswordForm(Request $request, string $token)
    {
        $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
        ], [
            'email.required' => 'Thiếu email quản trị để đặt lại mật khẩu.',
            'email.email' => 'Email quản trị không hợp lệ.',
        ]);

        return view('admin.custom_auth.reset_password', array_merge(
            $this->adminAuthViewData(),
            [
                'token' => $token,
                'resetEmail' => (string) $request->query('email'),
            ]
        ));
    }

    public function resetPassword(ResetAdminPasswordRequest $request): RedirectResponse
    {
        if (DatabaseRuntime::unavailable()) {
            return $this->redirectForDatabaseUnavailable('admin.password.request', $request->only('email'));
        }

        try {
            $status = Password::broker('users')->reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password): void {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($user));
                }
            );

            if ($status !== Password::PASSWORD_RESET) {
                return redirect()->route('admin.password.request')
                    ->withErrors([
                        'email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.',
                    ])
                    ->withInput($request->only('email'));
            }

            return redirect()->route('admin.login')->with('message', 'Mật khẩu quản trị đã được cập nhật. Vui lòng đăng nhập lại.');
        } catch (Throwable $e) {
            return $this->handleDatabaseFailure($e, 'admin.password.request', $request->only('email'));
        }
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

        if (DatabaseRuntime::unavailable()) {
            return $this->redirectForDatabaseUnavailable('admin.login', $request->only('email'));
        }

        try {
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
        } catch (Throwable $e) {
            return $this->handleDatabaseFailure($e, 'admin.login', $request->only('email'));
        }
    }

    protected function completeAdminLogin(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        try {
            if (AdminAccess::canAccess($user)) {
                return redirect()->route('admin.dashboard');
            }
        } catch (Throwable $e) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $this->handleDatabaseFailure($e, 'admin.login');
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin')->withErrors([
            'email' => 'Tài khoản này không có quyền truy cập admin.',
        ]);
    }

    protected function adminAuthViewData(): array
    {
        if (DatabaseRuntime::unavailable()) {
            return $this->databaseUnavailableViewData();
        }

        try {
            $bootstrapMode = ! $this->adminAccountExists();
            $requiresRegistrationCode = $this->registrationCodeRequired($bootstrapMode);
            $defaultRole = $this->resolveRegistrationRole($bootstrapMode);

            return [
                'adminAuthDatabaseUnavailable' => false,
                'adminAuthDatabaseUnavailableMessage' => null,
                'adminAuthRegistrationEnabled' => $this->registrationOpenForState($bootstrapMode),
                'adminAuthBootstrapMode' => $bootstrapMode,
                'adminAuthRequiresRegistrationCode' => $requiresRegistrationCode,
                'adminAuthRegistrationRole' => $defaultRole,
                'adminAuthRegistrationConfigured' => filled((string) config('admin-auth.registration.invite_code')),
                'adminAuthShowSeedHint' => app()->environment('local'),
                'adminAuthSeedDefaults' => config('admin-auth.seed_defaults', []),
            ];
        } catch (Throwable $e) {
            if (! DatabaseRuntime::causedByConnectionFailure($e)) {
                throw $e;
            }

            DatabaseRuntime::markUnavailable();

            return $this->databaseUnavailableViewData();
        }
    }

    protected function selfRegistrationEnabled(): bool
    {
        return (bool) config('admin-auth.registration.enabled', false);
    }

    protected function registrationCodeRequired(bool $bootstrapMode): bool
    {
        return true;
    }

    protected function hasValidRegistrationCode(Request $request): bool
    {
        $configuredCode = trim((string) config('admin-auth.registration.invite_code', ''));

        if ($configuredCode === '') {
            return false;
        }

        return hash_equals($configuredCode, trim((string) $request->input('registration_code', '')));
    }

    protected function adminAccountExists(): bool
    {
        return User::query()
            ->whereHas('roles', function ($query): void {
                $query->where('guard_name', AdminAccess::guardName())
                    ->whereIn('name', AdminAccess::queryableRoleNames());
            })
            ->exists();
    }

    protected function registrationOpenForState(bool $bootstrapMode): bool
    {
        if (! $this->selfRegistrationEnabled()) {
            return false;
        }

        return filled((string) config('admin-auth.registration.invite_code'));
    }

    protected function resolveRegistrationRole(bool $bootstrapMode): string
    {
        $configuredRole = (string) config(
            $bootstrapMode ? 'admin-auth.registration.bootstrap_role' : 'admin-auth.registration.default_role',
            $bootstrapMode ? 'Super Admin' : 'Manager'
        );

        $role = AdminAccess::normalizeRoleName($configuredRole);

        if (! in_array($role, AdminAccess::canonicalRoleNames(), true)) {
            return $bootstrapMode ? 'Super Admin' : 'Manager';
        }

        return $role;
    }

    protected function ensureAdminRoleSetup(string $roleName): void
    {
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
    }

    protected function buildAdminUserPayload(array $data): array
    {
        if (! Schema::hasColumn((new User())->getTable(), 'phone')) {
            unset($data['phone']);
        }

        return $data;
    }

    protected function databaseUnavailableViewData(): array
    {
        return [
            'adminAuthDatabaseUnavailable' => true,
            'adminAuthDatabaseUnavailableMessage' => DatabaseRuntime::unavailableMessage(),
            'adminAuthRegistrationEnabled' => false,
            'adminAuthBootstrapMode' => false,
            'adminAuthRequiresRegistrationCode' => false,
            'adminAuthRegistrationRole' => 'Manager',
            'adminAuthRegistrationConfigured' => filled((string) config('admin-auth.registration.invite_code')),
            'adminAuthShowSeedHint' => app()->environment('local'),
            'adminAuthSeedDefaults' => config('admin-auth.seed_defaults', []),
        ];
    }

    protected function redirectForDatabaseUnavailable(string $route, array $input = []): RedirectResponse
    {
        DatabaseRuntime::markUnavailable();

        return redirect()->route($route)
            ->withErrors(['database' => DatabaseRuntime::unavailableMessage()])
            ->withInput($input);
    }

    protected function handleDatabaseFailure(Throwable $e, string $route, array $input = []): RedirectResponse
    {
        if (! DatabaseRuntime::causedByConnectionFailure($e)) {
            throw $e;
        }

        return $this->redirectForDatabaseUnavailable($route, $input);
    }
}
