<?php

namespace App\Application\Order\Services;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Shipping;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class LegacyOrderPresentationService
{
    public function loadOrderPackage(string $orderCode): ?array
    {
        if (
            ! Schema::hasTable((new Order())->getTable()) ||
            ! Schema::hasTable((new OrderDetails())->getTable()) ||
            ! Schema::hasTable((new Customer())->getTable()) ||
            ! Schema::hasTable((new Shipping())->getTable())
        ) {
            return null;
        }

        $order = Order::where('order_code', $orderCode)->first();
        if (! $order) {
            return null;
        }

        $details = OrderDetails::with('product')
            ->where('order_code', $orderCode)
            ->get();

        return [
            'order' => $order,
            'customer' => Customer::where('customer_id', $order->customer_id)->first(),
            'shipping' => Shipping::where('shipping_id', $order->shipping_id)->first(),
            'details' => $details,
            'totals' => $this->calculateTotals($orderCode, $details),
        ];
    }

    public function calculateTotals(string $orderCode, $details = null): array
    {
        $details = $details instanceof Collection
            ? $details
            : OrderDetails::where('order_code', $orderCode)->get();

        $subtotal = (float) $details->sum(fn ($item) => $item->product_price * $item->product_sales_quantity);
        $firstDetail = $details->first();
        $shipping = (float) ($firstDetail->product_feeship ?? 0);
        $couponCode = $this->resolveCouponCode($details);
        $couponCondition = 2;
        $couponNumber = 0.0;
        $couponLabel = '0';
        $discount = 0.0;

        if ($couponCode && $couponCode !== 'no' && Schema::hasTable((new Coupon())->getTable())) {
            $coupon = Coupon::where('coupon_code', $couponCode)->first();

            if ($coupon) {
                $couponCondition = (int) $coupon->coupon_condition;
                $couponNumber = (float) $coupon->coupon_number;

                if ($couponCondition === 1) {
                    $discount = ($subtotal * $couponNumber) / 100;
                    $couponLabel = rtrim(rtrim(number_format($couponNumber, 2, '.', ''), '0'), '.') . '%';
                } else {
                    $discount = $couponNumber;
                    $couponLabel = number_format($couponNumber, 0, ',', '.') . 'đ';
                }
            }
        }

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping' => $shipping,
            'total' => max(0, $subtotal + $shipping - $discount),
            'items_count' => (int) $details->sum('product_sales_quantity'),
            'coupon_code' => $couponCode,
            'coupon_condition' => $couponCondition,
            'coupon_number' => $couponNumber,
            'coupon_label' => $couponLabel,
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
}
