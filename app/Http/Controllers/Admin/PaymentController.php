<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Order\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    private const STATUSES = ['pending', 'processing', 'succeeded', 'failed', 'refunded'];

    public function index(Request $request): View
    {
        $perPage = max(1, min((int) $request->integer('per_page', 15), 50));
        $search = trim((string) $request->query('q', ''));
        $method = (string) $request->query('method', 'all');
        $status = (string) $request->query('status', 'all');
        $dateFrom = $request->date('date_from');
        $dateTo = $request->date('date_to');

        $query = Payment::query()
            ->with(['order:id,code,customer_id,total,status,payment_status,placed_at', 'order.customer:id,name,email,phone'])
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('reference', 'like', '%' . $search . '%')
                    ->orWhere('provider', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhereHas('order', fn ($orderQuery) => $orderQuery->where('code', 'like', '%' . $search . '%'))
                    ->orWhereHas('order.customer', function ($customerQuery) use ($search): void {
                        $customerQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });

                if (ctype_digit($search)) {
                    $builder->orWhere('id', (int) $search);
                }
            });
        }

        if ($method !== 'all') {
            $query->where('provider', $method);
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

        $methods = Payment::query()
            ->select('provider')
            ->whereNotNull('provider')
            ->distinct()
            ->orderBy('provider')
            ->pluck('provider')
            ->filter()
            ->values();

        return view('admin.transactions.index', [
            'payments' => $query->paginate($perPage)->withQueryString(),
            'methods' => $methods,
            'statuses' => self::STATUSES,
            'stats' => [
                'total' => Payment::count(),
                'pending' => Payment::where('status', 'pending')->count(),
                'processing' => Payment::where('status', 'processing')->count(),
                'succeeded' => Payment::where('status', 'succeeded')->count(),
                'failed_or_refunded' => Payment::whereIn('status', ['failed', 'refunded'])->count(),
                'total_amount' => (float) Payment::sum('amount'),
                'total_fee' => (float) Payment::sum('fee'),
            ],
            'filters' => [
                'q' => $search,
                'method' => $method,
                'status' => $status,
                'date_from' => $dateFrom?->toDateString(),
                'date_to' => $dateTo?->toDateString(),
                'per_page' => $perPage,
            ],
        ]);
    }
}
