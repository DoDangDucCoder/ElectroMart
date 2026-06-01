<?php

namespace App\Application\Order\Services;

use App\Application\Shared\Services\CacheService;
use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use App\Infrastructure\Persistence\Eloquent\Order\OrderItem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LegacyOrderWorkflowService
{
    public function __construct(
        protected LegacyOrderPresentationService $presentationService,
        protected CacheService $cacheService
    ) {
    }

    public function canManageOrders(): bool
    {
        return $this->presentationService->modernOrderTablesReady();
    }

    public function canRenderAdminOrderView(): bool
    {
        return $this->canManageOrders();
    }

    public function paginateAdminOrders(int $perPage = 5): LengthAwarePaginator
    {
        if (! $this->canManageOrders()) {
            return $this->emptyPaginator($perPage);
        }

        $orders = Order::with(['shipping', 'payments'])
            ->orderByDesc('id')
            ->paginate($perPage);

        $orders->setCollection(
            $orders->getCollection()
                ->map(fn (Order $order): object => $this->presentationService->legacyOrderRecord($order))
        );

        return $orders;
    }

    public function ordersForDate(string $orderDate)
    {
        if (! $this->canManageOrders()) {
            return collect();
        }

        return Order::with(['shipping', 'payments'])
            ->whereDate('placed_at', $orderDate)
            ->orWhere(function ($query) use ($orderDate) {
                $query->whereNull('placed_at')
                    ->whereDate('created_at', $orderDate);
            })
            ->orderByDesc('id')
            ->get()
            ->map(fn (Order $order): object => $this->presentationService->legacyOrderRecord($order));
    }

    public function deleteOrderByCode(string $orderCode): array
    {
        if (! $this->canManageOrders()) {
            return $this->result(false, 404, 'Dữ liệu đơn hàng hiện chưa sẵn sàng trên môi trường này.');
        }

        $order = Order::where('code', $orderCode)->first();
        if (! $order) {
            return $this->result(false, 404, 'Không tìm thấy đơn hàng cần xóa.');
        }

        $order->delete();

        return $this->result(true, 200, 'Xóa đơn hàng thành công');
    }

    public function updateOrderLineQuantity(string $orderCode, int $productId, int $quantity): array
    {
        if ($quantity < 0) {
            return $this->result(false, 422, 'Số lượng sản phẩm không được nhỏ hơn 0.');
        }

        if (! $this->canManageOrders()) {
            return $this->result(false, 404, 'Dữ liệu đơn hàng hiện chưa sẵn sàng trên môi trường này.');
        }

        $order = Order::with('items')->where('code', $orderCode)->first();
        if (! $order) {
            return $this->result(false, 404, 'Không tìm thấy đơn hàng cần cập nhật.');
        }

        if ($this->presentationService->legacyStatusFromModern($order->status) !== 1) {
            return $this->result(false, 422, 'Chỉ có thể cập nhật số lượng khi đơn hàng còn chờ xử lý.');
        }

        $orderItem = $order->items->first(fn (OrderItem $item): bool => (int) $item->product_id === $productId);
        if (! $orderItem) {
            return $this->result(false, 404, 'Không tìm thấy dòng sản phẩm cần cập nhật.');
        }

        DB::transaction(function () use ($order, $orderItem, $quantity): void {
            $orderItem->quantity = $quantity;
            $orderItem->subtotal = (float) $orderItem->price * $quantity;
            $orderItem->save();

            $this->refreshOrderTotals($order->fresh('items'));
        });

        return $this->result(true, 200, 'Cập nhật số lượng thành công.');
    }

    public function updateOrderStatus(int $orderId, int $newStatus, array $quantities, array $productIds): array
    {
        if (count($quantities) !== count($productIds)) {
            return $this->result(false, 422, 'Dữ liệu số lượng đơn hàng không đồng bộ.');
        }

        if (! $this->canManageOrders()) {
            return $this->result(false, 404, 'Dữ liệu đơn hàng hiện chưa sẵn sàng trên môi trường này.');
        }

        $order = Order::with(['items.product'])->find($orderId);
        if (! $order) {
            return $this->result(false, 404, 'Không tìm thấy đơn hàng cần cập nhật.');
        }

        $lineItemsResult = $this->resolveLineItems($order, $quantities, $productIds);
        if (! $lineItemsResult['ok']) {
            return $lineItemsResult;
        }

        $lineItems = $lineItemsResult['data'];
        $previousStatus = $this->presentationService->legacyStatusFromModern($order->status);
        $inventoryService = app(OrderInventoryService::class);
        $hasReservedInventory = $inventoryService->hasReservedInventory($order);
        $shouldReserveStock = $newStatus >= 2 && $newStatus <= 4 && $previousStatus === 1 && ! $hasReservedInventory;
        $shouldRestoreStock = $newStatus === 5 && (
            $hasReservedInventory || ($previousStatus >= 2 && $previousStatus <= 3)
        );

        if (in_array($previousStatus, [4, 5], true) && $newStatus !== $previousStatus) {
            return $this->result(false, 422, 'Không thể đổi trạng thái khi đơn hàng đã giao hoặc đã hủy.');
        }

        try {
            // Stock check + decrement phải nằm trong transaction với lock để chặn race condition
            DB::transaction(function () use ($lineItems, $order, $newStatus, $shouldReserveStock, $shouldRestoreStock, $hasReservedInventory): void {
                foreach ($lineItems as $lineItem) {
                    /** @var OrderItem $detail */
                    $detail = $lineItem['detail'];
                    $qty = $lineItem['qty'];

                    $detail->quantity = $qty;
                    $detail->subtotal = (float) $detail->price * $qty;
                    $detail->save();

                }

                if ($shouldReserveStock) {
                    app(OrderInventoryService::class)->reserve($order, 'admin_status_reserved');
                } elseif ($shouldRestoreStock) {
                    $inventoryService = app(OrderInventoryService::class);

                    if ($hasReservedInventory) {
                        $inventoryService->release($order, 'admin_status_cancelled');
                    } else {
                        $inventoryService->restoreUntracked($order, 'admin_status_cancelled_legacy_untracked');
                    }
                }

                $order->status = $this->modernStatusFromLegacy($newStatus);

                if ($newStatus === 2) {
                    $order->confirmed_at = $order->confirmed_at ?: now();
                    $order->fulfillment_status = 'unfulfilled';
                } elseif ($newStatus === 3) {
                    $order->confirmed_at = $order->confirmed_at ?: now();
                    $order->shipped_at = $order->shipped_at ?: now();
                    $order->fulfillment_status = 'partial';
                } elseif ($newStatus === 4) {
                    $order->fulfillment_status = 'fulfilled';
                    $order->payment_status = $order->payment_status === 'paid' ? 'paid' : $order->payment_status;
                    $order->confirmed_at = $order->confirmed_at ?: now();
                    $order->shipped_at = $order->shipped_at ?: now();
                    $order->completed_at = $order->completed_at ?: now();
                } elseif ($newStatus === 5) {
                    $order->fulfillment_status = 'unfulfilled';
                    $order->cancelled_at = $order->cancelled_at ?: now();
                }

                $order->save();
                $this->refreshOrderTotals($order->fresh('items'));
            });
        } catch (\RuntimeException $exception) {
            return $this->result(false, 422, $exception->getMessage());
        }

        return $this->result(true, 200, 'Thay đổi tình trạng đơn hàng thành công.');
    }

    public function buildAdminOrderViewData(string $orderCode): array
    {
        if (! $this->canRenderAdminOrderView()) {
            return $this->result(false, 404, 'Dữ liệu đơn hàng hiện chưa sẵn sàng trên môi trường này.');
        }

        $package = $this->presentationService->loadOrderPackage($orderCode);
        if (! $package) {
            return $this->result(false, 404, 'Không tìm thấy đơn hàng cần xem.');
        }

        $order = $package['order'];
        $totals = $package['totals'];

        return $this->result(true, 200, null, [
            'order_details' => $package['details'],
            'customer' => $package['customer'],
            'shipping' => $package['shipping'],
            'coupon_condition' => $totals['coupon_condition'],
            'coupon_number' => $totals['coupon_number'],
            'getorder' => collect([$order]),
            'order_status' => (int) $order->order_status,
        ]);
    }

    protected function resolveLineItems(Order $order, array $quantities, array $productIds): array
    {
        $orderDetails = $order->items->keyBy(fn (OrderItem $detail): int => (int) $detail->product_id);

        if ($orderDetails->isEmpty()) {
            return $this->result(false, 404, 'Không tìm thấy chi tiết đơn hàng cần cập nhật.');
        }

        $lineItems = [];

        foreach ($productIds as $index => $productId) {
            $productId = (int) $productId;
            $qty = (int) $quantities[$index];
            $detail = $orderDetails->get($productId);

            if ($qty < 0) {
                return $this->result(false, 422, 'Số lượng sản phẩm không được nhỏ hơn 0.');
            }

            if (! $detail) {
                return $this->result(false, 404, 'Không tìm thấy dòng sản phẩm cần cập nhật.');
            }

            $lineItems[] = [
                'detail' => $detail,
                'qty' => $qty,
            ];
        }

        return $this->result(true, 200, null, $lineItems);
    }

    protected function refreshOrderTotals(Order $order): void
    {
        $subtotal = (float) $order->items->sum(fn (OrderItem $item): float => (float) $item->subtotal);
        $discount = $this->calculateDiscountForOrder($order, $subtotal);

        $order->subtotal = $subtotal;
        $order->discount_total = $discount;
        $order->coupon_discount = $discount;
        $order->total = max(0, $subtotal + (float) $order->shipping_total - $discount);
        $order->save();
    }

    protected function calculateDiscountForOrder(Order $order, float $subtotal): float
    {
        $couponCode = trim((string) $order->coupon_code);

        if ($couponCode === '' || ! Schema::hasTable((new Coupon())->getTable())) {
            return (float) ($order->discount_total ?? 0);
        }

        $coupon = Coupon::where('code', $couponCode)->first();
        if (! $coupon) {
            return (float) ($order->discount_total ?? 0);
        }

        $discount = $coupon->type === 'percent'
            ? ($subtotal * (float) $coupon->value) / 100
            : (float) $coupon->value;

        if ($coupon->type === 'percent' && $coupon->max_discount !== null) {
            $discount = min($discount, (float) $coupon->max_discount);
        }

        return max(0, $discount);
    }

    protected function modernStatusFromLegacy(int $status): string
    {
        return match ($status) {
            2 => 'processing',
            3 => 'shipped',
            4 => 'completed',
            5 => 'cancelled',
            default => 'pending',
        };
    }

    protected function emptyPaginator(int $perPage): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            collect(),
            0,
            $perPage,
            LengthAwarePaginator::resolveCurrentPage(),
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );
    }

    protected function result(bool $ok, int $status, ?string $message = null, mixed $data = null): array
    {
        return [
            'ok' => $ok,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];
    }
}
