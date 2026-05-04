<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\BrandService;
use App\Application\Catalog\Services\CategoryService;
use App\Application\Checkout\Services\CartService;
use App\Application\Checkout\Services\CheckoutService;
use App\Application\Checkout\Services\CouponService;
use App\Application\Checkout\Services\ShippingService;
use App\Http\Controllers\Concerns\LegacyStorefrontContext;
use App\Http\Requests\Storefront\Checkout\CalculateFeeRequest;
use App\Http\Requests\Storefront\Checkout\ConfirmOrderRequest;
use App\Http\Requests\Storefront\Checkout\LoginCustomerRequest;
use App\Http\Requests\Storefront\Checkout\RegisterCustomerRequest;
use App\Http\Requests\Storefront\Checkout\SelectDeliveryHomeRequest;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Geo\Location;
use App\Support\StorefrontCustomerAuth;
use App\Support\StorefrontCustomerPassword;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    use LegacyStorefrontContext;

    public function __construct(
        private readonly CartService $cartService,
        private readonly CouponService $couponService,
        private readonly ShippingService $shippingService,
        private readonly CheckoutService $checkoutService,
        private readonly CategoryService $categoryService,
        private readonly BrandService $brandService,
    ) {}

    public function login_checkout(Request $request)
    {
        return view('pages.auth.login', $this->storefrontAuthPageContext($request, 'login'));
    }

    public function register_checkout(Request $request)
    {
        return view('pages.auth.register', $this->storefrontAuthPageContext($request, 'register'));
    }

    public function add_customer(RegisterCustomerRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            /** @var Customer $customer */
            $customer = Customer::create([
                'name' => $data['customer_name'],
                'email' => $data['customer_email'],
                'password' => bcrypt($data['customer_password']),
                'phone' => $data['customer_phone'] ?? null,
                'is_active' => true,
                'meta' => [
                    'legacy_source' => 'form_checkout',
                ],
            ]);
        } catch (QueryException $exception) {
            $input = collect($request->except('customer_password'))->toArray();

            if ($this->isDuplicateCustomerKey($exception, 'customers_phone_unique')) {
                return Redirect::back()
                    ->withInput($input)
                    ->withErrors([
                        'customer_phone' => 'Số điện thoại này đã được sử dụng.',
                    ]);
            }

            if ($this->isDuplicateCustomerKey($exception, 'customers_email_unique')) {
                return Redirect::back()
                    ->withInput($input)
                    ->withErrors([
                        'customer_email' => 'Email này đã được sử dụng.',
                    ]);
            }

            throw $exception;
        }

        Session::put('customer_id', $customer->id);
        StorefrontCustomerAuth::clearDisplaySession();

        return Redirect::to($this->intendedCustomerRedirectUrl($request))
            ->with('message', 'Đăng ký tài khoản thành công.');
    }

    public function login_customer(LoginCustomerRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        $customer = Customer::where('email', $credentials['email_account'])->first();

        if (
            ! $customer ||
            ! $customer->is_active ||
            ! StorefrontCustomerPassword::verifyAndUpgrade($customer, $credentials['password_account'])
        ) {
            return Redirect::back()
                ->withInput($request->safe()->only('email_account'))
                ->with('error', 'Thông tin đăng nhập không chính xác.');
        }

        Session::put('customer_id', $customer->id);
        StorefrontCustomerAuth::clearDisplaySession();

        return Redirect::to($this->intendedCustomerRedirectUrl($request))
            ->with('message', 'Đăng nhập thành công.');
    }

    public function dang_xuat_khachhang(Request $request): RedirectResponse
    {
        $redirectKey = StorefrontCustomerAuth::normalizeRedirectKey($request->input('redirect', $request->query('redirect')), 'login');

        $this->clearCustomerSession();

        if (in_array($redirectKey, ['checkout', 'payment', 'history'], true)) {
            StorefrontCustomerAuth::rememberIntendedRedirect($redirectKey, $redirectKey);

            return Redirect::to(
                StorefrontCustomerAuth::destinationUrl('login').'?redirect='.$redirectKey
            )->with('message', 'Đăng xuất thành công.');
        }

        return Redirect::to(StorefrontCustomerAuth::destinationUrl($redirectKey, 'login'))
            ->with('message', 'Đăng xuất thành công.');
    }

    public function checkout(Request $request)
    {
        if ($this->cartService->items()->isEmpty()) {
            return Redirect::to('/gio-hang')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $currentCustomer = $this->sessionCustomer();
        if (! $currentCustomer) {
            $this->clearCustomerSession();
        }
        // Allow guests to proceed
        $cities = Location::where('type', 'city')->orderBy('name')->get();

        return view('pages.checkout.show_checkout', array_merge(
            $this->seoContext($request, 'Thanh toán', 'thanh toán'),
            $this->catalogContext(),
            $this->cartContext(),
            [
                'cities' => $cities,
                'currentCustomer' => $currentCustomer,
                'storefrontCustomerPicture' => Session::get(StorefrontCustomerAuth::SESSION_PICTURE_KEY),
            ]
        ));
    }

    public function payment(Request $request)
    {
        $currentCustomer = $this->sessionCustomer();
        if (! $currentCustomer) {
            $this->clearCustomerSession();
        }
        // Allow guests to proceed

        return view('pages.checkout.payment', array_merge(
            $this->seoContext($request, 'Phương thức thanh toán', 'thanh toán'),
            $this->catalogContext(),
            $this->cartContext()
        ));
    }

    public function calculate_fee(CalculateFeeRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $cityId = $validated['city_id'] ?? null;
        $provinceId = $validated['province_id'] ?? null;
        $wardId = $validated['ward_id'] ?? null;

        $fee = $this->shippingService->calculateFee(
            $cityId ? (int) $cityId : null,
            $provinceId ? (int) $provinceId : null,
            $wardId ? (int) $wardId : null
        );

        $this->shippingService->storeFee($fee);

        return response()->json(['fee' => $fee]);
    }

    public function select_delivery_home(SelectDeliveryHomeRequest $request)
    {
        $validated = $request->validated();
        $action = $validated['action'];
        $parentId = (int) $validated['ma_id'];

        $options = '<option value="">--- Chọn ---</option>';
        $locations = collect();

        if ($action === 'city') {
            $options = '<option value="">--- Chọn quận / huyện ---</option>';
            $locations = Location::where('type', 'province')
                ->where('parent_id', $parentId)
                ->orderBy('name')
                ->get();
        } elseif ($action === 'province') {
            $options = '<option value="">--- Chọn phường / xã ---</option>';
            $locations = Location::where('type', 'ward')
                ->where('parent_id', $parentId)
                ->orderBy('name')
                ->get();
        }

        foreach ($locations as $location) {
            $options .= '<option value="'.$location->id.'">'.e($location->name).'</option>';
        }

        return response($options);
    }

    public function del_fee(): RedirectResponse
    {
        $this->shippingService->clearFee();

        return Redirect::back()->with('message', 'Đã xóa phí vận chuyển tạm tính.');
    }

    public function confirm_order(ConfirmOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();
        }
        $customerId = $customer?->id;

        try {
            $order = $this->checkoutService->confirmOrder(array_merge($validated, [
                'payment_method' => $validated['payment_method'] ?? $validated['shipping_method'] ?? null,
            ]), $customerId);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        if (! $customer) {
            Session::put('guest_order_code', $order->code);
        }

        return response()->json([
            'message' => 'Xác nhận đơn hàng thành công.',
            'order_code' => $order->code,
        ]);
    }

    public function logout_checkout(Request $request): RedirectResponse
    {
        return $this->dang_xuat_khachhang($request);
    }

    public function momo_payment(Request $request): RedirectResponse
    {
        return Redirect::back()->with('error', 'Cổng thanh toán MoMo hiện đang tạm tắt.');
    }

    public function vnpay_payment(Request $request): RedirectResponse
    {
        return Redirect::back()->with('error', 'Cổng thanh toán VNPAY hiện đang tạm tắt.');
    }

    public function bankTransferInstructions(Request $request, string $orderCode)
    {
        $currentCustomer = $this->sessionCustomer();

        // Allow guests if they just placed this order
        if (! $currentCustomer && Session::get('guest_order_code') !== $orderCode) {
            $this->clearCustomerSession();
            StorefrontCustomerAuth::rememberIntendedRedirect('payment', 'payment');

            return Redirect::to(StorefrontCustomerAuth::destinationUrl('login').'?redirect=payment')
                ->with('error', 'Vui lòng đăng nhập để xem thông tin thanh toán.');
        }

        $order = \App\Infrastructure\Persistence\Eloquent\Order\Order::where('code', $orderCode)
            ->with(['items.product', 'shipping.city', 'shipping.province', 'shipping.ward', 'payments', 'customer'])
            ->first();

        if (! $order) {
            return Redirect::to('/history')->with('error', 'Không tìm thấy đơn hàng.');
        }

        if ($order->customer_id && (! $currentCustomer || $order->customer_id !== $currentCustomer->id)) {
            return Redirect::to('/history')->with('error', 'Bạn không có quyền xem đơn hàng này.');
        }

        if (! $order->customer_id && Session::get('guest_order_code') !== $orderCode) {
            return Redirect::to('/')->with('error', 'Bạn không có quyền xem đơn hàng này.');
        }

        $paymentMethod = $order->payments->first()?->provider ?? 'unknown';

        if ($paymentMethod !== 'bank_transfer') {
            return Redirect::to('/view-history-order/'.$orderCode)
                ->with('error', 'Đơn hàng này không sử dụng phương thức chuyển khoản ngân hàng.');
        }

        $bankInfo = config('services.bank_transfer', []);
        $transferContent = 'DH '.$order->code;

        $statusLabel = match ($order->status) {
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đang giao hàng',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'refunded' => 'Đã hoàn tiền',
            default => 'Không xác định',
        };

        $paymentStatusLabel = match ($order->payment_status) {
            'unpaid' => 'Chưa thanh toán',
            'pending' => 'Đang chờ xác nhận',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại',
            'refunded' => 'Đã hoàn tiền',
            default => 'Không xác định',
        };

        return view('pages.checkout.bank_transfer_instructions', array_merge(
            $this->seoContext($request, 'Hướng dẫn chuyển khoản', 'thanh toán chuyển khoản'),
            $this->catalogContext(),
            [
                'order' => $order,
                'bankInfo' => $bankInfo,
                'transferContent' => $transferContent,
                'statusLabel' => $statusLabel,
                'paymentStatusLabel' => $paymentStatusLabel,
                'currentCustomer' => $currentCustomer,
                'storefrontCustomerPicture' => Session::get(StorefrontCustomerAuth::SESSION_PICTURE_KEY),
            ]
        ));
    }

    public function orderSuccess(Request $request, string $orderCode)
    {
        $order = \App\Infrastructure\Persistence\Eloquent\Order\Order::where('code', $orderCode)
            ->with(['items.product', 'shipping.city', 'shipping.province', 'shipping.ward', 'payments', 'customer'])
            ->first();

        if (!$order) {
            return Redirect::to('/history')->with('error', 'Không tìm thấy đơn hàng.');
        }

        $orderView = $this->buildOrderViewData($order);

        $recommendedProducts = \App\Infrastructure\Persistence\Eloquent\Catalog\Product::query()
            ->where('is_active', true)->inRandomOrder()->limit(5)->get();

        return view('pages.checkout.order_success', array_merge(
            $this->seoContext($request, 'Đặt hàng thành công', 'đặt hàng thành công'),
            $this->catalogContext(),
            [
                'orderView' => $orderView,
                'recommendedProducts' => $recommendedProducts,
            ]
        ));
    }

    public function orderFailed(Request $request, string $orderCode)
    {
        $order = \App\Infrastructure\Persistence\Eloquent\Order\Order::where('code', $orderCode)
            ->with(['items.product', 'shipping.city', 'shipping.province', 'shipping.ward', 'payments', 'customer'])
            ->first();

        if (!$order) {
            return Redirect::to('/history')->with('error', 'Không tìm thấy đơn hàng.');
        }

        $orderView = $this->buildOrderViewData($order);
        $orderView['fail_reason'] = $request->query('reason', 'Giao dịch bị từ chối bởi ngân hàng');
        $orderView['status'] = 'failed';

        $recommendedProducts = \App\Infrastructure\Persistence\Eloquent\Catalog\Product::query()
            ->where('is_active', true)->inRandomOrder()->limit(5)->get();

        return view('pages.checkout.order_failed', array_merge(
            $this->seoContext($request, 'Đặt hàng thất bại', 'đặt hàng thất bại'),
            $this->catalogContext(),
            [
                'orderView' => $orderView,
                'recommendedProducts' => $recommendedProducts,
            ]
        ));
    }

    protected function buildOrderViewData($order): array
    {
        $items = $order->items->map(function ($item) {
            return [
                'name' => $item->product_name ?? $item->name ?? 'Sản phẩm',
                'image' => $item->product?->product_image ?? data_get($item->meta, 'thumbnail') ?? '',
                'quantity' => $item->quantity ?? $item->product_qty ?? 1,
                'price' => (float)($item->price ?? $item->product_price ?? 0),
                'subtotal' => (float)($item->subtotal ?? ($item->price ?? 0) * ($item->quantity ?? 1)),
                'attributes' => $this->formatOrderItemAttributes($item->product_attribute ?? $item->attributes ?? null),
            ];
        })->toArray();

        $shipping = $order->shipping;
        $payment = $order->payments->first();

        return [
            'code' => $order->code,
            'date' => ($order->placed_at ?? $order->created_at)?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i'),
            'status' => $order->status ?? 'pending',
            'items' => $items,
            'totals' => [
                'subtotal' => (float)($order->subtotal ?? $order->total ?? 0),
                'shipping' => (float)($order->shipping_total ?? 0),
                'discount' => (float)($order->discount_total ?? $order->coupon_discount ?? 0),
                'total' => (float)($order->total ?? 0),
            ],
            'shipping' => [
                'name' => $shipping?->recipient_name ?? $order->customer?->name ?? '',
                'phone' => $shipping?->recipient_phone ?? $order->customer?->phone ?? '',
                'address' => $this->formatShippingAddress($shipping),
            ],
            'payment' => [
                'provider_label' => $this->formatPaymentProviderLabel($payment?->provider),
            ],
            'coupon_code' => $order->coupon_code ?? null,
            'estimated_delivery' => now()->addDays(3)->format('d/m/Y') . ' - ' . now()->addDays(5)->format('d/m/Y'),
        ];
    }

    protected function formatShippingAddress($shipping): string
    {
        if (! $shipping) {
            return '';
        }

        return implode(', ', array_filter([
            $shipping->address_line1,
            $shipping->address_line2,
            $shipping->ward?->name,
            $shipping->province?->name,
            $shipping->city?->name,
            $shipping->country,
        ]));
    }

    protected function formatOrderItemAttributes($attributes): string
    {
        if (empty($attributes)) {
            return '';
        }

        if (is_string($attributes)) {
            return $attributes;
        }

        $parts = [];
        foreach ((array) $attributes as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }

            $parts[] = is_string($key)
                ? ucfirst(str_replace('_', ' ', $key)) . ': ' . $value
                : (string) $value;
        }

        return implode(' | ', array_filter($parts));
    }

    protected function formatPaymentProviderLabel(?string $provider): string
    {
        return match ($provider) {
            'cod', 'cash' => 'Thanh toán khi nhận hàng (COD)',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'card' => 'Thanh toán thẻ',
            'paypal' => 'PayPal',
            'momo' => 'MoMo',
            'vnpay' => 'VNPay',
            null, '' => 'Thanh toán khi nhận hàng (COD)',
            default => strtoupper($provider),
        };
    }

    protected function seoContext(Request $request, string $title = 'Thanh toán', string $keyword = 'thanh toán'): array
    {
        return [
            'meta_desc' => $title,
            'meta_keywords' => $keyword,
            'meta_title' => $title,
            'url_canonical' => $request->url(),
        ];
    }

    protected function catalogContext(): array
    {
        return [
            'category_post' => $this->legacyCategoryPosts(),
            'slider' => $this->legacyActiveSliders(),
            'category' => $this->categoryService->listAll(['is_active' => true]),
            'brand' => $this->brandService->listAll(['is_active' => true]),
        ];
    }

    protected function cartContext(): array
    {
        $items = $this->cartService->items();

        if ($items->isEmpty()) {
            $this->couponService->clear();
            $this->shippingService->clearFee();
        }

        $coupon = $items->isEmpty()
            ? null
            : $this->couponService->validatedCurrent(
                $this->cartService->subtotal(),
                (int) Session::get('customer_id') ?: null
            );
        $shippingFee = $items->isEmpty() ? 0.0 : $this->shippingService->getFee();

        return [
            'cartItems' => $items,
            'cartTotals' => $this->cartService->totals($coupon, $shippingFee),
            'coupon' => $coupon,
            'shipping_fee' => $shippingFee,
        ];
    }

    protected function sessionCustomer(): ?Customer
    {
        $customerId = (int) Session::get('customer_id');

        if ($customerId <= 0) {
            return null;
        }

        return Customer::query()
            ->whereKey($customerId)
            ->where('is_active', true)
            ->first();
    }

    protected function intendedCustomerRedirectUrl(Request $request, string $default = 'checkout'): string
    {
        $redirectKey = StorefrontCustomerAuth::fromRequest($request, $default);

        $redirectTo = StorefrontCustomerAuth::normalizeInternalUrl(
            $request->input('redirect_to'),
            $redirectKey
        );

        if ($request->filled('redirect_to')) {
            StorefrontCustomerAuth::forgetIntendedRedirect();

            return $redirectTo;
        }

        $sessionUrl = StorefrontCustomerAuth::pullIntendedUrl($redirectKey);
        $redirectKey = StorefrontCustomerAuth::pullIntendedRedirect($redirectKey);

        return $sessionUrl ?: StorefrontCustomerAuth::destinationUrl($redirectKey, $default);
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
        StorefrontCustomerAuth::forgetIntendedRedirect();
    }

    protected function redirectLabel(string $redirectKey): string
    {
        return match ($redirectKey) {
            'history' => 'đơn hàng của bạn',
            'payment' => 'bước thanh toán',
            default => 'bước checkout',
        };
    }

    protected function continueActionLabel(string $redirectKey): string
    {
        return match ($redirectKey) {
            'history' => 'Xem đơn hàng của tôi',
            'payment' => 'Tiếp tục tới thanh toán',
            default => 'Tiếp tục tới checkout',
        };
    }

    protected function storefrontAuthPageContext(Request $request, string $mode): array
    {
        $currentCustomer = $this->sessionCustomer();
        $authRedirect = StorefrontCustomerAuth::rememberIntendedDestination($request, 'checkout');
        $authRedirectKey = $authRedirect['key'];
        $authRedirectUrl = $authRedirect['url'];

        $authCustomerDisplayName = $currentCustomer?->name ?: ($currentCustomer?->email ?: 'Tài khoản của bạn');

        $authRoutes = [
            'login' => $this->storefrontAuthLink('/dang-nhap', $authRedirectKey, $authRedirectUrl),
            'register' => $this->storefrontAuthLink('/dang-ky', $authRedirectKey, $authRedirectUrl),
        ];

        return array_merge(
            $this->seoContext(
                $request,
                $mode === 'register' ? 'Đăng ký tài khoản' : 'Đăng nhập tài khoản',
                $mode === 'register' ? 'dang-ky' : 'dang-nhap'
            ),
            $this->catalogContext(),
            [
                'authPageMode' => $mode,
                'authPageHeading' => $mode === 'register' ? 'Tạo tài khoản mới' : 'Đăng nhập tài khoản',
                'authPageDescription' => $mode === 'register'
                    ? 'Điền thông tin cơ bản để tạo tài khoản và tiếp tục mua sắm.'
                    : 'Dùng tài khoản hiện có để tiếp tục đặt hàng và theo dõi lịch sử đơn mua.',
                'authLoginUrl' => $authRoutes['login'],
                'authRegisterUrl' => $authRoutes['register'],
                'currentCustomer' => $currentCustomer,
                'authRedirectKey' => $authRedirectKey,
                'authRedirectUrl' => $authRedirectUrl,
                'authRedirectLabel' => $this->redirectLabel($authRedirectKey),
                'authContinueLabel' => $this->continueActionLabel($authRedirectKey),
                'authLoggedInDescription' => 'Tài khoản của bạn đã sẵn sàng. Bạn có thể tiếp tục tới '.$this->redirectLabel($authRedirectKey).' ngay bây giờ.',
                'authCustomerDisplayName' => $authCustomerDisplayName,
                'authCustomerEmail' => $currentCustomer?->email,
                'authCustomerPhone' => $currentCustomer?->phone,
                'storefrontCustomerPicture' => Session::get(StorefrontCustomerAuth::SESSION_PICTURE_KEY),
            ]
        );
    }

    protected function storefrontAuthLink(string $path, string $redirectKey, string $redirectUrl): string
    {
        return url($path).'?'.http_build_query([
            'redirect' => $redirectKey,
            'redirect_to' => $redirectUrl,
        ]);
    }

    protected function isDuplicateCustomerKey(QueryException $exception, string $keyName): bool
    {
        $message = strtolower($exception->getMessage());

        return str_contains($message, 'duplicate entry') && str_contains($message, strtolower($keyName));
    }
}
