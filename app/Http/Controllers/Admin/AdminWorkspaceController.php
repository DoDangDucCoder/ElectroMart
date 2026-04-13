<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Profile\UpdateAdminProfileRequest;
use App\Models\User;
use App\Support\AdminWorkspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AdminWorkspaceController extends Controller
{
    public function search(Request $request): View
    {
        /** @var User $user */
        $user = $request->user('web');
        $query = trim((string) $request->query('q', ''));
        $destinations = AdminWorkspace::destinationsFor($user);

        return view('admin.tools.search', [
            'query' => $query,
            'availableCount' => $destinations->count(),
            'results' => $query === ''
                ? AdminWorkspace::topSuggestions($user, 12)
                : AdminWorkspace::search($user, $query),
            'popularDestinations' => AdminWorkspace::topSuggestions($user, 8),
        ]);
    }

    public function profile(Request $request): View
    {
        /** @var User $user */
        $user = $request->user('web');

        return view('admin.tools.profile', [
            'adminUser' => $user,
            'roleNames' => $user->getRoleNames(),
            'permissionNames' => $user->getPermissionNames(),
            'profileSupportsPhone' => Schema::hasColumn((new User())->getTable(), 'phone'),
            'recommendedDestinations' => AdminWorkspace::topSuggestions($user, 6)
                ->reject(fn (array $entry) => $entry['key'] === 'admin-profile')
                ->values(),
        ]);
    }

    public function updateProfile(UpdateAdminProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user('web');
        $data = $request->validated();

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (Schema::hasColumn((new User())->getTable(), 'phone')) {
            $payload['phone'] = $data['phone'] ?? null;
        }

        if (filled($data['password'] ?? null)) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->fill($payload)->save();

        return redirect()
            ->route('admin.profile.show')
            ->with('message', filled($data['password'] ?? null)
                ? 'Cập nhật hồ sơ và mật khẩu quản trị thành công.'
                : 'Cập nhật hồ sơ quản trị thành công.');
    }

    public function settings(Request $request): View
    {
        /** @var User $user */
        $user = $request->user('web');
        $groupedDestinations = AdminWorkspace::destinationsFor($user)
            ->reject(fn (array $entry) => $entry['key'] === 'admin-settings')
            ->groupBy('section');

        return view('admin.tools.settings', [
            'adminUser' => $user,
            'groupedDestinations' => $groupedDestinations,
            'shortcutCount' => $groupedDestinations->flatten(1)->count(),
            'roleNames' => $user->getRoleNames(),
            'permissionCount' => $user->getPermissionNames()->count(),
        ]);
    }
}
