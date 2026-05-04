<?php

namespace App\Http\Controllers;

use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductReview;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductView;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use App\Support\StorefrontCustomerAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Concerns\LegacyStorefrontContext;

class CustomerAccountController extends Controller
{
    use LegacyStorefrontContext;

    public function index(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();

            return $this->redirectToLogin('Vui lòng đăng nhập.');
        }

        return view('pages.account.profile', array_merge(
            $this->seoContext($request, 'Thông tin tài khoản', 'thong-tin-tai-khoan'),
            $this->catalogContext(),
            ['customer' => $customer]
        ));
    }

    public function orderManagement(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();
            return $this->redirectToLogin('Vui lòng đăng nhập.');
        }

        $orders = Order::query()
            ->where('customer_id', $customer->id)
            ->with(['items.product', 'shipping'])
            ->latest('id')
            ->paginate(10);

        $allOrders = Order::query()->where('customer_id', $customer->id);
        $stats = [
            'total'     => (int) (clone $allOrders)->count(),
            'pending'   => (int) (clone $allOrders)->whereIn('status', ['pending', 'confirmed'])->count(),
            'shipping'  => (int) (clone $allOrders)->whereIn('status', ['shipping', 'shipped'])->count(),
            'completed' => (int) (clone $allOrders)->where('status', 'completed')->count(),
            'cancelled' => (int) (clone $allOrders)->where('status', 'cancelled')->count(),
        ];

        return view('pages.account.order-management', array_merge(
            $this->seoContext($request, 'Đơn hàng của tôi', 'don-hang-cua-toi'),
            $this->catalogContext(),
            [
                'customer' => $customer,
                'orders'   => $orders,
                'stats'    => $stats,
            ]
        ));
    }

    public function purchaseHistory(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();
            return $this->redirectToLogin('Vui lòng đăng nhập.');
        }

        $orders = Order::query()
            ->where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->with(['items.product'])
            ->latest('completed_at')
            ->latest('id')
            ->paginate(10);

        $completedAll = Order::query()
            ->where('customer_id', $customer->id)
            ->where('status', 'completed');

        $stats = [
            'total_orders'   => (int) (clone $completedAll)->count(),
            'total_spent'    => (float) (clone $completedAll)->sum('total'),
            'this_month'     => (int) (clone $completedAll)->where('completed_at', '>=', Carbon::now()->startOfMonth())->count(),
            'loyalty_points' => (float) ($customer->loyalty_points_balance ?? 0),
        ];

        return view('pages.account.purchase-history', array_merge(
            $this->seoContext($request, 'Lịch sử mua hàng', 'lich-su-mua-hang'),
            $this->catalogContext(),
            [
                'customer' => $customer,
                'orders'   => $orders,
                'stats'    => $stats,
            ]
        ));
    }

    public function wishlist(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();
            return $this->redirectToLogin('Vui lòng đăng nhập.');
        }

        // Wishlist stored in session since no dedicated Wishlist model exists
        $wishlistIds = Session::get('wishlist_product_ids', []);
        $products = collect();

        if (! empty($wishlistIds)) {
            $products = Product::query()
                ->whereIn('id', $wishlistIds)
                ->where('is_active', true)
                ->with('brand')
                ->get();
        }

        $stats = [
            'total'      => $products->count(),
            'on_sale'    => $products->filter(fn ($p) => $p->sale_price && $p->sale_price < $p->price)->count(),
            'out_of_stock' => $products->filter(fn ($p) => ($p->stock_quantity ?? 0) <= 0)->count(),
        ];

        return view('pages.account.wishlist', array_merge(
            $this->seoContext($request, 'Sản phẩm yêu thích', 'san-pham-yeu-thich'),
            $this->catalogContext(),
            [
                'customer' => $customer,
                'products' => $products,
                'stats'    => $stats,
            ]
        ));
    }

    public function profile(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();
            return $this->redirectToLogin('Vui lòng đăng nhập.');
        }

        return view('pages.account.profile', array_merge(
            $this->seoContext($request, 'Thông tin tài khoản', 'thong-tin-tai-khoan'),
            $this->catalogContext(),
            ['customer' => $customer]
        ));
    }

    public function orderTracking(Request $request, string $orderCode)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();
            return $this->redirectToLogin('Vui lòng đăng nhập.');
        }

        $order = Order::query()
            ->where('customer_id', $customer->id)
            ->where('code', $orderCode)
            ->with(['items.product', 'shipping.city', 'shipping.province', 'shipping.ward', 'address.city', 'address.province', 'address.ward'])
            ->first();

        if (! $order) {
            return Redirect::route('customer.account.orders')->with('error', 'Không tìm thấy đơn hàng.');
        }

        // Build timeline from order timestamps
        $steps = collect();
        $steps->push(['title' => 'Đặt hàng thành công', 'time' => $order->placed_at ?? $order->created_at, 'desc' => 'Đơn hàng đã được tạo thành công', 'done' => true]);

        if ($order->confirmed_at || in_array($order->status, ['confirmed', 'processing', 'shipping', 'shipped', 'completed'])) {
            $steps->push(['title' => 'Xác nhận đơn hàng', 'time' => $order->confirmed_at, 'desc' => 'Đơn hàng đã được xác nhận', 'done' => true]);
        }

        if ($order->shipped_at || in_array($order->status, ['shipping', 'shipped', 'completed'])) {
            $shippingLabel = $order->shipping?->shipping_method
                ? ucfirst(str_replace('_', ' ', $order->shipping->shipping_method))
                : 'Đơn vị vận chuyển';

            $steps->push(['title' => 'Đã giao cho vận chuyển', 'time' => $order->shipped_at, 'desc' => $order->shipping ? $shippingLabel . ' đã nhận hàng' : 'Đã bàn giao đơn vị vận chuyển', 'done' => true]);
        }

        $isDelivering = in_array($order->status, ['shipping', 'shipped']);
        if ($isDelivering) {
            $steps->push(['title' => 'Đang vận chuyển', 'time' => null, 'desc' => 'Đơn hàng đang trên đường giao đến bạn', 'done' => false, 'active' => true]);
        }

        if ($order->status === 'completed') {
            $steps->push(['title' => 'Đang vận chuyển', 'time' => null, 'desc' => 'Đã hoàn tất vận chuyển', 'done' => true]);
            $steps->push(['title' => 'Giao hàng thành công', 'time' => $order->completed_at, 'desc' => 'Đơn hàng đã được giao', 'done' => true]);
        } else {
            $steps->push(['title' => 'Giao hàng thành công', 'time' => null, 'desc' => '', 'done' => false]);
        }

        if ($order->status === 'cancelled') {
            $steps = collect();
            $steps->push(['title' => 'Đặt hàng thành công', 'time' => $order->placed_at ?? $order->created_at, 'desc' => '', 'done' => true]);
            $steps->push(['title' => 'Đơn hàng đã bị hủy', 'time' => $order->cancelled_at, 'desc' => 'Đơn hàng đã bị hủy', 'done' => false, 'active' => true]);
        }

        return view('pages.account.order-tracking', array_merge(
            $this->seoContext($request, 'Theo dõi đơn hàng #' . $orderCode, 'theo-doi-don-hang'),
            $this->catalogContext(),
            [
                'customer' => $customer,
                'order'    => $order,
                'steps'    => $steps->values()->all(),
            ]
        ));
    }

    public function addresses(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();

            return $this->redirectToLogin('Vui lòng đăng nhập.');
        }

        $addresses = $customer->addresses()
            ->with(['city', 'province', 'ward'])
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        return view('pages.account.addresses', array_merge(
            $this->seoContext($request, 'Địa chỉ giao hàng', 'dia-chi'),
            $this->catalogContext(),
            [
                'customer'       => $customer,
                'active_section' => 'addresses',
                'addresses'      => $addresses,
            ]
        ));
    }

    public function updateProfile(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();

            return $this->redirectToLogin('Phiên đăng nhập không còn hợp lệ.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $customer->update($data);
        Session::put('customer_name', $customer->fresh()?->name ?? $data['name']);

        return Redirect::back()->with('message', 'Cập nhật thông tin thành công.');
    }

    public function postReview(Request $request)
    {
        if (! $this->sessionCustomer()) {
            $this->clearCustomerSession();

            return $this->redirectToLogin('Phiên đăng nhập không còn hợp lệ.');
        }

        // Simple feedback logic for now
        return Redirect::back()->with('message', 'Cảm ơn bạn đã gửi đánh giá!');
    }

    public function changePassword(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();

            return $this->redirectToLogin('Phiên đăng nhập không còn hợp lệ.');
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
        ]);

        if (! \Illuminate\Support\Facades\Hash::check($request->current_password, $customer->password)) {
            return Redirect::back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $customer->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->new_password)
        ]);

        return Redirect::back()->with('message', 'Đổi mật khẩu thành công!');
    }

    public function invoices(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();
            return $this->redirectToLogin('Vui lòng đăng nhập để xem hóa đơn.');
        }

        if (! Schema::hasTable('vat_invoices')) {
            return view('pages.account.invoices', array_merge(
                $this->seoContext($request, 'Hóa đơn / xuất hóa đơn VAT', 'hoa-don-vat'),
                $this->catalogContext(),
                [
                    'customer'     => $customer,
                    'invoices'     => collect(),
                    'invoiceStats' => [
                        'total_requests' => 0,
                        'issued'         => 0,
                        'processing'     => 0,
                        'total_value'    => 0,
                    ],
                ]
            ));
        }

        $invoices = $customer->vatInvoices()
            ->with(['order.items.product'])
            ->latest()
            ->paginate(15);

        $invoiceStats = [
            'total_requests' => (int) $customer->vatInvoices()->count(),
            'issued'         => (int) $customer->vatInvoices()->where('status', 'issued')->count(),
            'processing'     => (int) $customer->vatInvoices()->where('status', 'processing')->count(),
            'total_value'    => (float) $customer->vatInvoices()->where('status', 'issued')->sum('value'),
        ];

        return view('pages.account.invoices', array_merge(
            $this->seoContext($request, 'Hóa đơn / xuất hóa đơn VAT', 'hoa-don-vat'),
            $this->catalogContext(),
            [
                'customer'     => $customer,
                'invoices'     => $invoices,
                'invoiceStats' => $invoiceStats,
            ]
        ));
    }

    public function changePasswordView(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();
            return $this->redirectToLogin('Vui lòng đăng nhập.');
        }

        return view('pages.account.change-password', array_merge(
            $this->seoContext($request, 'Đổi mật khẩu', 'doi-mat-khau'),
            $this->catalogContext(),
            ['customer' => $customer]
        ));
    }

    public function cancelledOrders(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();
            return $this->redirectToLogin('Vui lòng đăng nhập.');
        }

        $cancelledOrders = Order::query()
            ->where('customer_id', $customer->id)
            ->where('status', 'cancelled')
            ->with(['items.product'])
            ->latest('cancelled_at')
            ->latest('id')
            ->get();

        $stats = [
            'total'         => $cancelledOrders->count(),
            'refunded'      => $cancelledOrders->where('payment_status', 'refunded')->count(),
            'pending'       => $cancelledOrders->whereIn('payment_status', ['pending', 'paid'])->count(),
            'total_value'   => (float) $cancelledOrders->sum('total'),
        ];

        $recommendedProducts = Product::query()
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('pages.account.cancelled-orders', array_merge(
            $this->seoContext($request, 'Đơn hàng đã hủy', 'don-hang-da-huy'),
            $this->catalogContext(),
            [
                'customer'            => $customer,
                'cancelledOrders'     => $cancelledOrders,
                'stats'               => $stats,
                'recommendedProducts' => $recommendedProducts,
            ]
        ));
    }

    public function vouchers(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();
            return $this->redirectToLogin('Vui lòng đăng nhập.');
        }

        $now = Carbon::now();

        $availableVouchers = Coupon::query()
            ->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            })
            ->orderByDesc('id')
            ->get();

        $usedRedemptions = $customer->couponRedemptions()
            ->with(['coupon', 'order'])
            ->latest()
            ->get();
        $usedCouponIds = $usedRedemptions->pluck('coupon_id')->filter()->unique();

        $expiredVouchers = Coupon::query()
            ->where(function ($query) use ($now) {
                $query->where('is_active', false)
                    ->orWhere('expires_at', '<', $now);
            })
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $expiringSoon = $availableVouchers
            ->filter(fn ($coupon) => $coupon->expires_at && $coupon->expires_at->lte($now->copy()->addDays(7)))
            ->count();

        $stats = [
            'total'          => $availableVouchers->count() + $usedRedemptions->count() + $expiredVouchers->count(),
            'active'         => $availableVouchers->count(),
            'expiring_soon'  => $expiringSoon,
            'used'           => $usedRedemptions->count(),
        ];

        return view('pages.account.vouchers', array_merge(
            $this->seoContext($request, 'Kho voucher / mã giảm giá', 'voucher-giam-gia'),
            $this->catalogContext(),
            [
                'customer'          => $customer,
                'availableVouchers' => $availableVouchers,
                'usedRedemptions'   => $usedRedemptions,
                'usedCouponIds'     => $usedCouponIds,
                'expiredVouchers'   => $expiredVouchers,
                'stats'             => $stats,
            ]
        ));
    }

    public function reviews(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();
            return $this->redirectToLogin('Vui lòng đăng nhập.');
        }

        $reviews = ProductReview::query()
            ->where('customer_id', $customer->id)
            ->with(['product'])
            ->latest()
            ->paginate(10);

        $allMine = ProductReview::query()
            ->where('customer_id', $customer->id);

        $stats = [
            'total'     => (int) (clone $allMine)->count(),
            'approved'  => (int) (clone $allMine)->where('status', 'approved')->count(),
            'pending'   => (int) (clone $allMine)->where('status', 'pending')->count(),
            'avg'       => round((float) (clone $allMine)->avg('rating'), 1),
        ];

        $reviewedProductIds = ProductReview::query()
            ->where('customer_id', $customer->id)
            ->pluck('product_id')
            ->unique();

        $reviewableProducts = Product::query()
            ->whereIn('id', function ($query) use ($customer) {
                $query->select('order_items.product_id')
                    ->from('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->where('orders.customer_id', $customer->id)
                    ->whereIn('orders.status', ['completed', 'shipped']);
            })
            ->whereNotIn('id', $reviewedProductIds)
            ->where('is_active', true)
            ->limit(5)
            ->get();

        return view('pages.account.reviews', array_merge(
            $this->seoContext($request, 'Nhận xét / đánh giá của tôi', 'nhan-xet-danh-gia'),
            $this->catalogContext(),
            [
                'customer'           => $customer,
                'reviews'            => $reviews,
                'stats'              => $stats,
                'reviewableProducts' => $reviewableProducts,
            ]
        ));
    }

    public function resetPasswordView(Request $request)
    {
        return view('pages.account.reset-password', array_merge(
            $this->seoContext($request, 'Đặt lại mật khẩu mới', 'dat-lai-mat-khau'),
            $this->catalogContext(),
            ['customer' => null]
        ));
    }

    public function recentlyViewed(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();
            return $this->redirectToLogin('Vui lòng đăng nhập.');
        }

        $latestViews = ProductView::query()
            ->select('product_id', DB::raw('MAX(viewed_at) as last_viewed_at'))
            ->where('customer_id', $customer->id)
            ->groupBy('product_id')
            ->orderByDesc('last_viewed_at')
            ->paginate(20);

        $productIds = collect($latestViews->items())->pluck('product_id');
        $products = Product::query()
            ->whereIn('id', $productIds)
            ->with('brand')
            ->get()
            ->keyBy('id');

        $viewedProducts = collect($latestViews->items())
            ->map(function ($row) use ($products) {
                $product = $products->get($row->product_id);
                if (! $product) {
                    return null;
                }
                return [
                    'product'        => $product,
                    'last_viewed_at' => $row->last_viewed_at ? Carbon::parse($row->last_viewed_at) : null,
                ];
            })
            ->filter()
            ->values();

        $sevenDaysAgo = Carbon::now()->subDays(7);
        $stats = [
            'total'        => (int) ProductView::query()
                ->where('customer_id', $customer->id)
                ->distinct('product_id')
                ->count('product_id'),
            'last_7_days'  => (int) ProductView::query()
                ->where('customer_id', $customer->id)
                ->where('viewed_at', '>=', $sevenDaysAgo)
                ->distinct('product_id')
                ->count('product_id'),
            'on_sale'      => (int) Product::query()
                ->whereIn('id', $productIds)
                ->where(function ($q) {
                    $q->where(function ($q2) {
                        $q2->whereNotNull('sale_price')
                            ->whereColumn('sale_price', '<', 'price')
                            ->where('sale_price', '>', 0);
                    });
                })
                ->count(),
            'in_stock'     => (int) Product::query()
                ->whereIn('id', $productIds)
                ->where('is_active', true)
                ->count(),
        ];

        return view('pages.account.recently-viewed', array_merge(
            $this->seoContext($request, 'Sản phẩm đã xem', 'san-pham-da-xem'),
            $this->catalogContext(),
            [
                'customer'       => $customer,
                'viewedProducts' => $viewedProducts,
                'paginator'      => $latestViews,
                'stats'          => $stats,
            ]
        ));
    }

    public function paymentMethods(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();
            return $this->redirectToLogin('Vui lòng đăng nhập.');
        }

        if (! Schema::hasTable('customer_payment_methods')) {
            return view('pages.account.payment-methods', array_merge(
                $this->seoContext($request, 'Phương thức thanh toán của tôi', 'phuong-thuc-thanh-toan'),
                $this->catalogContext(),
                [
                    'customer' => $customer,
                    'methods'  => collect(),
                    'stats'    => [
                        'linked'  => 0,
                        'default' => 0,
                        'wallet'  => 0,
                        'card'    => 0,
                    ],
                ]
            ));
        }

        $methods = $customer->paymentMethods()
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        $stats = [
            'linked'  => $methods->count(),
            'default' => $methods->where('is_default', true)->count(),
            'wallet'  => $methods->where('type', 'wallet')->count(),
            'card'    => $methods->whereIn('type', ['card', 'bank_account'])->count(),
        ];

        return view('pages.account.payment-methods', array_merge(
            $this->seoContext($request, 'Phương thức thanh toán của tôi', 'phuong-thuc-thanh-toan'),
            $this->catalogContext(),
            [
                'customer' => $customer,
                'methods'  => $methods,
                'stats'    => $stats,
            ]
        ));
    }

    public function returns(Request $request)
    {
        $customer = $this->sessionCustomer();
        if (! $customer) {
            $this->clearCustomerSession();
            return $this->redirectToLogin('Vui lòng đăng nhập.');
        }

        if (! Schema::hasTable('order_returns')) {
            return view('pages.account.returns', array_merge(
                $this->seoContext($request, 'Trả hàng / hoàn tiền', 'tra-hang-hoan-tien'),
                $this->catalogContext(),
                [
                    'customer'            => $customer,
                    'returns'             => collect(),
                    'stats'               => [
                        'total'        => 0,
                        'approved'     => 0,
                        'pending'      => 0,
                        'refunded'     => 0,
                        'total_refund' => 0,
                    ],
                    'recommendedProducts' => collect(),
                ]
            ));
        }

        $returns = $customer->orderReturns()
            ->with(['order', 'orderItem.product'])
            ->latest()
            ->paginate(10);

        $allMine = $customer->orderReturns();
        $stats = [
            'total'        => (int) (clone $allMine)->count(),
            'approved'     => (int) (clone $allMine)->where('status', 'approved')->count(),
            'pending'      => (int) (clone $allMine)->where('status', 'pending')->count(),
            'refunded'     => (int) (clone $allMine)->where('status', 'refunded')->count(),
            'total_refund' => (float) (clone $allMine)->where('status', 'refunded')->sum('refund_amount'),
        ];

        $recommendedProducts = Product::query()
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('pages.account.returns', array_merge(
            $this->seoContext($request, 'Trả hàng / hoàn tiền', 'tra-hang-hoan-tien'),
            $this->catalogContext(),
            [
                'customer'            => $customer,
                'returns'             => $returns,
                'stats'               => $stats,
                'recommendedProducts' => $recommendedProducts,
            ]
        ));
    }

    public function otpVerification(Request $request)
    {
        return view('pages.account.otp-verification', array_merge(
            $this->seoContext($request, 'Xác thực OTP / mã xác nhận', 'xac-thuc-otp'),
            $this->catalogContext(),
            ['customer' => null]
        ));
    }

    protected function seoContext(Request $request, string $title, string $keyword): array
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
            'category' => ProductCategory::where('is_active', true)
                ->orderBy('display_order')
                ->orderByDesc('id')
                ->get(),
            'brand' => Brand::where('is_active', true)
                ->orderBy('display_order')
                ->orderByDesc('id')
                ->get(),
            'category_post' => $this->legacyCategoryPosts(),
            'slider' => $this->legacyActiveSliders(),
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

    protected function redirectToLogin(string $message): RedirectResponse
    {
        return Redirect::to(StorefrontCustomerAuth::destinationUrl('login') . '?' . http_build_query([
            'redirect_to' => url('/account'),
        ]))->with('error', $message);
    }

    protected function clearCustomerSession(): void
    {
        Session::forget([
            'customer_id',
            'customer_name',
            'customer_picture',
        ]);

        StorefrontCustomerAuth::clearDisplaySession();
    }
}
