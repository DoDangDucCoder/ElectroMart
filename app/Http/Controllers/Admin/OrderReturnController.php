<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Order\OrderReturn;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class OrderReturnController extends Controller
{
    private const STATUSES = [
        OrderReturn::STATUS_PENDING,
        OrderReturn::STATUS_APPROVED,
        OrderReturn::STATUS_REJECTED,
        OrderReturn::STATUS_REFUNDED,
    ];

    public function index(Request $request): View
    {
        $perPage = max(1, min((int) $request->integer('per_page', 15), 50));
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $dateFrom = $request->date('date_from');
        $dateTo = $request->date('date_to');
        $sort = (string) $request->query('sort', 'newest');

        $query = OrderReturn::query()
            ->with([
                'customer:id,name,email,phone',
                'order:id,code,total,status,customer_id',
                'orderItem:id,order_id,product_name,sku,quantity,subtotal',
            ]);

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('code', 'like', '%' . $search . '%')
                    ->orWhere('reason', 'like', '%' . $search . '%')
                    ->orWhere('refund_method', 'like', '%' . $search . '%')
                    ->orWhereHas('order', fn ($orderQuery) => $orderQuery->where('code', 'like', '%' . $search . '%'))
                    ->orWhereHas('customer', function ($customerQuery) use ($search): void {
                        $customerQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('orderItem', function ($itemQuery) use ($search): void {
                        $itemQuery->where('product_name', 'like', '%' . $search . '%')
                            ->orWhere('sku', 'like', '%' . $search . '%');
                    });
            });
        }

        if (in_array($status, self::STATUSES, true)) {
            $query->where('status', $status);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom->toDateString());
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo->toDateString());
        }

        match ($sort) {
            'refund_desc' => $query->orderByDesc('refund_amount')->orderByDesc('id'),
            'refund_asc' => $query->orderBy('refund_amount')->orderByDesc('id'),
            'oldest' => $query->orderBy('created_at')->orderBy('id'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };

        return view('admin.returns.index', [
            'returns' => $query->paginate($perPage)->withQueryString(),
            'statuses' => self::STATUSES,
            'stats' => [
                'total' => OrderReturn::count(),
                'pending' => OrderReturn::where('status', OrderReturn::STATUS_PENDING)->count(),
                'approved' => OrderReturn::where('status', OrderReturn::STATUS_APPROVED)->count(),
                'rejected' => OrderReturn::where('status', OrderReturn::STATUS_REJECTED)->count(),
                'refunded' => OrderReturn::where('status', OrderReturn::STATUS_REFUNDED)->count(),
            ],
            'filters' => [
                'q' => $search,
                'status' => $status,
                'date_from' => $dateFrom?->toDateString(),
                'date_to' => $dateTo?->toDateString(),
                'sort' => $sort,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function updateStatus(Request $request, OrderReturn $orderReturn): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', self::STATUSES)],
        ]);

        $isRefunded = $data['status'] === OrderReturn::STATUS_REFUNDED;

        $orderReturn->update([
            'status' => $data['status'],
            'refunded_at' => $isRefunded
                ? ($orderReturn->refunded_at ?? Carbon::now())
                : null,
        ]);

        if ($isRefunded && $orderReturn->order) {
            $orderReturn->order->update([
                'payment_status' => 'refunded',
                'status' => 'refunded',
            ]);
        }

        return redirect()
            ->route('admin.returns', $request->query())
            ->with('message', 'Cập nhật trạng thái trả hàng thành công.');
    }
}
