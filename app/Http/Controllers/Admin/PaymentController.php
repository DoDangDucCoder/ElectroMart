<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Order\Payment;
use App\Support\CsvExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    private const STATUSES = ['pending', 'processing', 'succeeded', 'failed', 'refunded'];

    public function index(Request $request): View
    {
        $filters = $this->filters($request);
        $query = $this->paymentQuery($filters);

        $methods = Payment::query()
            ->select('provider')
            ->whereNotNull('provider')
            ->distinct()
            ->orderBy('provider')
            ->pluck('provider')
            ->filter()
            ->values();

        return view('admin.transactions.index', [
            'payments' => $query->paginate($filters['per_page'])->withQueryString(),
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
            'filters' => $filters,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $this->filters($request);
        $rows = $this->paymentQuery($filters)
            ->limit(5000)
            ->get()
            ->map(fn (Payment $payment): array => [
                $payment->id,
                $payment->reference ?: '',
                $payment->order?->code ?: '',
                $payment->order?->customer?->name ?: '',
                $payment->provider,
                $payment->status,
                (float) $payment->amount,
                $payment->currency,
                $payment->created_at?->format('d/m/Y H:i') ?? '',
                $payment->paid_at?->format('d/m/Y H:i') ?? '',
            ]);

        return CsvExport::download('giao-dich-' . now()->format('Ymd-His') . '.csv', [
            'Mã thanh toán',
            'Mã tham chiếu',
            'Mã đơn',
            'Khách hàng',
            'Phương thức',
            'Trạng thái',
            'Số tiền',
            'Tiền tệ',
            'Ngày tạo',
            'Ngày thanh toán',
        ], $rows);
    }

    private function filters(Request $request): array
    {
        return [
            'q' => trim((string) $request->query('q', '')),
            'method' => (string) $request->query('method', 'all'),
            'status' => (string) $request->query('status', 'all'),
            'date_from' => $request->date('date_from')?->toDateString(),
            'date_to' => $request->date('date_to')?->toDateString(),
            'sort' => (string) $request->query('sort', 'newest'),
            'per_page' => max(1, min((int) $request->integer('per_page', 15), 50)),
        ];
    }

    private function paymentQuery(array $filters): Builder
    {
        $query = Payment::query()
            ->with(['order:id,code,customer_id,total,status,payment_status,placed_at', 'order.customer:id,name,email,phone']);

        if ($filters['q'] !== '') {
            $query->where(function ($builder) use ($filters): void {
                $builder->where('reference', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('provider', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('status', 'like', '%' . $filters['q'] . '%')
                    ->orWhereHas('order', fn ($orderQuery) => $orderQuery->where('code', 'like', '%' . $filters['q'] . '%'))
                    ->orWhereHas('order.customer', function ($customerQuery) use ($filters): void {
                        $customerQuery->where('name', 'like', '%' . $filters['q'] . '%')
                            ->orWhere('email', 'like', '%' . $filters['q'] . '%');
                    });

                if (ctype_digit($filters['q'])) {
                    $builder->orWhere('id', (int) $filters['q']);
                }
            });
        }

        if ($filters['method'] !== 'all') {
            $query->where('provider', $filters['method']);
        }

        if (in_array($filters['status'], self::STATUSES, true)) {
            $query->where('status', $filters['status']);
        }

        if ($filters['date_from']) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if ($filters['date_to']) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        match ($filters['sort']) {
            'oldest' => $query->orderBy('created_at')->orderBy('id'),
            'amount_desc' => $query->orderByDesc('amount')->orderByDesc('id'),
            'amount_asc' => $query->orderBy('amount')->orderByDesc('id'),
            'date_asc' => $query->orderBy('created_at')->orderBy('id'),
            'date_desc' => $query->orderByDesc('created_at')->orderByDesc('id'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };

        return $query;
    }
}
