<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Profile\UpdateAdminProfileRequest;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use App\Models\User;
use App\Support\AdminWorkspace;
use App\Support\CsvExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $sort = (string) $request->query('sort', 'newest');

        $query = Product::query()
            ->with(['brand', 'primaryCategory'])
            ->withCount('categories');

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

        match ($sort) {
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'price_asc' => $query->orderBy('price')->orderByDesc('id'),
            'price_desc' => $query->orderByDesc('price')->orderByDesc('id'),
            'stock_asc' => $query->orderBy('stock_quantity')->orderByDesc('id'),
            'stock_desc' => $query->orderByDesc('stock_quantity')->orderByDesc('id'),
            default => $query->orderByDesc('id'),
        };

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
                'sort' => $sort,
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
        $sort = (string) $request->query('sort', 'name_asc');

        $query = ProductCategory::query()
            ->with('parent')
            ->withCount(['products', 'children']);

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

        match ($sort) {
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'products_asc' => $query->orderBy('products_count')->orderByDesc('id'),
            'products_desc' => $query->orderByDesc('products_count')->orderByDesc('id'),
            'newest' => $query->orderByDesc('id'),
            'oldest' => $query->orderBy('id'),
            'order_desc' => $query->orderByDesc('display_order')->orderByDesc('name'),
            default => $query->orderBy('display_order')->orderBy('name'),
        };

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
                'sort' => $sort,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function ordersManagement(Request $request): View
    {
        $filters = $this->orderFilters($request);
        $query = $this->orderQuery($filters);

        return view('admin.order.index', [
            'orders' => $query->paginate($filters['per_page'])->withQueryString(),
            'stats' => [
                'total' => Order::count(),
                'pending' => Order::where('status', 'pending')->count(),
                'processing' => Order::whereIn('status', ['confirmed', 'processing'])->count(),
                'shipped' => Order::whereIn('status', ['shipping', 'shipped'])->count(),
                'completed' => Order::where('status', 'completed')->count(),
                'cancelled' => Order::where('status', 'cancelled')->count(),
            ],
            'filters' => $filters,
        ]);
    }

    public function ordersExport(Request $request): StreamedResponse
    {
        $filters = $this->orderFilters($request);
        $rows = $this->orderQuery($filters)
            ->limit(5000)
            ->get()
            ->map(static function (Order $order): array {
                $firstItem = $order->items->first();
                $payment = $order->payments->first();
                $placedAt = $order->placed_at ?? $order->created_at;

                return [
                    $order->code,
                    $placedAt?->format('d/m/Y H:i') ?? '',
                    $order->customer?->name ?? 'Khách vãng lai',
                    $order->customer?->phone ?? $order->customer?->email ?? '',
                    $firstItem?->product_name ?? '',
                    (int) $order->items_count,
                    (float) $order->total,
                    strtoupper((string) ($payment?->provider ?? 'COD')),
                    $order->status,
                    $order->payment_status,
                ];
            });

        return CsvExport::download('don-hang-' . now()->format('Ymd-His') . '.csv', [
            'Mã đơn',
            'Ngày tạo',
            'Khách hàng',
            'Liên hệ',
            'Sản phẩm đầu tiên',
            'Số dòng sản phẩm',
            'Tổng tiền',
            'Thanh toán',
            'Trạng thái đơn',
            'Trạng thái thanh toán',
        ], $rows);
    }

    private function orderFilters(Request $request): array
    {
        return [
            'q' => trim((string) $request->query('q', '')),
            'status' => (string) $request->query('status', 'all'),
            'payment_status' => (string) $request->query('payment_status', 'all'),
            'sort' => (string) $request->query('sort', 'newest'),
            'per_page' => max(1, min((int) $request->integer('per_page', 15), 50)),
        ];
    }

    private function orderQuery(array $filters): Builder
    {
        $query = Order::query()
            ->with(['customer', 'items', 'payments'])
            ->withCount('items');

        if ($filters['q'] !== '') {
            $query->where(function ($builder) use ($filters): void {
                $builder->where('code', 'like', '%' . $filters['q'] . '%')
                    ->orWhereHas('customer', function ($customerQuery) use ($filters): void {
                        $customerQuery->where('name', 'like', '%' . $filters['q'] . '%')
                            ->orWhere('email', 'like', '%' . $filters['q'] . '%')
                            ->orWhere('phone', 'like', '%' . $filters['q'] . '%');
                    })
                    ->orWhereHas('items', function ($itemQuery) use ($filters): void {
                        $itemQuery->where('product_name', 'like', '%' . $filters['q'] . '%');
                    });
            });
        }

        if ($filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if ($filters['payment_status'] !== 'all') {
            $query->where('payment_status', $filters['payment_status']);
        }

        match ($filters['sort']) {
            'oldest' => $query->orderBy('id'),
            'total_desc' => $query->orderByDesc('total')->orderByDesc('id'),
            'total_asc' => $query->orderBy('total')->orderByDesc('id'),
            'date_asc' => $query->orderBy('placed_at')->orderBy('id'),
            'date_desc' => $query->orderByDesc('placed_at')->orderByDesc('id'),
            default => $query->orderByDesc('id'),
        };

        return $query;
    }
}
