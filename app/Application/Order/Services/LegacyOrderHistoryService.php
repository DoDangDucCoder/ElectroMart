<?php

namespace App\Application\Order\Services;

use App\Infrastructure\Persistence\Eloquent\Order\Order;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class LegacyOrderHistoryService
{
    public function __construct(
        protected LegacyOrderPresentationService $presentationService
    ) {
    }

    public function paginateCustomerOrders(int $customerId, int $perPage = 10): LengthAwarePaginator
    {
        if (! $this->presentationService->modernOrderTablesReady()) {
            return $this->emptyPaginator($perPage);
        }

        $orders = Order::with(['items', 'shipping', 'payments'])
            ->where('customer_id', $customerId)
            ->orderByDesc('id')
            ->paginate($perPage);

        $orders->setCollection(
            $orders->getCollection()->map(fn (Order $order): array => $this->mapHistoryOrder($order))
        );

        return $orders;
    }

    public function buildDetailView(Order $order): array
    {
        $package = $this->presentationService->loadOrderPackage($order->code);
        $orderDetails = $package['details'] ?? collect();
        $customer = $package['customer'] ?? null;
        $shipping = $package['shipping'] ?? null;
        $totals = $package['totals'] ?? $this->presentationService->calculateTotals($order->code, $orderDetails);

        return [
            'code' => $order->code,
            'placed_at_label' => ($order->placed_at ?: $order->created_at)
                ? ($order->placed_at ?: $order->created_at)->format('d/m/Y H:i')
                : 'Chưa xác định',
            'status' => $this->statusKey($order->status),
            'status_label' => $this->formatStatusLabel($order->status),
            'can_cancel' => $this->canCancel($order),
            'cancel_reason' => data_get($order->meta, 'cancel_reason'),
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
                'status_label' => $order->status === 'cancelled' ? 'Đã hủy' : 'Chưa xác định',
                'amount' => (float) $totals['total'],
                'reference' => null,
            ],
            'items' => $orderDetails->map(function ($item): array {
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
            'note' => $order->note,
            'source' => $order->source,
        ];
    }

    public function canCancel(Order $order): bool
    {
        return ! in_array($order->status, ['completed', 'cancelled', 'refunded'], true);
    }

    protected function mapHistoryOrder(Order $order): array
    {
        $totals = $this->presentationService->calculateTotals($order->code);
        $createdAt = $order->placed_at ?: ($order->created_at ? Carbon::parse($order->created_at) : null);
        $payment = $order->payments->first();

        return [
            'code' => $order->code,
            'placed_at_label' => $createdAt ? $createdAt->format('d/m/Y H:i') : 'Chưa xác định',
            'status' => $this->statusKey($order->status),
            'status_label' => $this->formatStatusLabel($order->status),
            'payment_label' => $this->formatModernPaymentProviderLabel($payment?->provider),
            'payment_status_label' => $this->formatModernPaymentStatusLabel($payment?->status),
            'items_count' => (int) $totals['items_count'],
            'total' => (float) $totals['total'],
            'can_cancel' => $this->canCancel($order),
            'cancel_reason' => data_get($order->meta, 'cancel_reason'),
        ];
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

    protected function statusKey(?string $status): string
    {
        return match ($status) {
            'confirmed', 'processing' => 'processing',
            'shipping', 'shipped' => 'shipping',
            'completed' => 'completed',
            'cancelled', 'refunded' => 'cancelled',
            default => 'pending',
        };
    }

    protected function formatStatusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Chờ xử lý',
            'completed' => 'Đã giao hàng',
            'cancelled' => 'Đơn hàng đã bị hủy',
            'refunded' => 'Đã hoàn tiền',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang xử lý',
            'shipping', 'shipped' => 'Đang giao hàng',
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

    protected function formatModernPaymentProviderLabel(?string $provider): string
    {
        return match ($provider) {
            'cod', 'cash' => 'Thanh toán khi nhận hàng',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'paypal' => 'PayPal',
            'momo' => 'MoMo',
            'vnpay' => 'VNPay',
            null, '' => 'Chưa xác định',
            default => strtoupper($provider),
        };
    }

    protected function formatModernPaymentStatusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Chờ thanh toán',
            'processing' => 'Đang xử lý',
            'succeeded', 'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại',
            'refunded' => 'Đã hoàn tiền',
            null, '' => 'Chưa xác định',
            default => ucfirst($status),
        };
    }
}
