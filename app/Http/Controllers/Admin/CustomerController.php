<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = max(1, min((int) $request->integer('per_page', 15), 50));
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $dateFrom = $request->date('date_from');
        $dateTo = $request->date('date_to');

        $query = Customer::query()
            ->withCount('orders')
            ->withSum('orders as total_spending', 'total')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom->toDateString());
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo->toDateString());
        }

        return view('admin.customer.index', [
            'customers' => $query->paginate($perPage)->withQueryString(),
            'filters' => [
                'q' => $search,
                'status' => $status,
                'date_from' => $dateFrom?->toDateString(),
                'date_to' => $dateTo?->toDateString(),
                'per_page' => $perPage,
            ],
            'stats' => [
                'total' => Customer::query()->count(),
                'active' => Customer::query()->where('is_active', true)->count(),
                'inactive' => Customer::query()->where('is_active', false)->count(),
                'new_this_month' => Customer::query()
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count(),
            ],
        ]);
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
