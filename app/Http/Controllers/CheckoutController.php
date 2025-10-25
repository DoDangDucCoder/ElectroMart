<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\BrandService;
use App\Application\Catalog\Services\CategoryService;
use App\Application\Checkout\Services\CartService;
use App\Application\Checkout\Services\CheckoutService;
use App\Application\Checkout\Services\CouponService;
use App\Application\Checkout\Services\ShippingService;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Geo\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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
        return view('pages.checkout.login_checkout', $this->seoContext($request) + $this->catalogContext());
    }

    public function add_customer(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_password' => ['required', 'string', 'min:6'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
        ]);

        if (Customer::where('email', $data['customer_email'])->exists()) {
            return Redirect::back()->with('error', 'Email already exists.');
        }

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

    public function login_customer(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email_account' => ['required', 'email'],
            'password_account' => ['required', 'string'],
        ]);

        $customer = Customer::where('email', $credentials['email_account'])->first();

        if (! $customer || ! password_verify($credentials['password_account'], $customer->password)) {
            return Redirect::back()->with('error', 'Invalid login credentials.');
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

        return view('pages.checkout.show_checkout', array_merge(
            $this->seoContext($request, 'Checkout', 'checkout'),
            $this->catalogContext(),
            $this->cartContext(),
            [
                'cities' => Location::where('type', 'city')->orderBy('name')->get(),
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

    public function calculate_fee(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'city_id' => ['nullable', 'integer'],
            'province_id' => ['nullable', 'integer'],
            'ward_id' => ['nullable', 'integer'],
            'matp' => ['nullable'],
            'maqh' => ['nullable'],
            'xaid' => ['nullable'],
        ]);

        $cityId = $validated['city_id'] ?? ($validated['matp'] ?? null);
        $provinceId = $validated['province_id'] ?? ($validated['maqh'] ?? null);
        $wardId = $validated['ward_id'] ?? ($validated['xaid'] ?? null);

        $fee = $this->shippingService->calculateFee(
            $cityId ? (int) $cityId : null,
            $provinceId ? (int) $provinceId : null,
            $wardId ? (int) $wardId : null
        );

        $this->shippingService->storeFee($fee);

        return response()->json(['fee' => $fee]);
    }

    public function select_delivery_home(Request $request)
    {
        $action = $request->input('action');
        $parentId = (int) $request->input('ma_id');

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

    public function confirm_order(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shipping_email' => ['required', 'email'],
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_phone' => ['required', 'string', 'max:30'],
            'shipping_notes' => ['nullable', 'string', 'max:500'],
            'shipping_method' => ['nullable', 'string', 'max:50'],
            'city_id' => ['nullable', 'integer'],
            'province_id' => ['nullable', 'integer'],
            'ward_id' => ['nullable', 'integer'],
        ]);

        if (! Session::get('customer_id')) {
            return response()->json(['message' => 'Please sign in before placing an order.'], 401);
        }

        try {
            $order = $this->checkoutService->confirmOrder(array_merge($validated, [
                'order_note' => $request->input('order_note'),
            ]), Session::get('customer_id'));
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
            'category_post' => \App\Models\CatePost::orderBy('cate_post_id', 'DESC')->get(),
            'slider' => \App\Models\Slider::orderBy('slider_id', 'DESC')->where('slider_status', '1')->take(4)->get(),
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
}
