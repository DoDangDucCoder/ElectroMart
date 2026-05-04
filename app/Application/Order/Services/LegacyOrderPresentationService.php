<?php

namespace App\Application\Order\Services;

use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use App\Infrastructure\Persistence\Eloquent\Order\OrderItem;
use App\Infrastructure\Persistence\Eloquent\Order\OrderShipping;
use App\Infrastructure\Persistence\Eloquent\Order\Payment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class LegacyOrderPresentationService
{
    public function loadOrderPackage(string $orderCode): ?array
    {
        if (! $this->modernOrderTablesReady()) {
            return null;
        }

        $order = Order::with([
            'customer',
            'items.product',
            'shipping.city',
            'shipping.province',
            'shipping.ward',
            'payments',
        ])->where('code', $orderCode)->first();

        if (! $order) {
            return null;
        }

        $details = $order->items
            ->map(fn (OrderItem $item): object => $this->legacyOrderDetailRecord($item, $order));

        return [
            'order' => $this->legacyOrderRecord($order),
            'customer' => $this->legacyCustomerRecord($order->customer, $order->shipping),
            'shipping' => $this->legacyShippingRecord($order->shipping, $order->payments->first()),
            'details' => $details,
            'totals' => $this->calculateTotals($orderCode, $details),
        ];
    }

    public function calculateTotals(string $orderCode, $details = null): array
    {
        $order = $this->findModernOrder($orderCode);

        if ($details instanceof Collection) {
            $details = $details;
        } elseif ($order) {
            $details = $order->items
                ->map(fn (OrderItem $item): object => $this->legacyOrderDetailRecord($item, $order));
        } else {
            $details = collect();
        }

        $subtotal = (float) $details->sum(fn ($item) => (float) $item->product_price * (int) $item->product_sales_quantity);
        $firstDetail = $details->first();
        $shipping = (float) ($firstDetail->product_feeship ?? $order?->shipping_total ?? 0);
        $couponCode = $order?->coupon_code ?: $this->resolveCouponCode($details);
        $coupon = $this->resolveCouponTotals($couponCode, $subtotal, $order);

        return [
            'subtotal' => $subtotal,
            'discount' => $coupon['discount'],
            'shipping' => $shipping,
            'total' => max(0, $subtotal + $shipping - $coupon['discount']),
            'items_count' => (int) $details->sum('product_sales_quantity'),
            'coupon_code' => $couponCode ?: 'no',
            'coupon_condition' => $coupon['condition'],
            'coupon_number' => $coupon['number'],
            'coupon_label' => $coupon['label'],
        ];
    }

    public function renderPrintableHtml(string $orderCode): ?string
    {
        $package = $this->loadOrderPackage($orderCode);
        if (! $package) {
            return null;
        }

        return view('admin.order.print', [
            'printOrder' => $package,
        ])->render();
    }

    public function legacyOrderRecord(Order $order): object
    {
        $placedAt = $order->placed_at ?: $order->created_at;

        return (object) [
            'order_id' => $order->id,
            'order_code' => $order->code,
            'customer_id' => $order->customer_id,
            'shipping_id' => $order->shipping?->id,
            'order_status' => $this->legacyStatusFromModern($order->status),
            'order_destroy' => data_get($order->meta, 'cancel_reason'),
            'order_date' => $placedAt?->toDateString(),
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
        ];
    }

    public function legacyCustomerRecord(?Customer $customer, ?OrderShipping $shipping = null): object
    {
        return (object) [
            'customer_id' => $customer?->id,
            'customer_name' => $customer?->name ?? $shipping?->recipient_name ?? 'Khách hàng',
            'customer_phone' => $customer?->phone ?? $shipping?->recipient_phone,
            'customer_email' => $customer?->email ?? $shipping?->email,
        ];
    }

    public function legacyShippingRecord(?OrderShipping $shipping, ?Payment $payment = null): object
    {
        return (object) [
            'shipping_id' => $shipping?->id,
            'shipping_name' => $shipping?->recipient_name,
            'shipping_address' => $this->formatModernShippingAddress($shipping),
            'shipping_phone' => $shipping?->recipient_phone,
            'shipping_email' => $shipping?->email,
            'shipping_notes' => $shipping?->address_line2,
            'shipping_method' => $this->legacyPaymentMethod($payment?->provider ?: $shipping?->shipping_method),
        ];
    }

    public function legacyOrderDetailRecord(OrderItem $item, Order $order): object
    {
        return (object) [
            'order_details_id' => $item->id,
            'order_code' => $order->code,
            'product_id' => (int) $item->product_id,
            'product_name' => $item->product_name,
            'product_attribute' => $this->formatOrderItemAttributes($item->attributes),
            'product_coupon' => $order->coupon_code ?: 'no',
            'product_feeship' => (float) $order->shipping_total,
            'product_sales_quantity' => (int) $item->quantity,
            'product_price' => (float) $item->price,
            'product' => $item->product,
        ];
    }

    public function legacyStatusFromModern(?string $status): int
    {
        return match ($status) {
            'completed' => 2,
            'cancelled', 'refunded' => 3,
            default => 1,
        };
    }

    public function modernOrderTablesReady(): bool
    {
        return Schema::hasTable('orders')
            && Schema::hasTable('order_items')
            && Schema::hasTable('order_shipping')
            && Schema::hasTable('customers');
    }

    protected function findModernOrder(string $orderCode): ?Order
    {
        if (! $this->modernOrderTablesReady()) {
            return null;
        }

        return Order::with([
            'items.product',
            'shipping.city',
            'shipping.province',
            'shipping.ward',
            'payments',
        ])->where('code', $orderCode)->first();
    }

    protected function resolveCouponCode(Collection $details): string
    {
        foreach ($details as $detail) {
            $couponCode = (string) ($detail->product_coupon ?? 'no');
            if ($couponCode !== '' && $couponCode !== 'no') {
                return $couponCode;
            }
        }

        return 'no';
    }

    protected function resolveCouponTotals(?string $couponCode, float $subtotal, ?Order $order): array
    {
        $couponCode = trim((string) $couponCode);

        if ($couponCode === '' || $couponCode === 'no') {
            $discount = (float) ($order?->discount_total ?? 0);

            return [
                'condition' => 2,
                'number' => $discount,
                'discount' => $discount,
                'label' => $discount > 0 ? number_format($discount, 0, ',', '.').'đ' : '0',
            ];
        }

        if (Schema::hasTable((new Coupon())->getTable())) {
            $coupon = Coupon::where('code', $couponCode)->first();

            if ($coupon) {
                $condition = $coupon->type === 'percent' ? 1 : 2;
                $number = (float) $coupon->value;
                $discount = $condition === 1 ? ($subtotal * $number) / 100 : $number;

                if ($condition === 1 && $coupon->max_discount !== null) {
                    $discount = min($discount, (float) $coupon->max_discount);
                }

                return [
                    'condition' => $condition,
                    'number' => $number,
                    'discount' => $discount,
                    'label' => $condition === 1
                        ? rtrim(rtrim(number_format($number, 2, '.', ''), '0'), '.').'%'
                        : number_format($number, 0, ',', '.').'đ',
                ];
            }
        }

        $discount = (float) ($order?->discount_total ?? 0);

        return [
            'condition' => 2,
            'number' => $discount,
            'discount' => $discount,
            'label' => $discount > 0 ? number_format($discount, 0, ',', '.').'đ' : '0',
        ];
    }

    protected function formatModernShippingAddress(?OrderShipping $shipping): string
    {
        if (! $shipping) {
            return 'Chưa có địa chỉ giao hàng';
        }

        $parts = array_filter([
            $shipping->address_line1,
            $shipping->ward?->name,
            $shipping->province?->name,
            $shipping->city?->name,
            $shipping->country,
        ]);

        return implode(', ', $parts) ?: 'Chưa có địa chỉ giao hàng';
    }

    protected function formatOrderItemAttributes($attributes): string
    {
        if (empty($attributes)) {
            return 'Mặc định';
        }

        if (is_string($attributes)) {
            return $attributes;
        }

        if (isset($attributes['attribute']) && is_string($attributes['attribute'])) {
            return $attributes['attribute'];
        }

        $parts = [];
        foreach ((array) $attributes as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }

            $parts[] = is_string($key)
                ? ucfirst(str_replace('_', ' ', $key)).': '.$value
                : $value;
        }

        return implode(' | ', array_filter($parts)) ?: 'Mặc định';
    }

    protected function legacyPaymentMethod(?string $provider): int
    {
        return match ($provider) {
            'bank_transfer' => 0,
            'cod', 'cash', null, '' => 1,
            default => 2,
        };
    }
}
