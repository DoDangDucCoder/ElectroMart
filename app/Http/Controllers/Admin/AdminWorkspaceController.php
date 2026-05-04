<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Profile\UpdateAdminProfileRequest;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
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

    public function productsManagement(Request $request): View
    {
        $perPage = max(1, min((int) $request->integer('per_page', 15), 50));
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');

        $query = Product::query()
            ->with(['brand', 'primaryCategory'])
            ->withCount('categories')
            ->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'hidden') {
            $query->where('is_active', false);
        } elseif ($status === 'out_of_stock') {
            $query->where('stock_quantity', '<=', 0);
        } elseif ($status === 'low_stock') {
            $query->where('stock_quantity', '>', 0)
                ->where('stock_quantity', '<=', 10);
        }

        return view('admin.product.admin_index', [
            'products' => $query->paginate($perPage)->withQueryString(),
            'stats' => [
                'total' => Product::count(),
                'active' => Product::where('is_active', true)->count(),
                'hidden' => Product::where('is_active', false)->count(),
                'out_of_stock' => Product::where('stock_quantity', '<=', 0)->count(),
                'low_stock' => Product::where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10)->count(),
            ],
            'filters' => [
                'q' => $search,
                'status' => $status,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function categoriesManagement(Request $request): View
    {
        $perPage = max(1, min((int) $request->integer('per_page', 15), 50));
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $level = (string) $request->query('level', 'all');

        $query = ProductCategory::query()
            ->with('parent')
            ->withCount(['products', 'children'])
            ->orderBy('display_order')
            ->orderBy('name');

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'hidden') {
            $query->where('is_active', false);
        }

        if ($level === 'parent') {
            $query->whereNull('parent_id');
        } elseif ($level === 'child') {
            $query->whereNotNull('parent_id');
        }

        return view('admin.category.admin_index', [
            'categories' => $query->paginate($perPage)->withQueryString(),
            'stats' => [
                'total' => ProductCategory::count(),
                'active' => ProductCategory::where('is_active', true)->count(),
                'hidden' => ProductCategory::where('is_active', false)->count(),
                'parents' => ProductCategory::whereNull('parent_id')->count(),
                'children' => ProductCategory::whereNotNull('parent_id')->count(),
            ],
            'filters' => [
                'q' => $search,
                'status' => $status,
                'level' => $level,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function ordersManagement(Request $request): View
    {
        $perPage = max(1, min((int) $request->integer('per_page', 15), 50));
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $paymentStatus = (string) $request->query('payment_status', 'all');

        $query = Order::query()
            ->with(['customer', 'items', 'payments'])
            ->withCount('items')
            ->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('code', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function ($customerQuery) use ($search): void {
                        $customerQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('items', function ($itemQuery) use ($search): void {
                        $itemQuery->where('product_name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($paymentStatus !== 'all') {
            $query->where('payment_status', $paymentStatus);
        }

        return view('admin.order.index', [
            'orders' => $query->paginate($perPage)->withQueryString(),
            'stats' => [
                'total' => Order::count(),
                'pending' => Order::where('status', 'pending')->count(),
                'processing' => Order::whereIn('status', ['confirmed', 'processing'])->count(),
                'shipped' => Order::whereIn('status', ['shipping', 'shipped'])->count(),
                'completed' => Order::where('status', 'completed')->count(),
                'cancelled' => Order::where('status', 'cancelled')->count(),
            ],
            'filters' => [
                'q' => $search,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'per_page' => $perPage,
            ],
        ]);
    }
}
