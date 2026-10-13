<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Support\CsvExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filters($request);
        $query = $this->customerQuery($filters);

        return view('admin.customer.index', [
            'customers' => $query->paginate($filters['per_page'])->withQueryString(),
            'filters' => $filters,
            'stats' => [
                'total' => Customer::query()->count(),
                'active' => Customer::query()->where('is_active', true)->count(),
                'inactive' => Customer::query()->where('is_active', false)->count(),
                'new_this_month' => Customer::query()
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count(),
                'returning' => Customer::query()
                    ->whereIn('id', DB::table('orders')
                        ->select('customer_id')
                        ->whereNotNull('customer_id')
                        ->groupBy('customer_id')
                        ->havingRaw('COUNT(*) >= 2'))
                    ->count(),
            ],
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $this->filters($request);
        $rows = $this->customerQuery($filters)
            ->limit(5000)
            ->get()
            ->map(fn (Customer $customer): array => [
                $customer->id,
                $customer->name,
                $customer->email,
                $customer->phone ?: '',
                $customer->created_at?->format('d/m/Y H:i') ?? '',
                (int) $customer->orders_count,
                (float) ($customer->total_spending ?? 0),
                $customer->is_active ? 'Hoạt động' : 'Tạm khóa',
            ]);

        return CsvExport::download('khach-hang-' . now()->format('Ymd-His') . '.csv', [
            'Mã khách hàng',
            'Tên khách hàng',
            'Email',
            'Số điện thoại',
            'Ngày tạo',
            'Số đơn hàng',
            'Tổng chi tiêu',
            'Trạng thái',
        ], $rows);
    }

    private function filters(Request $request): array
    {
        return [
            'q' => trim((string) $request->query('q', '')),
            'status' => (string) $request->query('status', 'all'),
            'date_from' => $request->date('date_from')?->toDateString(),
            'date_to' => $request->date('date_to')?->toDateString(),
            'sort' => (string) $request->query('sort', 'newest'),
            'per_page' => max(1, min((int) $request->integer('per_page', 15), 50)),
        ];
    }

    private function customerQuery(array $filters): Builder
    {
        $query = Customer::query()
            ->withCount('orders')
            ->withSum('orders as total_spending', 'total');

        if ($filters['q'] !== '') {
            $query->where(function (Builder $builder) use ($filters): void {
                $builder->where('name', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('phone', 'like', '%' . $filters['q'] . '%');
            });
        }

        if ($filters['status'] === 'active') {
            $query->where('is_active', true);
        } elseif ($filters['status'] === 'inactive') {
            $query->where('is_active', false);
        }

        if ($filters['date_from']) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if ($filters['date_to']) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        match ($filters['sort']) {
            'spending_desc' => $query->orderByDesc('total_spending')->orderByDesc('id'),
            'spending_asc' => $query->orderBy('total_spending')->orderByDesc('id'),
            'orders_desc' => $query->orderByDesc('orders_count')->orderByDesc('id'),
            'orders_asc' => $query->orderBy('orders_count')->orderByDesc('id'),
            'name_asc' => $query->orderBy('name')->orderByDesc('id'),
            'name_desc' => $query->orderByDesc('name')->orderByDesc('id'),
            'oldest' => $query->orderBy('created_at')->orderBy('id'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };

        return $query;
    }

    public function show(Customer $customer): View
    {
        $customer->load([
            'addresses' => fn ($query) => $query->orderByDesc('is_default')->orderByDesc('created_at'),
            'orders' => fn ($query) => $query->withCount('items')->orderByDesc('created_at')->limit(10),
            'paymentMethods' => fn ($query) => $query->orderByDesc('is_default')->orderByDesc('created_at'),
            'vatInvoices' => fn ($query) => $query->with('order')->orderByDesc('created_at')->limit(10),
            'orderReturns' => fn ($query) => $query->with('order')->orderByDesc('created_at')->limit(10),
        ]);

        $orderStats = $customer->orders()
            ->selectRaw('COUNT(*) as orders_count, COALESCE(SUM(total), 0) as total_spending')
            ->first();

        return view('admin.customer.show', [
            'customer' => $customer,
            'orderStats' => [
                'orders_count' => (int) ($orderStats?->orders_count ?? 0),
                'total_spending' => (float) ($orderStats?->total_spending ?? 0),
            ],
            'safeMeta' => $this->safeMeta($customer->meta ?? []),
        ]);
    }

    public function toggleStatus(Customer $customer): \Illuminate\Http\RedirectResponse
    {
        $newStatus = ! $customer->is_active;
        $customer->update(['is_active' => $newStatus]);

        $label = $newStatus ? 'mở khóa' : 'khóa';

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', "Đã {$label} tài khoản khách hàng {$customer->name}.");
    }

    private function safeMeta(array $meta): array
    {
        $blocked = [
            'password',
            'password_reset_token',
            'password_reset_token_hash',
            'password_reset_requested_at',
            'reset_token',
            'token',
            'remember_token',
            'secret',
        ];

        return collect($meta)
            ->reject(fn ($value, string $key): bool => str_contains(strtolower($key), 'token')
                || str_contains(strtolower($key), 'password')
                || in_array(strtolower($key), $blocked, true))
            ->all();
    }
}
