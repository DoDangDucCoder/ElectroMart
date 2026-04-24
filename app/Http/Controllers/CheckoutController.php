<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\BrandService;
use App\Application\Catalog\Services\CategoryService;
use App\Application\Checkout\Services\CartService;
use App\Application\Checkout\Services\CheckoutService;
use App\Application\Checkout\Services\CouponService;
use App\Application\Checkout\Services\ShippingService;
use App\Http\Requests\Storefront\Checkout\CalculateFeeRequest;
use App\Http\Requests\Storefront\Checkout\ConfirmOrderRequest;
use App\Http\Requests\Storefront\Checkout\LoginCustomerRequest;
use App\Http\Requests\Storefront\Checkout\RegisterCustomerRequest;
use App\Http\Requests\Storefront\Checkout\SelectDeliveryHomeRequest;
use App\Models\CatePost;
use App\Models\Slider;
use App\Support\StorefrontCustomerAuth;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Geo\Location;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CouponService $couponService,
        private readonly ShippingService $shippingService,
        private readonly CheckoutService $checkoutService,
        private readonly CategoryService $categoryService,
        private readonly BrandService $brandService,
    ) {
    }

    public function login_checkout(Request $request)
    {
        return view('pages.checkout.login_checkout', $this->storefrontAuthPageContext($request, 'login'));
    }

    public function register_checkout(Request $request)
    {
        return view('pages.checkout.register_checkout', $this->storefrontAuthPageContext($request, 'register'));
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
            ! password_verify($credentials['password_account'], $customer->password)
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
        $redirectKey = StorefrontCustomerAuth::normalizeRedirectKey($request->query('redirect'), 'login');

        $this->clearCustomerSession();

        if (in_array($redirectKey, ['checkout', 'payment', 'history'], true)) {
            StorefrontCustomerAuth::rememberIntendedRedirect($redirectKey, $redirectKey);

            return Redirect::to(
                StorefrontCustomerAuth::destinationUrl('login') . '?redirect=' . $redirectKey
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
            StorefrontCustomerAuth::rememberIntendedRedirect('checkout', 'checkout');

            return Redirect::to(StorefrontCustomerAuth::destinationUrl('login') . '?redirect=checkout')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục thanh toán.');
        }

        return view('pages.checkout.show_checkout', array_merge(
            $this->seoContext($request, 'Checkout', 'checkout'),
            $this->catalogContext(),
            $this->cartContext(),
            [
                'cities' => Location::where('type', 'city')->orderBy('name')->get(),
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
            StorefrontCustomerAuth::rememberIntendedRedirect('payment', 'payment');

            return Redirect::to(StorefrontCustomerAuth::destinationUrl('login') . '?redirect=payment')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục thanh toán.');
        }

        return view('pages.checkout.payment', array_merge(
            $this->seoContext($request, 'Checkout', 'payment'),
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
            $options .= '<option value="' . $location->id . '">' . e($location->name) . '</option>';
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
            return response()->json(['message' => 'Vui lòng đăng nhập trước khi đặt hàng.'], 401);
        }

        try {
            $order = $this->checkoutService->confirmOrder(array_merge($validated, [
                'payment_method' => $validated['shipping_method'] ?? null,
            ]), $customer->id);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
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
        return Redirect::back()->with('error', 'MoMo payment gateway is currently disabled.');
    }

    public function vnpay_payment(Request $request): RedirectResponse
    {
        return Redirect::back()->with('error', 'VNPAY payment gateway is currently disabled.');
    }

    protected function seoContext(Request $request, string $title = 'Checkout', string $keyword = 'checkout'): array
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
                'authLoggedInDescription' => 'Tài khoản của bạn đã sẵn sàng. Bạn có thể tiếp tục tới ' . $this->redirectLabel($authRedirectKey) . ' ngay bây giờ.',
                'authCustomerDisplayName' => $authCustomerDisplayName,
                'authCustomerEmail' => $currentCustomer?->email,
                'authCustomerPhone' => $currentCustomer?->phone,
                'storefrontCustomerPicture' => Session::get(StorefrontCustomerAuth::SESSION_PICTURE_KEY),
            ]
        );
    }

    protected function storefrontAuthLink(string $path, string $redirectKey, string $redirectUrl): string
    {
        return url($path) . '?' . http_build_query([
            'redirect' => $redirectKey,
            'redirect_to' => $redirectUrl,
        ]);
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

        return Slider::orderBy('slider_id', 'DESC')->where('slider_status', '1')->take(4)->get();
    }

    protected function isDuplicateCustomerKey(QueryException $exception, string $keyName): bool
    {
        $message = strtolower($exception->getMessage());

        return str_contains($message, 'duplicate entry') && str_contains($message, strtolower($keyName));
    }
}
