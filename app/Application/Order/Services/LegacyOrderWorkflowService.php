<?php

namespace App\Application\Order\Services;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\Shipping;
use App\Models\Statistic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LegacyOrderWorkflowService
{
    public function canManageOrders(): bool
    {
        return Schema::hasTable((new Order())->getTable())
            && Schema::hasTable((new OrderDetails())->getTable())
            && Schema::hasTable((new Product())->getTable());
    }

    public function canRenderAdminOrderView(): bool
    {
        return $this->canManageOrders()
            && Schema::hasTable((new Customer())->getTable())
            && Schema::hasTable((new Shipping())->getTable());
    }

    public function deleteOrderByCode(string $orderCode): array
    {
        if (! Schema::hasTable((new Order())->getTable())) {
            return $this->result(false, 404, 'Dữ liệu đơn hàng legacy hiện chưa sẵn sàng trên môi trường này.');
        }

        $order = Order::where('order_code', $orderCode)->first();
        if (! $order) {
            return $this->result(false, 404, 'Không tìm thấy đơn hàng cần xóa.');
        }

        DB::transaction(function () use ($order, $orderCode): void {
            if (Schema::hasTable((new OrderDetails())->getTable())) {
                OrderDetails::where('order_code', $orderCode)->delete();
            }

            $order->delete();
        });

        return $this->result(true, 200, 'Xóa đơn hàng thành công');
    }

    public function updateOrderLineQuantity(string $orderCode, int $productId, int $quantity): array
    {
        if (! $this->canManageOrders()) {
            return $this->result(false, 404, 'Dữ liệu đơn hàng legacy hiện chưa sẵn sàng trên môi trường này.');
        }

        $orderDetails = OrderDetails::where('product_id', $productId)
            ->where('order_code', $orderCode)
            ->first();

        if (! $orderDetails) {
            return $this->result(false, 404, 'Không tìm thấy dòng sản phẩm cần cập nhật.');
        }

        $orderDetails->product_sales_quantity = $quantity;
        $orderDetails->save();

        return $this->result(true, 200, 'Cập nhật số lượng thành công.');
    }

    public function updateOrderStatus(int $orderId, int $newStatus, array $quantities, array $productIds): array
    {
        if (count($quantities) !== count($productIds)) {
            return $this->result(false, 422, 'Dữ liệu số lượng đơn hàng không đồng bộ.');
        }

        if (! $this->canManageOrders()) {
            return $this->result(false, 404, 'Dữ liệu đơn hàng legacy hiện chưa sẵn sàng trên môi trường này.');
        }

        $order = Order::find($orderId);
        if (! $order) {
            return $this->result(false, 404, 'Không tìm thấy đơn hàng cần cập nhật.');
        }

        $lineItemsResult = $this->resolveLineItems($order, $quantities, $productIds);
        if (! $lineItemsResult['ok']) {
            return $lineItemsResult;
        }

        $lineItems = $lineItemsResult['data'];
        $previousStatus = (int) $order->order_status;
        $shouldFinalize = $newStatus === 2 && $previousStatus !== 2;

        foreach ($lineItems as $lineItem) {
            if (! $shouldFinalize) {
                continue;
            }

            $availableQuantity = (int) ($lineItem['product']->product_quantity ?? 0);
            if ($lineItem['qty'] > $availableQuantity) {
                return $this->result(false, 422, 'Số lượng bán trong kho không đủ.');
            }
        }

        DB::transaction(function () use ($lineItems, $order, $newStatus, $shouldFinalize): void {
            foreach ($lineItems as $lineItem) {
                $lineItem['detail']->product_sales_quantity = $lineItem['qty'];
                $lineItem['detail']->save();
            }

            $order->order_status = $newStatus;
            $order->save();

            if (! $shouldFinalize) {
                return;
            }

            $sales = 0.0;
            $profit = 0.0;
            $quantity = 0;

            foreach ($lineItems as $lineItem) {
                $product = $lineItem['product'];
                $qty = $lineItem['qty'];
                $productQuantity = (int) ($product->product_quantity ?? 0);
                $productSold = (int) ($product->product_sold ?? 0);
                $productPrice = (float) ($product->product_price ?? 0);
                $productCost = (float) ($product->price_cost ?? 0);

                $product->product_quantity = max(0, $productQuantity - $qty);
                $product->product_sold = $productSold + $qty;
                $product->save();

                $quantity += $qty;
                $sales += $productPrice * $qty;
                $profit += ($productPrice - $productCost) * $qty;
            }

            $this->recordStatistic($order->order_date, $sales, $profit, $quantity);
        });

        return $this->result(true, 200, 'Thay đổi tình trạng đơn hàng thành công.');
    }

    public function buildAdminOrderViewData(string $orderCode): array
    {
        if (! $this->canRenderAdminOrderView()) {
            return $this->result(false, 404, 'Dữ liệu đơn hàng legacy hiện chưa sẵn sàng trên môi trường này.');
        }

        $orderDetails = OrderDetails::with('product')->where('order_code', $orderCode)->get();
        $orders = Order::where('order_code', $orderCode)->get();

        if ($orders->isEmpty()) {
            return $this->result(false, 404, 'Không tìm thấy đơn hàng cần xem.');
        }

        $order = $orders->first();
        $customer = Customer::where('customer_id', $order->customer_id)->first();
        $shipping = Shipping::where('shipping_id', $order->shipping_id)->first();
        $couponMeta = $this->resolveCouponMeta($orderCode);

        return $this->result(true, 200, null, [
            'order_details' => $orderDetails,
            'customer' => $customer,
            'shipping' => $shipping,
            'coupon_condition' => $couponMeta['coupon_condition'],
            'coupon_number' => $couponMeta['coupon_number'],
            'getorder' => $orders,
            'order_status' => (int) $order->order_status,
        ]);
    }

    protected function resolveLineItems(Order $order, array $quantities, array $productIds): array
    {
        $orderDetails = OrderDetails::where('order_code', $order->order_code)
            ->get()
            ->keyBy(fn ($detail) => (int) $detail->product_id);

        if ($orderDetails->isEmpty()) {
            return $this->result(false, 404, 'Không tìm thấy chi tiết đơn hàng cần cập nhật.');
        }

        $lineItems = [];

        foreach ($productIds as $index => $productId) {
            $productId = (int) $productId;
            $qty = (int) $quantities[$index];
            $detail = $orderDetails->get($productId);

            if (! $detail) {
                return $this->result(false, 404, 'Không tìm thấy dòng sản phẩm cần cập nhật.');
            }

            $product = Product::find($productId);
            if (! $product) {
                return $this->result(false, 404, 'Không tìm thấy sản phẩm của đơn hàng.');
            }

            $lineItems[] = [
                'detail' => $detail,
                'product' => $product,
                'qty' => $qty,
            ];
        }

        return $this->result(true, 200, null, $lineItems);
    }

    protected function resolveCouponMeta(string $orderCode): array
    {
        $couponCondition = 2;
        $couponNumber = 0;
        $couponCode = OrderDetails::where('order_code', $orderCode)->value('product_coupon');

        if (
            $couponCode &&
            $couponCode !== 'no' &&
            Schema::hasTable((new Coupon())->getTable())
        ) {
            $coupon = Coupon::where('coupon_code', $couponCode)->first();
            if ($coupon) {
                $couponCondition = (int) $coupon->coupon_condition;
                $couponNumber = (float) $coupon->coupon_number;
            }
        }

        return [
            'coupon_condition' => $couponCondition,
            'coupon_number' => $couponNumber,
        ];
    }

    protected function recordStatistic(?string $orderDate, float $sales, float $profit, int $quantity): void
    {
        if (! $orderDate || ! Schema::hasTable((new Statistic())->getTable())) {
            return;
        }

        $statistic = Statistic::where('order_date', $orderDate)->first();

        if ($statistic) {
            $statistic->sales = $statistic->sales + $sales;
            $statistic->profit = $statistic->profit + $profit;
            $statistic->quantity = $statistic->quantity + $quantity;
            $statistic->total_order = $statistic->total_order + 1;
            $statistic->save();

            return;
        }

        $statistic = new Statistic();
        $statistic->order_date = $orderDate;
        $statistic->sales = $sales;
        $statistic->profit = $profit;
        $statistic->quantity = $quantity;
        $statistic->total_order = 1;
        $statistic->save();
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
