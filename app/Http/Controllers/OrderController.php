<?php

namespace App\Http\Controllers;

use App\Application\Order\Services\LegacyOrderWorkflowService;
use App\Application\Order\Services\LegacyOrderPresentationService;
use App\Application\Order\Services\LegacyOrderHistoryService;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\CatePost;
use App\Models\Slider;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand as StorefrontBrand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory as StorefrontCategory;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer as StorefrontCustomer;
use App\Infrastructure\Persistence\Eloquent\Order\Order as StorefrontOrder;
use Carbon\Carbon;
use Session;
use DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderController extends Controller
{
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
			return response()->json(['message' => 'Phiên đăng nhập không còn hợp lệ.'], 401);
		}

		if ($modernCustomer) {
			$order = StorefrontOrder::where('customer_id', $modernCustomer->id)
				->where('code', $data['order_code'])
				->first();

			if (! $order) {
				return response()->json(['message' => 'Không tìm thấy đơn hàng cần hủy.'], 404);
			}

			if (! $this->canCancelModernOrder($order)) {
				return response()->json(['message' => 'Đơn hàng này không thể hủy ở trạng thái hiện tại.'], 422);
			}

			$meta = $order->meta ?? [];
			$meta['cancel_reason'] = $data['lydo'];
			$meta['cancelled_from'] = 'storefront_history';

			$order->status = 'cancelled';
			$order->cancelled_at = now();
			$order->meta = $meta;
			$order->note = trim(implode("\n", array_filter([
				$order->note,
				'Khách hủy đơn: ' . $data['lydo'],
			])));
			$order->save();

			return response()->json([
				'message' => 'Hủy đơn hàng thành công.',
				'order_code' => $order->code,
			]);
		}

		$order = Order::where('order_code', $data['order_code'])
			->where('customer_id', $sessionCustomerId)
			->first();

		if (! $order) {
			return response()->json(['message' => 'Không tìm thấy đơn hàng cần hủy.'], 404);
		}

		if (! $this->canCancelLegacyOrder($order)) {
			return response()->json(['message' => 'Đơn hàng này không thể hủy ở trạng thái hiện tại.'], 422);
		}

		$order->order_destroy = $data['lydo'];
		$order->order_status = 3;
		$order->save();

		return response()->json([
			'message' => 'Hủy đơn hàng thành công.',
			'order_code' => $order->order_code,
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
			'order_qty' => ['required', 'integer', 'min:1'],
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
			'order_status' => ['required', 'integer', 'in:1,2,3'],
			'quantity' => ['required', 'array', 'min:1'],
			'quantity.*' => ['required', 'integer', 'min:1'],
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
			return redirect('/manage-order')->with('message', $result['message']);
		}

		return view('admin.view_order')->with($result['data']);
	}
	public function manage_order()
	{
		if (! Schema::hasTable((new Order())->getTable())) {
			$getorder = new LengthAwarePaginator([], 0, 5, 1, [
				'path' => request()->url(),
			]);

			return view('admin.manage_order')->with(compact('getorder'));
		}

		$getorder = Order::orderby('order_id', 'DESC')->paginate(5);
		return view('admin.manage_order')->with(compact('getorder'));
	}
	public function history(Request $request)
	{
		if (!Session::get('customer_id')) {
			return redirect('dang-nhap')->with('error', 'Vui lòng đăng nhập để xem lịch sử mua hàng');
		}

		$modernCustomer = $this->resolveStorefrontCustomer();
		if ($this->expectsModernStorefrontCustomerSession() && ! $modernCustomer) {
			return redirect('dang-nhap')->with('error', 'Phiên đăng nhập không còn hợp lệ.');
		}
		$orders = $modernCustomer
			? $this->modernHistoryOrders($modernCustomer->id)
			: $this->legacyHistoryOrders((int) Session::get('customer_id'));

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
		if (!Session::get('customer_id')) {
			return redirect('dang-nhap')->with('error', 'Vui lòng đăng nhập để xem lịch sử mua hàng');
		}

		$modernCustomer = $this->resolveStorefrontCustomer();
		if ($this->expectsModernStorefrontCustomerSession() && ! $modernCustomer) {
			return redirect('dang-nhap')->with('error', 'Phiên đăng nhập không còn hợp lệ.');
		}

		if ($modernCustomer) {
			$order = StorefrontOrder::with([
				'customer',
				'items.product',
				'shipping.city',
				'shipping.province',
				'shipping.ward',
				'payments',
			])
				->where('customer_id', $modernCustomer->id)
				->where('code', $order_code)
				->first();

			if (! $order) {
				return redirect('/history')->with('error', 'Không tìm thấy đơn hàng bạn cần xem.');
			}

			return view('pages.history.view_history_order', array_merge(
				$this->storefrontPageContext($request, 'Chi tiết đơn hàng'),
				[
					'orderView' => $this->buildModernOrderDetailView($order),
				]
			));
		}

		$order = Order::where('customer_id', Session::get('customer_id'))
			->where('order_code', $order_code)
			->first();

		if (! $order) {
			return redirect('/history')->with('error', 'Không tìm thấy đơn hàng bạn cần xem.');
		}

		return view('pages.history.view_history_order', array_merge(
			$this->storefrontPageContext($request, 'Chi tiết đơn hàng'),
			[
				'orderView' => $this->buildLegacyOrderDetailView($order),
			]
		));
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

	protected function legacyCategoryPosts()
	{
		if (! Schema::hasTable('tbl_category_post')) {
			return collect();
		}

		return CatePost::orderBy('cate_post_id', 'DESC')->get();
	}

	protected function legacyActiveSliders()
	{
		if (! Schema::hasTable('tbl_slider')) {
			return collect();
		}

		return Slider::orderBy('slider_id', 'DESC')
			->where('slider_status', '1')
			->take(4)
			->get();
	}

	protected function storefrontCategories()
	{
		if (Schema::hasTable('product_categories')) {
			return StorefrontCategory::where('is_active', true)
				->orderBy('parent_id')
				->orderBy('display_order')
				->get();
		}

		if (Schema::hasTable('tbl_category_product')) {
			return DB::table('tbl_category_product')
				->where('category_status', '0')
				->orderby('category_parent', 'desc')
				->orderby('category_order', 'ASC')
				->get();
		}

		return collect();
	}

	protected function storefrontBrands()
	{
		if (Schema::hasTable('brands')) {
			return StorefrontBrand::where('is_active', true)
				->orderByDesc('id')
				->get();
		}

		if (Schema::hasTable('tbl_brand')) {
			return DB::table('tbl_brand')
				->where('brand_status', '0')
				->orderby('brand_id', 'desc')
				->get();
		}

		return collect();
	}

	protected function resolveStorefrontCustomer(): ?StorefrontCustomer
	{
		$customerId = (int) Session::get('customer_id');

		if ($customerId <= 0 || ! $this->supportsModernStorefrontOrders()) {
			return null;
		}

		if (Session::has('customer_name') || Session::has('customer_picture')) {
			return null;
		}

		return StorefrontCustomer::find($customerId);
	}

	protected function expectsModernStorefrontCustomerSession(): bool
	{
		$customerId = (int) Session::get('customer_id');

		return $customerId > 0
			&& $this->supportsModernStorefrontOrders()
			&& ! Session::has('customer_name')
			&& ! Session::has('customer_picture');
	}

	protected function supportsModernStorefrontOrders(): bool
	{
		return Schema::hasTable('customers')
			&& Schema::hasTable('orders')
			&& Schema::hasTable('order_items')
			&& Schema::hasTable('order_shipping')
			&& Schema::hasTable('payments');
	}

	protected function modernHistoryOrders(int $customerId)
	{
		$orders = StorefrontOrder::with(['items', 'shipping', 'payments'])
			->where('customer_id', $customerId)
			->orderByDesc('id')
			->paginate(10);

		$orders->setCollection(
			$orders->getCollection()->map(fn(StorefrontOrder $order) => $this->mapModernHistoryOrder($order))
		);

		return $orders;
	}

	protected function legacyHistoryOrders(int $customerId)
	{
		return app(LegacyOrderHistoryService::class)->paginateCustomerOrders($customerId);
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

	protected function buildLegacyOrderDetailView(Order $order): array
	{
		return app(LegacyOrderHistoryService::class)->buildDetailView($order);
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
				? ucfirst(str_replace('_', ' ', $key)) . ': ' . $value
				: $value;
		}

		return implode(' | ', array_filter($parts)) ?: 'Mặc định';
	}

	protected function canCancelModernOrder(StorefrontOrder $order): bool
	{
		return ! in_array($order->status, ['cancelled', 'completed', 'refunded'], true);
	}

	protected function canCancelLegacyOrder(Order $order): bool
	{
		return app(LegacyOrderHistoryService::class)->canCancel($order);
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
			'pending' => 'Đơn hàng mới',
			'confirmed' => 'Đã xác nhận',
			'processing' => 'Đang xử lý',
			'shipped' => 'Đang giao hàng',
			'completed' => 'Hoàn tất',
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
