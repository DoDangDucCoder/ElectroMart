<?php

namespace App\Application\Order\Services;

use App\Models\Order;
use App\Models\Shipping;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LegacyOrderHistoryService
{
    public function __construct(
        protected LegacyOrderPresentationService $presentationService
    ) {
    }

    public function paginateCustomerOrders(int $customerId, int $perPage = 10): LengthAwarePaginator
    {
        $orders = Order::where('customer_id', $customerId)
            ->orderByDesc('order_id')
            ->paginate($perPage);

        $shippingById = $this->shippingMapForOrders($orders->getCollection());

        $orders->setCollection(
            $orders->getCollection()->map(fn (Order $order) => $this->mapHistoryOrder($order, $shippingById->get((int) $order->shipping_id)))
        );

        return $orders;
    }

    public function buildDetailView(Order $order): array
    {
        $package = $this->presentationService->loadOrderPackage($order->order_code);
        $orderDetails = $package['details'] ?? collect();
        $customer = $package['customer'] ?? null;
        $shipping = $package['shipping'] ?? null;
        $totals = $package['totals'] ?? $this->presentationService->calculateTotals($order->order_code, $orderDetails);

        return [
            'code' => $order->order_code,
            'placed_at_label' => $order->created_at ? Carbon::parse($order->created_at)->format('d/m/Y H:i') : 'Chưa xác định',
            'status' => $this->statusKey((int) $order->order_status),
            'status_label' => $this->formatStatusLabel((int) $order->order_status),
            'can_cancel' => $this->canCancel($order),
            'cancel_reason' => $order->order_destroy ?? null,
            'customer' => [
                'name' => $customer?->customer_name ?? 'Khách hàng',
                'phone' => $customer?->customer_phone,
                'email' => $customer?->customer_email,
            ],
            'shipping' => [
                'name' => $shipping?->shipping_name,
                'phone' => $shipping?->shipping_phone,
                'email' => $shipping?->shipping_email,
                'address' => $shipping?->shipping_address,
                'notes' => $shipping?->shipping_notes,
                'method_label' => $this->formatShippingMethodLabel($shipping?->shipping_method),
                'tracking_number' => null,
                'shipping_fee' => (float) $totals['shipping'],
            ],
            'payment' => [
                'provider_label' => $this->formatShippingMethodLabel($shipping?->shipping_method),
                'status_label' => (int) $order->order_status === 3 ? 'Đã hủy' : 'Chưa xác định',
                'amount' => (float) $totals['total'],
                'reference' => null,
            ],
            'items' => $orderDetails->map(function ($item) {
                return [
                    'name' => $item->product_name,
                    'attributes' => $item->product_attribute ?: 'Mặc định',
                    'quantity' => (int) $item->product_sales_quantity,
                    'price' => (float) $item->product_price,
                    'subtotal' => (float) $item->product_price * (int) $item->product_sales_quantity,
                    'image' => $item->product?->product_image,
                ];
            }),
            'totals' => $totals,
            'coupon_code' => $totals['coupon_code'] !== 'no' ? $totals['coupon_code'] : null,
            'note' => null,
            'source' => 'legacy',
        ];
    }

    public function canCancel(Order $order): bool
    {
        return ! in_array((int) $order->order_status, [2, 3], true);
    }

    protected function mapHistoryOrder(Order $order, ?Shipping $shipping = null): array
    {
        $totals = $this->presentationService->calculateTotals($order->order_code);
        $createdAt = $order->created_at ? Carbon::parse($order->created_at) : null;

        return [
            'code' => $order->order_code,
            'placed_at_label' => $createdAt ? $createdAt->format('d/m/Y H:i') : 'Chưa xác định',
            'status' => $this->statusKey((int) $order->order_status),
            'status_label' => $this->formatStatusLabel((int) $order->order_status),
            'payment_label' => $this->formatShippingMethodLabel($shipping?->shipping_method),
            'payment_status_label' => (int) $order->order_status === 3 ? 'Đã hủy' : 'Chưa xác định',
            'items_count' => (int) $totals['items_count'],
            'total' => (float) $totals['total'],
            'can_cancel' => $this->canCancel($order),
            'cancel_reason' => $order->order_destroy ?? null,
        ];
    }

    protected function shippingMapForOrders(Collection $orders): Collection
    {
        $shippingIds = $orders->pluck('shipping_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($shippingIds->isEmpty()) {
            return collect();
        }

        return Shipping::whereIn('shipping_id', $shippingIds)->get()->keyBy(fn ($shipping) => (int) $shipping->shipping_id);
    }

    protected function statusKey(int $status): string
    {
        return match ($status) {
            2 => 'completed',
            3 => 'cancelled',
            default => 'pending',
        };
    }

    protected function formatStatusLabel(int $status): string
    {
        return match ($status) {
            1 => 'Đơn hàng mới',
            2 => 'Đã xử lý - Đã giao hàng',
            3 => 'Đơn hàng đã bị hủy',
            default => 'Chưa xác định',
        };
    }

    protected function formatShippingMethodLabel($method): string
    {
        return match ((string) $method) {
            '0' => 'Chuyển khoản',
            '1' => 'Tiền mặt',
            '2' => 'Paypal',
            default => 'Chưa xác định',
        };
    }
}
