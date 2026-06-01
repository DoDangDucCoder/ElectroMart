<?php

namespace App\Http\Controllers;

use App\Application\Order\Services\LegacyOrderPresentationService;
use App\Application\Order\Services\LegacyOrderWorkflowService;
use App\Application\Order\Services\OrderInventoryService;
use App\Http\Controllers\Concerns\LegacyStorefrontContext;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand as StorefrontBrand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory as StorefrontCategory;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer as StorefrontCustomer;
use App\Infrastructure\Persistence\Eloquent\Order\Order as StorefrontOrder;
use App\Support\StorefrontCustomerAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    use LegacyStorefrontContext;

    // public function __construct()
    // {
    // 	$this->middleware('permission:delete order', ['only' => ['order_code']]);
    // }
    public function huy_don_hang(Request $request)
    {
        $data = $request->validate([
            'order_code' => ['required', 'string'],
            'lydo' => ['required', 'string', 'max:1000'],
        ]);

        $sessionCustomerId = (int) Session::get('customer_id');
        if ($sessionCustomerId <= 0) {
            return response()->json(['message' => 'Vui lòng đăng nhập để hủy đơn hàng.'], 401);
        }

        $modernCustomer = $this->resolveStorefrontCustomer();
        if ($this->expectsModernStorefrontCustomerSession() && ! $modernCustomer) {
            $this->clearCustomerSession();

            return response()->json(['message' => 'Phiên đăng nhập không còn hợp lệ.'], 401);
        }

        if (! $modernCustomer) {
            return response()->json(['message' => 'Phiên đăng nhập không còn hợp lệ.'], 401);
        }

        $order = StorefrontOrder::where('customer_id', $modernCustomer->id)
            ->where('code', $data['order_code'])
            ->first();

        if (! $order) {
            return response()->json(['message' => 'Không tìm thấy đơn hàng cần hủy.'], 404);
        }

        if (! $this->canCancelModernOrder($order)) {
            return response()->json(['message' => 'Đơn hàng này không thể hủy ở trạng thái hiện tại.'], 422);
        }

        DB::transaction(function () use ($order, $data): void {
            app(OrderInventoryService::class)->release($order, 'storefront_customer_cancelled');
            $order->refresh();

            $meta = $order->meta ?? [];
            $meta['cancel_reason'] = $data['lydo'];
            $meta['cancelled_from'] = 'storefront_history';

            $order->status = 'cancelled';
            $order->cancelled_at = now();
            $order->meta = $meta;
            $order->note = trim(implode("\n", array_filter([
                $order->note,
                'Khách hủy đơn: '.$data['lydo'],
            ])));
            $order->save();
        });

        return response()->json([
            'message' => 'Hủy đơn hàng thành công.',
            'order_code' => $order->code,
        ]);
    }

    public function order_code(Request $request, $order_code)
    {
        $result = app(LegacyOrderWorkflowService::class)->deleteOrderByCode($order_code);

        return $this->orderActionResponse($request, $result['message'], $result['status']);
    }

    public function update_qty(Request $request)
    {
        $data = $request->validate([
            'order_product_id' => ['required', 'integer', 'min:1'],
            'order_code' => ['required', 'string'],
            'order_qty' => ['required', 'integer', 'min:0'],
        ]);

        $result = app(LegacyOrderWorkflowService::class)->updateOrderLineQuantity(
            $data['order_code'],
            (int) $data['order_product_id'],
            (int) $data['order_qty']
        );

        return response()->json(['message' => $result['message']], $result['status']);
    }

    public function update_order_qty(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'integer', 'min:1'],
            'order_status' => ['required', 'integer', 'in:1,2,3,4,5'],
            'quantity' => ['required', 'array', 'min:1'],
            'quantity.*' => ['required', 'integer', 'min:0'],
            'order_product_id' => ['required', 'array', 'min:1'],
            'order_product_id.*' => ['required', 'integer', 'min:1'],
        ]);

        $result = app(LegacyOrderWorkflowService::class)->updateOrderStatus(
            (int) $data['order_id'],
            (int) $data['order_status'],
            $data['quantity'],
            $data['order_product_id']
        );

        return response()->json(['message' => $result['message']], $result['status']);
    }

    public function print_order($checkout_code)
    {
        $html = app(LegacyOrderPresentationService::class)->renderPrintableHtml($checkout_code);
        if (! $html) {
            abort(404);
        }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->stream();
    }

    public function view_order($order_code)
    {
        $result = app(LegacyOrderWorkflowService::class)->buildAdminOrderViewData($order_code);

        if (! $result['ok']) {
            return redirect('/admin/orders-management')->with('message', $result['message']);
        }

        return view('admin.view_order')->with($result['data']);
    }

    public function track_order($order_code)
    {
        $result = app(LegacyOrderWorkflowService::class)->buildAdminOrderViewData($order_code);

        if (! $result['ok']) {
            return redirect('/admin/orders-management')->with('message', $result['message']);
        }

        $data = $result['data'];
        $order = $data['getorder']->first();
        $details = $data['order_details'];
        $shipping = $data['shipping'];

        $tracking = $this->buildOrderTrackingTimeline($order, $details);

        return view('admin.order.track', array_merge($data, [
            'order' => $order,
            'tracking' => $tracking,
            'route_points' => $this->buildShippingRoutePoints($shipping),
        ]));
    }

    protected function buildOrderTrackingTimeline($order, $details): array
    {
        $createdAt = $order->created_at ? \Carbon\Carbon::parse($order->created_at) : now();
        $orderDate = $order->order_date ? \Carbon\Carbon::parse($order->order_date) : null;
        $status = (int) $order->order_status;

        // Distribute synthetic timestamps across 6 steps when partial.
        $estimateBetween = function (int $stepIndex, int $totalSteps) use ($createdAt, $orderDate) {
            $end = $orderDate?->copy() ?: $createdAt->copy()->addDays(2);
            $totalMinutes = max(60, $createdAt->diffInMinutes($end));
            $step = (int) round(($totalMinutes / max(1, $totalSteps - 1)) * ($stepIndex - 1));
            return $createdAt->copy()->addMinutes($step);
        };

        $stepLabels = [
            'Chờ xác nhận',
            'Đã xác nhận',
            'Đang chuẩn bị hàng',
            'Đã bàn giao cho đơn vị vận chuyển',
            'Đang giao hàng',
            'Dự kiến giao',
        ];

        // 1=pending, 2=processing, 3=shipping, 4=delivered, 5=cancelled
        $currentStep = match ($status) {
            1 => 1,
            2 => 3,
            3 => 5,
            4 => 6,
            5 => 0,
            default => 1,
        };

        $now = now();
        $steps = [];
        foreach ($stepLabels as $i => $label) {
            $idx = $i + 1;
            $isDone = $status !== 5 && $idx < $currentStep;
            $isActive = $status !== 5 && $idx === $currentStep;
            $isFuture = $idx > $currentStep;

            if ($idx === 1) {
                $when = $createdAt;
            } elseif ($idx === 6 && $status === 4) {
                $when = $orderDate ?: $createdAt->copy()->addDays(2);
            } elseif ($idx === 6) {
                $when = $createdAt->copy()->addDays(2);
            } else {
                $when = $estimateBetween($idx, 6);
            }

            $steps[] = [
                'index' => $idx,
                'label' => $label,
                'datetime' => $when,
                'datetime_label' => $when->format($idx === 6 && $isFuture ? 'd/m/Y' : 'd/m/Y H:i'),
                'is_done' => $isDone,
                'is_active' => $isActive,
                'is_future' => $isFuture,
            ];
        }

        return [
            'status_code' => $status,
            'status_label' => match ($status) {
                1 => 'Chờ xử lý',
                2 => 'Đang xử lý',
                3 => 'Đang giao hàng',
                4 => 'Đã giao thành công',
                5 => 'Đã hủy',
                default => 'Đang xử lý',
            },
            'status_chip_tone' => match ($status) {
                4 => 'success',
                5 => 'danger',
                default => 'in-progress',
            },
            'current_step' => $currentStep,
            'steps' => $steps,
            'eta_label' => match ($status) {
                4 => 'Đã giao thành công',
                5 => 'Đã hủy',
                default => $orderDate?->translatedFormat('l, d/m/Y') ?: $createdAt->copy()->addDays(2)->translatedFormat('l, d/m/Y'),
            },
            'eta_window' => '08:00 - 17:00',
            'sub_steps' => [
                ['label' => 'Kho lấy hàng', 'sub' => 'Hà Nội', 'is_done' => $currentStep > 1],
                ['label' => 'Trung tâm trung chuyển', 'sub' => 'Hà Nội', 'is_done' => $currentStep > 3],
                ['label' => 'Trung tâm trung chuyển', 'sub' => 'Đà Nẵng', 'is_done' => $currentStep > 4],
                ['label' => 'Đang giao hàng', 'sub' => 'TP. Hồ Chí Minh', 'is_done' => $currentStep >= 5, 'is_active' => $currentStep === 5],
                ['label' => 'Giao thành công', 'sub' => 'TP. Hồ Chí Minh', 'is_done' => $currentStep >= 6 && $status === 4],
            ],
            'carrier' => [
                'name' => 'Giao Hàng Nhanh (GHN)',
                'short' => 'GHN',
                'tracking_number' => 'GHN' . str_pad((string) $order->order_id, 10, '0', STR_PAD_LEFT),
                'driver_name' => 'Nguyễn Văn A',
                'driver_phone' => '0901 234 567',
            ],
        ];
    }

    protected function buildShippingRoutePoints($shipping): array
    {
        // Default Vietnam transit waypoints. Front-end Leaflet draws a polyline through them.
        return [
            ['lat' => 21.0285, 'lng' => 105.8542, 'label' => 'Kho Hà Nội', 'kind' => 'origin'],
            ['lat' => 16.0544, 'lng' => 108.2022, 'label' => 'Trung chuyển Đà Nẵng', 'kind' => 'transit'],
            ['lat' => 10.8231, 'lng' => 106.6297, 'label' => 'TP. Hồ Chí Minh', 'kind' => 'current'],
            ['lat' => 10.7769, 'lng' => 106.7009, 'label' => 'Điểm giao', 'kind' => 'destination'],
        ];
    }

    public function manage_order()
    {
        $getorder = app(LegacyOrderWorkflowService::class)->paginateAdminOrders(5);

        return view('admin.manage_order')->with(compact('getorder'));
    }

    public function history(Request $request)
    {
        if (! Session::get('customer_id')) {
            return redirect('dang-nhap')->with('error', 'Vui lòng đăng nhập để xem lịch sử mua hàng');
        }

        $modernCustomer = $this->resolveStorefrontCustomer();
        if ($this->expectsModernStorefrontCustomerSession() && ! $modernCustomer) {
            $this->clearCustomerSession();

            return redirect('dang-nhap')->with('error', 'Phiên đăng nhập không còn hợp lệ.');
        }
        if (! $modernCustomer) {
            $this->clearCustomerSession();

            return redirect('dang-nhap')->with('error', 'Phiên đăng nhập không còn hợp lệ.');
        }

        $orders = $this->modernHistoryOrders($modernCustomer->id);

        return view('pages.history.history', array_merge(
            $this->storefrontPageContext($request, 'Lịch sử đơn hàng'),
            [
                'orders' => $orders,
                'historyStats' => $this->buildHistoryStats($orders),
            ]
        ));
    }

    public function view_history_order(Request $request, $order_code)
    {
        $modernCustomer = $this->resolveStorefrontCustomer();

        // Allow guest if just placed order matches code in session
        if (! Session::get('customer_id') && Session::get('guest_order_code') !== $order_code) {
            return redirect('dang-nhap')->with('error', 'Vui lòng đăng nhập để xem lịch sử mua hàng');
        }

        if ($this->expectsModernStorefrontCustomerSession() && ! $modernCustomer && Session::get('customer_id')) {
            $this->clearCustomerSession();

            return redirect('dang-nhap')->with('error', 'Phiên đăng nhập không còn hợp lệ.');
        }

        if ($modernCustomer || (! Session::get('customer_id') && Session::get('guest_order_code') === $order_code)) {
            $orderQuery = StorefrontOrder::with([
                'customer',
                'items.product',
                'shipping.city',
                'shipping.province',
                'shipping.ward',
                'payments',
            ]);

            if ($modernCustomer) {
                $orderQuery = $orderQuery->where('customer_id', $modernCustomer->id);
            } else {
                $orderQuery = $orderQuery->whereNull('customer_id');
            }

            $order = $orderQuery->where('code', $order_code)->first();

            if (! $order) {
                return redirect($modernCustomer ? '/account/orders' : '/')->with('error', 'Không tìm thấy đơn hàng bạn cần xem.');
            }

            return view('pages.history.view_history_order', array_merge(
                $this->storefrontPageContext($request, 'Chi tiết đơn hàng'),
                [
                    'orderView' => $this->buildModernOrderDetailView($order),
                ]
            ));
        }

        return redirect('/account/orders')->with('error', 'Không tìm thấy đơn hàng bạn cần xem.');
    }

    protected function storefrontPageContext(Request $request, string $title): array
    {
        return [
            'category_post' => $this->legacyCategoryPosts(),
            'slider' => $this->legacyActiveSliders(),
            'category' => $this->storefrontCategories(),
            'brand' => $this->storefrontBrands(),
            'meta_desc' => $title,
            'meta_keywords' => $title,
            'meta_title' => $title,
            'url_canonical' => $request->url(),
        ];
    }

    protected function storefrontCategories()
    {
        if (! Schema::hasTable('product_categories')) {
            return collect();
        }

        return StorefrontCategory::where('is_active', true)
            ->orderBy('parent_id')
            ->orderBy('display_order')
            ->get();
    }

    protected function storefrontBrands()
    {
        if (! Schema::hasTable('brands')) {
            return collect();
        }

        return StorefrontBrand::where('is_active', true)
            ->orderByDesc('id')
            ->get();
    }

    protected function resolveStorefrontCustomer(): ?StorefrontCustomer
    {
        $customerId = (int) Session::get('customer_id');

        if ($customerId <= 0 || ! $this->supportsModernStorefrontOrders()) {
            return null;
        }

        return StorefrontCustomer::find($customerId);
    }

    protected function expectsModernStorefrontCustomerSession(): bool
    {
        $customerId = (int) Session::get('customer_id');

        return $customerId > 0
            && $this->supportsModernStorefrontOrders();
    }

    protected function supportsModernStorefrontOrders(): bool
    {
        return Schema::hasTable('customers')
            && Schema::hasTable('orders')
            && Schema::hasTable('order_items')
            && Schema::hasTable('order_shipping')
            && Schema::hasTable('payments');
    }

    protected function clearCustomerSession(): void
    {
        Session::forget([
            'customer_id',
            'shipping_id',
            'customer_name',
            'customer_picture',
        ]);

        StorefrontCustomerAuth::clearDisplaySession();
    }

    protected function modernHistoryOrders(int $customerId)
    {
        $orders = StorefrontOrder::with(['items', 'shipping', 'payments'])
            ->where('customer_id', $customerId)
            ->orderByDesc('id')
            ->paginate(10);

        $orders->setCollection(
            $orders->getCollection()->map(fn (StorefrontOrder $order) => $this->mapModernHistoryOrder($order))
        );

        return $orders;
    }

    protected function buildHistoryStats($orders): array
    {
        $collection = collect($orders->items());
        $latestOrder = $collection->first();

        return [
            'total_orders' => $orders->total(),
            'active_orders' => $collection->where('can_cancel', true)->count(),
            'cancelled_orders' => $collection->where('status', 'cancelled')->count(),
            'latest_order_code' => $latestOrder['code'] ?? null,
        ];
    }

    protected function mapModernHistoryOrder(StorefrontOrder $order): array
    {
        $placedAt = $order->placed_at ?: $order->created_at;
        $payment = $order->payments->first();

        return [
            'code' => $order->code,
            'placed_at_label' => $placedAt ? $placedAt->format('d/m/Y H:i') : 'Chưa xác định',
            'status' => $order->status,
            'status_label' => $this->formatModernOrderStatusLabel($order->status),
            'payment_label' => $this->formatPaymentProviderLabel($payment?->provider),
            'payment_status_label' => $this->formatPaymentStatusLabel($payment?->status),
            'items_count' => (int) $order->items->sum('quantity'),
            'total' => (float) $order->total,
            'can_cancel' => $this->canCancelModernOrder($order),
            'cancel_reason' => data_get($order->meta, 'cancel_reason'),
        ];
    }

    protected function buildModernOrderDetailView(StorefrontOrder $order): array
    {
        $shipping = $order->shipping;
        $payment = $order->payments->first();
        $placedAt = $order->placed_at ?: $order->created_at;

        return [
            'code' => $order->code,
            'placed_at_label' => $placedAt ? $placedAt->format('d/m/Y H:i') : 'Chưa xác định',
            'status' => $order->status,
            'status_label' => $this->formatModernOrderStatusLabel($order->status),
            'can_cancel' => $this->canCancelModernOrder($order),
            'cancel_reason' => data_get($order->meta, 'cancel_reason'),
            'customer' => [
                'name' => $order->customer?->name ?? $shipping?->recipient_name ?? 'Khách hàng',
                'phone' => $order->customer?->phone ?? $shipping?->recipient_phone,
                'email' => $order->customer?->email ?? $shipping?->email,
            ],
            'shipping' => [
                'name' => $shipping?->recipient_name,
                'phone' => $shipping?->recipient_phone,
                'email' => $shipping?->email,
                'address' => $this->formatModernShippingAddress($shipping),
                'notes' => $shipping?->address_line2,
                'method_label' => $this->formatShippingMethodLabel($shipping?->shipping_method ?: data_get($order->meta, 'shipping_method')),
                'tracking_number' => $shipping?->tracking_number,
                'shipping_fee' => (float) $order->shipping_total,
            ],
            'payment' => [
                'provider_label' => $this->formatPaymentProviderLabel($payment?->provider),
                'status_label' => $this->formatPaymentStatusLabel($payment?->status),
                'amount' => (float) ($payment?->amount ?? $order->total),
                'reference' => $payment?->reference,
            ],
            'items' => $order->items->map(function ($item) {
                return [
                    'name' => $item->product_name,
                    'attributes' => $this->formatOrderItemAttributes($item->attributes),
                    'quantity' => (int) $item->quantity,
                    'price' => (float) $item->price,
                    'subtotal' => (float) $item->subtotal,
                    'image' => $item->product?->product_image ?: data_get($item->meta, 'thumbnail'),
                ];
            }),
            'totals' => [
                'subtotal' => (float) $order->subtotal,
                'discount' => (float) $order->discount_total,
                'shipping' => (float) $order->shipping_total,
                'total' => (float) $order->total,
            ],
            'coupon_code' => $order->coupon_code,
            'note' => $order->note,
            'source' => $order->source,
        ];
    }

    protected function calculateLegacyTotals(string $orderCode, $details = null): array
    {
        return app(LegacyOrderPresentationService::class)->calculateTotals($orderCode, $details);
    }

    protected function formatModernShippingAddress($shipping): string
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

        return implode(', ', $parts);
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

    protected function canCancelModernOrder(StorefrontOrder $order): bool
    {
        return ! in_array($order->status, ['cancelled', 'completed', 'refunded'], true);
    }

    protected function orderActionResponse(Request $request, string $message, int $status = 200)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => $message], $status);
        }

        if ($status >= 400) {
            return redirect()->back()->with('error', $message);
        }

        return redirect()->back()->with('message', $message);
    }

    protected function formatModernOrderStatusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang xử lý',
            'shipping', 'shipped' => 'Đang giao hàng',
            'completed' => 'Đã giao hàng',
            'cancelled' => 'Đã hủy',
            'refunded' => 'Đã hoàn tiền',
            default => 'Chưa xác định',
        };
    }

    protected function formatPaymentProviderLabel(?string $provider): string
    {
        return match ($provider) {
            'cod', 'cash' => 'Thanh toán khi nhận hàng',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'card' => 'Thanh toán thẻ',
            'paypal' => 'PayPal',
            'momo' => 'MoMo',
            'vnpay' => 'VNPay',
            null, '' => 'Chưa xác định',
            default => strtoupper($provider),
        };
    }

    protected function formatPaymentStatusLabel(?string $status): string
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

    protected function formatShippingMethodLabel(?string $method): string
    {
        return match ($method) {
            'cod' => 'Thanh toán khi nhận hàng',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'standard', null, '' => 'Giao hàng tiêu chuẩn',
            'express' => 'Giao hàng nhanh',
            default => ucwords(str_replace('_', ' ', $method)),
        };
    }
}
