<?php

namespace App\Http\Controllers\Api\Admin;

use App\Application\Order\Services\OrderInventoryService;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    private const ORDER_STATUSES = [
        'pending',
        'confirmed',
        'processing',
        'shipped',
        'delivered',
        'cancelled',
        'refunded',
        'completed',
    ];

    private const PAYMENT_STATUSES = [
        'pending',
        'processing',
        'paid',
        'succeeded',
        'failed',
        'refunded',
    ];

    private const FULFILLMENT_STATUSES = [
        'unfulfilled',
        'partial',
        'fulfilled',
    ];

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string', Rule::in(self::ORDER_STATUSES)],
            'payment_status' => ['nullable', 'string', Rule::in(self::PAYMENT_STATUSES)],
            'fulfillment_status' => ['nullable', 'string', Rule::in(self::FULFILLMENT_STATUSES)],
            'customer_id' => ['nullable', 'integer', 'min:1'],
            'code' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
        $filters = collect($validated)->only(['status', 'payment_status', 'fulfillment_status', 'customer_id', 'code'])->toArray();
        $perPage = max(1, min((int) ($validated['per_page'] ?? 15), 100));

        $query = Order::query()->with(['customer', 'items']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (! empty($filters['fulfillment_status'])) {
            $query->where('fulfillment_status', $filters['fulfillment_status']);
        }

        if (! empty($filters['customer_id'])) {
            $query->where('customer_id', (int) $filters['customer_id']);
        }

        if (! empty($filters['code'])) {
            $query->where('code', 'like', '%' . $filters['code'] . '%');
        }

        $orders = $query->orderByDesc('id')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($orders->items()),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $order = Order::with(['customer', 'address', 'items', 'shipping', 'payments'])->find($id);

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order),
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['sometimes', 'string', Rule::in(self::ORDER_STATUSES)],
            'payment_status' => ['sometimes', 'string', Rule::in(self::PAYMENT_STATUSES)],
            'fulfillment_status' => ['sometimes', 'string', Rule::in(self::FULFILLMENT_STATUSES)],
        ]);

        if ($validated === []) {
            return response()->json([
                'success' => false,
                'message' => 'Cần cung cấp ít nhất một trường trạng thái.',
                'errors' => [
                    'status' => ['Provide status, payment_status, or fulfillment_status.'],
                ],
            ], 422);
        }

        $order = Order::find($id);

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng.',
            ], 404);
        }

        DB::transaction(function () use ($order, $validated): void {
            if (($validated['status'] ?? null) === 'cancelled' && $order->status !== 'cancelled') {
                $inventoryService = app(OrderInventoryService::class);

                if ($inventoryService->hasReservedInventory($order)) {
                    $inventoryService->release($order, 'admin_api_status_cancelled');
                } elseif (in_array($order->status, ['confirmed', 'processing', 'shipping', 'shipped'], true)) {
                    $inventoryService->restoreUntracked($order, 'admin_api_status_cancelled_legacy_untracked');
                }
            }

            $order->fill($validated);
            $order->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái đơn hàng thành công.',
            'data' => new OrderResource($order->load(['customer', 'items', 'shipping', 'payments'])),
        ]);
    }
}
