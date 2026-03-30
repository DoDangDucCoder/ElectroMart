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
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Geo\Location;
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
        $currentCustomer = $this->sessionCustomer();

        return view('pages.checkout.login_checkout', array_merge(
            $this->seoContext($request),
            $this->catalogContext(),
            $this->cartContext(),
            [
                'currentCustomer' => $currentCustomer,
            ]
        ));
    }

    public function add_customer(RegisterCustomerRequest $request): RedirectResponse
    {
        $data = $request->validated();

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

        Session::put('customer_id', $customer->id);

        return Redirect::to('/checkout')->with('message', 'Registration successful.');
    }

    public function login_customer(LoginCustomerRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        $customer = Customer::where('email', $credentials['email_account'])->first();

        if (! $customer || ! password_verify($credentials['password_account'], $customer->password)) {
            return Redirect::back()
                ->withInput($request->safe()->only('email_account'))
                ->with('error', 'Invalid login credentials.');
        }

        Session::put('customer_id', $customer->id);

        return Redirect::to('/checkout')->with('message', 'Logged in successfully.');
    }

    public function dang_xuat_khachhang(): RedirectResponse
    {
        Session::forget('customer_id');

        return Redirect::back()->with('message', 'Logged out.');
    }

    public function checkout(Request $request)
    {
        if ($this->cartService->items()->isEmpty()) {
            return Redirect::to('/gio-hang')->with('error', 'Your cart is empty.');
        }

        $currentCustomer = $this->sessionCustomer();

        return view('pages.checkout.show_checkout', array_merge(
            $this->seoContext($request, 'Checkout', 'checkout'),
            $this->catalogContext(),
            $this->cartContext(),
            [
                'cities' => Location::where('type', 'city')->orderBy('name')->get(),
                'currentCustomer' => $currentCustomer,
            ]
        ));
    }

    public function payment(Request $request)
    {
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

        $options = '<option value="">---Select---</option>';
        $locations = collect();

        if ($action === 'city') {
            $options = '<option value="">---Select district---</option>';
            $locations = Location::where('type', 'province')
                ->where('parent_id', $parentId)
                ->orderBy('name')
                ->get();
        } elseif ($action === 'province') {
            $options = '<option value="">---Select ward---</option>';
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

        return Redirect::back()->with('message', 'Shipping fee cleared.');
    }

    public function confirm_order(ConfirmOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $customer = $this->sessionCustomer();
        if (! $customer) {
            return response()->json(['message' => 'Please sign in before placing an order.'], 401);
        }

        try {
            $order = $this->checkoutService->confirmOrder(array_merge($validated, [
                'payment_method' => $validated['shipping_method'] ?? null,
            ]), $customer->id);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Order confirmed successfully.',
            'order_code' => $order->code,
        ]);
    }

    public function logout_checkout(): RedirectResponse
    {
        return $this->dang_xuat_khachhang();
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
        $coupon = $this->couponService->current();
        $shippingFee = $this->shippingService->getFee();

        return [
            'cartItems' => $this->cartService->items(),
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
}
