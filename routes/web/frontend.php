<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrandProduct;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryProduct;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

// Đổi ngôn ngữ giao diện (hiện chuẩn hóa về tiếng Việt)
Route::get('lang/{locale}', function ($locale) {
    if ($locale !== 'vi') {
        abort(404);
    }

    session()->put('locale', $locale);

    return redirect()->back();
});

// Home & general pages
Route::get('/', [HomeController::class, 'index']);
Route::get('/trang-chu', [HomeController::class, 'index']);
Route::get('/404', [HomeController::class, 'error_page']);
Route::post('/load-more-product', [HomeController::class, 'load_more_product']);
Route::get('/show_quick_cart', [CartController::class, 'show_quick_cart']);
Route::match(['get', 'post'], '/tim-kiem', [HomeController::class, 'search']);
Route::match(['get', 'post'], '/san-pham', [HomeController::class, 'products']);
Route::get('/san-pham-ban-chay', [HomeController::class, 'best_selling']);
Route::post('/autocomplete-ajax', [HomeController::class, 'autocomplete_ajax'])->middleware('throttle:60,1');
Route::get('/yeu-thich', fn () => redirect()->route('customer.account.wishlist', [], 301));
Route::get('/goi-y-san-pham', fn () => redirect()->to(url('/san-pham?mode=recommendations'), 301));
Route::get('/khuyen-mai', [HomeController::class, 'khuyenMai']);
Route::get('/kiem-tra-don-hang', [HomeController::class, 'orderTracking']);
Route::redirect('/order_tracking', '/kiem-tra-don-hang', 301);
Route::get('/chatbot/conversation', [ChatController::class, 'conversation']);
Route::post('/chatbot/messages', [ChatController::class, 'store']);

// Contact page
Route::get('/lien-he', [ContactController::class, 'lien_he']);
Route::post('/lien-he', [ContactController::class, 'submitContact'])->middleware('throttle:3,1');

// Customer support center
Route::view('/cham-soc-khach-hang', 'pages.support.index')->name('support.index');
Route::redirect('/support/index', '/cham-soc-khach-hang', 301);
$supportPages = [
    'faq' => ['/cau-hoi-thuong-gap', 'pages.support.faq', 'support.faq'],
    'bao-hanh' => ['/chinh-sach-bao-hanh', 'pages.support.warranty', 'support.warranty'],
    'doi-tra' => ['/chinh-sach-doi-tra', 'pages.support.doi-tra', 'support.doi-tra'],
    'bao-mat' => ['/chinh-sach-bao-mat', 'pages.support.bao-mat', 'support.bao-mat'],
    'thanh-toan' => ['/chinh-sach-thanh-toan', 'pages.support.thanh-toan', 'support.thanh-toan'],
    'van-chuyen' => ['/chinh-sach-van-chuyen', 'pages.support.van-chuyen', 'support.van-chuyen'],
    'dieu-khoan' => ['/dieu-khoan-su-dung', 'pages.support.dieu-khoan', 'support.dieu-khoan'],
    'huong-dan-mua-hang' => ['/huong-dan-mua-hang', 'pages.support.huong-dan-mua-hang', 'support.huong-dan-mua-hang'],
    'huong-dan-thanh-toan' => ['/huong-dan-thanh-toan', 'pages.support.huong-dan-thanh-toan', 'support.huong-dan-thanh-toan'],
    'gioi-thieu' => ['/gioi-thieu', 'pages.support.gioi-thieu', 'support.gioi-thieu'],
];
foreach ($supportPages as $slug => [$legacyPath, $view, $name]) {
    Route::get('/cham-soc-khach-hang/'.$slug, fn () => view($view, [
        'url_canonical' => url('/cham-soc-khach-hang/'.$slug),
    ]))->name($name);
    Route::redirect($legacyPath, '/cham-soc-khach-hang/'.$slug, 301);
}
Route::redirect('/about', '/cham-soc-khach-hang/gioi-thieu', 301);

// Catalog & product detail
Route::get('/danh-muc/{slug_category_product}', function (Request $request, string $slug_category_product) {
    return redirect()->to(url('/san-pham').'?'.http_build_query(array_merge($request->query(), [
        'category' => $slug_category_product,
    ])), 301);
});
Route::post('/product-tabs', [CategoryProduct::class, 'product_tabs']);
Route::get('/chi-tiet/{product_slug}', [ProductController::class, 'details_product']);
Route::get('/tag/{product_tag}', function (Request $request, string $product_tag) {
    return redirect()->to(url('/san-pham').'?'.http_build_query(array_merge($request->query(), [
        'tag' => $product_tag,
    ])), 301);
});
Route::post('/quickview', [ProductController::class, 'quickview']);
Route::post('/load-comment', [ProductController::class, 'load_comment']);
Route::post('/send-comment', [ProductController::class, 'send_comment'])->middleware('throttle:10,1');
Route::post('/insert-rating', [ProductController::class, 'insert_rating'])->middleware('throttle:10,1');
Route::get('/so-sanh', function (Request $request) {
    $firstId = collect(explode(',', (string) $request->query('ids')))
        ->map(fn ($id) => (int) $id)
        ->filter()
        ->first();

    if ($firstId) {
        $product = \App\Infrastructure\Persistence\Eloquent\Catalog\Product::query()
            ->whereKey($firstId)
            ->where('is_active', true)
            ->first();

        if ($product) {
            $slug = $product->product_slug ?? $product->slug;

            return redirect()->to(url('/chi-tiet/'.$slug).'?compare=1#so-sanh-nhanh', 301);
        }
    }

    return redirect()->to(url('/san-pham'), 301);
});

// Blog
Route::get('/bai-viet', [PostController::class, 'index'])->name('blog.index');
Route::get('/danh-muc-bai-viet/{post_slug}', function (Request $request, string $post_slug) {
    return redirect()->to(url('/bai-viet').'?'.http_build_query(array_merge($request->query(), [
        'category' => $post_slug,
    ])), 301);
});
Route::get('/bai-viet/{post_slug}', [PostController::class, 'bai_viet']);
Route::get('/thuong-hieu/{brandSlug}', function (Request $request, string $brandSlug) {
    return redirect()->to(url('/san-pham').'?'.http_build_query(array_merge($request->query(), [
        'brand' => $brandSlug,
    ])), 301);
});

// Coupon (customer side)
Route::post('/check-coupon', [CartController::class, 'check_coupon']);
Route::delete('/unset-coupon', [CouponController::class, 'unset_coupon']);
Route::get('/unset-coupon', fn () => Redirect::to('/gio-hang')
    ->with('error', 'Vui lòng dùng nút gỡ mã giảm giá trên trang giỏ hàng.'));

// Cart routes
Route::post('/update-cart-quantity', [CartController::class, 'update_cart_quantity']);
Route::post('/update-cart', [CartController::class, 'update_cart']);
Route::post('/save-cart', [CartController::class, 'save_cart']);
Route::post('/add-cart-ajax', [CartController::class, 'add_cart_ajax']);
Route::get('/show-cart', [CartController::class, 'show_cart']);
Route::get('/gio-hang', [CartController::class, 'gio_hang']);
// Cart mutations require CSRF-protected methods. Legacy GET URLs redirect without changing cart state.
Route::delete('/delete-to-cart/{rowId}', [CartController::class, 'delete_to_cart']);
Route::get('/delete-to-cart/{rowId}', fn () => Redirect::to('/gio-hang')
    ->with('error', 'Vui lòng dùng nút xóa trên trang giỏ hàng.'));
Route::delete('/del-product/{session_id}', [CartController::class, 'delete_product']);
Route::get('/del-product/{session_id}', fn () => Redirect::to('/gio-hang')
    ->with('error', 'Vui lòng dùng nút xóa trên trang giỏ hàng.'));
Route::post('/update-quick-cart', [CartController::class, 'update_quick_cart']);
Route::match(['post', 'delete'], '/del-all-product', [CartController::class, 'delete_all_product']);
Route::get('/del-all-product', fn () => Redirect::to('/gio-hang')
    ->with('error', 'Vui lòng dùng nút xóa tất cả trên trang giỏ hàng.'));
Route::get('/show-cart-menu', [CartController::class, 'show_cart_menu']);
Route::get('/hover-cart', [CartController::class, 'hover_cart']);
Route::match(['post', 'delete'], '/remove-item', [CartController::class, 'remove_item']);
Route::get('/cart-session', [CartController::class, 'cart_session']);

// Checkout flow
Route::get('/dang-nhap', [CheckoutController::class, 'login_checkout']);
Route::get('/dang-ky', [CheckoutController::class, 'register_checkout']);
Route::post('/dang-xuat-khach-hang', [CheckoutController::class, 'dang_xuat_khachhang']);
Route::get('/dang-xuat-khach-hang', fn () => Redirect::to('/dang-nhap')
    ->with('error', 'Vui lòng dùng nút đăng xuất trên giao diện tài khoản.'));
Route::post('/del-fee', [CheckoutController::class, 'del_fee']);
Route::get('/del-fee', fn () => Redirect::to('/checkout')
    ->with('error', 'Vui lòng cập nhật phí vận chuyển từ trang thanh toán.'));
Route::post('/logout-checkout', [CheckoutController::class, 'logout_checkout']);
Route::get('/logout-checkout', fn () => Redirect::to('/dang-nhap')
    ->with('error', 'Vui lòng dùng nút đăng xuất trên trang thanh toán.'));
Route::post('/add-customer', [CheckoutController::class, 'add_customer'])->middleware('throttle:10,1');
Route::post('/login-customer', [CheckoutController::class, 'login_customer'])->middleware('throttle:10,1');
Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
Route::redirect('/thanh-toan', '/checkout', 301);
Route::post('/checkout', [CheckoutController::class, 'select_checkout_items']);
Route::get('/payment', [CheckoutController::class, 'payment']);
Route::post('/calculate-fee', [CheckoutController::class, 'calculate_fee']);
Route::post('/select-delivery-home', [CheckoutController::class, 'select_delivery_home']);
Route::post('/confirm-order', [CheckoutController::class, 'confirm_order'])->middleware('throttle:5,1');

// Order result pages
Route::get('/dat-hang-thanh-cong/{order_code}', [CheckoutController::class, 'orderSuccess'])->name('order.success');
Route::redirect('/thank-you', '/account/orders', 301);
Route::get('/dat-hang-that-bai/{order_code}', [CheckoutController::class, 'orderFailed'])->name('order.failed');

// Customer order history
Route::get('/view-history-order/{order_code}', function (Request $request, string $order_code) {
    if (! Session::get('customer_id') && Session::get('guest_order_code') === $order_code) {
        return app(OrderController::class)->view_history_order($request, $order_code);
    }

    return redirect()->route('customer.account.order-tracking', ['orderCode' => $order_code], 301);
});
Route::redirect('/history', '/account/orders', 301);
Route::post('/huy-don-hang', [OrderController::class, 'huy_don_hang']);

// Bank transfer instruction page
Route::get('/payment/bank-transfer/{order_code}', [CheckoutController::class, 'bankTransferInstructions'])
    ->name('payment.bank-transfer');

// Customer social login
Route::get('/login-facebook-customer', [AdminController::class, 'login_facebook_customer']);
Route::get('/customer/facebook/callback', [AdminController::class, 'callback_facebook_customer']);
Route::get('/login-customer-google', [AdminController::class, 'login_customer_google']);
Route::get('/customer/google/callback', [AdminController::class, 'callback_customer_google']);

// Video for storefront
Route::redirect('video-shop', '/bai-viet', 301);
Route::post('watch-video', [VideoController::class, 'watch_video']);

// Forgot password flow
Route::get('/quen-mat-khau', [MailController::class, 'quen_mat_khau']);
Route::get('/update-new-pass', [MailController::class, 'update_new_pass']);
Route::post('/recover-pass', [MailController::class, 'recover_pass'])->middleware('throttle:5,1');
Route::post('/reset-new-pass', [MailController::class, 'reset_new_pass'])->middleware('throttle:5,1');

// Payment gateways
Route::get('process-transaction', [PayPalController::class, 'processTransaction'])->name('processTransaction');
Route::get('success-transaction', [PayPalController::class, 'successTransaction'])->name('successTransaction');
Route::get('cancel-transaction', [PayPalController::class, 'cancelTransaction'])->name('cancelTransaction');
// Route::post('/vnpay_payment', [CheckoutController::class, 'vnpay_payment']);
// Route::post('/momo_payment', [CheckoutController::class, 'momo_payment']);

// Compatibility redirects for old Laravel scaffold auth URLs.
// Official auth flows live under the custom admin/customer routes above.
Route::get('/login', fn () => Redirect::to('/admin', 301))->name('login');
Route::post('/login', fn () => Redirect::to('/admin')
    ->with('error', 'Vui lòng đăng nhập bằng trang quản trị chính thức.'));

Route::get('/register', fn () => Redirect::to('/admin/register', 301));
Route::post('/register', fn () => Redirect::to('/admin/register')
    ->with('error', 'Vui lòng đăng ký bằng trang quản trị chính thức.'));

Route::get('/password/reset', fn () => Redirect::to('/admin/forgot-password', 301));
Route::post('/password/email', fn () => Redirect::to('/admin/forgot-password')
    ->with('error', 'Vui lòng dùng trang quên mật khẩu quản trị chính thức.'));
Route::get('/password/reset/{token}', function (string $token) {
    $email = request()->query('email');
    $query = $email ? '?'.http_build_query(['email' => $email]) : '';

    return Redirect::to('/admin/reset-password/'.$token.$query, 301);
});
Route::post('/password/reset', fn () => Redirect::to('/admin/forgot-password')
    ->with('error', 'Vui lòng dùng trang đặt lại mật khẩu quản trị chính thức.'));

Route::get('/password/confirm', fn () => Redirect::to('/admin', 301));
Route::post('/password/confirm', fn () => Redirect::to('/admin'));
Route::post('/logout', fn () => Redirect::to('/admin')
    ->with('error', 'Vui lòng đăng xuất bằng luồng đăng xuất chính thức.'));
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Local/testing payment gateway preview routes.
if (app()->environment(['local', 'testing'])) {
    Route::get('/vnpay-mock/select-method', [\App\Http\Controllers\VnPayMockController::class, 'selectMethod']);
    Route::get('/vnpay-mock/ncb', [\App\Http\Controllers\VnPayMockController::class, 'ncb']);
    Route::get('/vnpay-mock/otp', [\App\Http\Controllers\VnPayMockController::class, 'otp']);
}

// Account management routes
Route::get('/account', [\App\Http\Controllers\CustomerAccountController::class, 'index'])->name('customer.account');
Route::get('/account/addresses', [\App\Http\Controllers\CustomerAccountController::class, 'addresses'])->name('customer.account.addresses');
Route::post('/account/update', [\App\Http\Controllers\CustomerAccountController::class, 'updateProfile'])->name('customer.account.update');
Route::post('/account/review', [\App\Http\Controllers\CustomerAccountController::class, 'postReview'])->name('customer.account.review');
Route::post('/account/change-password', [\App\Http\Controllers\CustomerAccountController::class, 'changePassword'])->name('customer.account.change-password');
Route::get('/account/invoices', [\App\Http\Controllers\CustomerAccountController::class, 'invoices'])->name('customer.account.invoices');
Route::get('/account/change-password', [\App\Http\Controllers\CustomerAccountController::class, 'changePasswordView'])->name('customer.account.change-password.view');
Route::get('/account/cancelled-orders', [\App\Http\Controllers\CustomerAccountController::class, 'cancelledOrders'])->name('customer.account.cancelled-orders');
Route::get('/account/vouchers', [\App\Http\Controllers\CustomerAccountController::class, 'vouchers'])->name('customer.account.vouchers');
Route::get('/account/reviews', [\App\Http\Controllers\CustomerAccountController::class, 'reviews'])->name('customer.account.reviews');
Route::get('/dat-lai-mat-khau', [\App\Http\Controllers\CustomerAccountController::class, 'resetPasswordView'])->name('customer.reset-password');
Route::get('/account/recently-viewed', [\App\Http\Controllers\CustomerAccountController::class, 'recentlyViewed'])->name('customer.account.recently-viewed');
Route::get('/account/payment-methods', [\App\Http\Controllers\CustomerAccountController::class, 'paymentMethods'])->name('customer.account.payment-methods');
Route::get('/account/returns', [\App\Http\Controllers\CustomerAccountController::class, 'returns'])->name('customer.account.returns');
Route::get('/xac-thuc-otp', [\App\Http\Controllers\CustomerAccountController::class, 'otpVerification'])->name('customer.otp-verification');

// Account section pages (previously served with hardcoded data, now backed by controller)
Route::get('/account/orders', [\App\Http\Controllers\CustomerAccountController::class, 'orderManagement'])->name('customer.account.orders');
Route::get('/account/purchase-history', [\App\Http\Controllers\CustomerAccountController::class, 'purchaseHistory'])->name('customer.account.purchase-history');
Route::get('/account/wishlist', [\App\Http\Controllers\CustomerAccountController::class, 'wishlist'])->name('customer.account.wishlist');
Route::get('/account/profile', [\App\Http\Controllers\CustomerAccountController::class, 'profile'])->name('customer.account.profile');
Route::get('/account/order-tracking/{orderCode}', [\App\Http\Controllers\CustomerAccountController::class, 'orderTracking'])->name('customer.account.order-tracking');

// Fix #26: Wishlist DB sync — frontend gọi sau khi đăng nhập để đồng bộ localStorage → DB
Route::post('/account/wishlist/sync', [\App\Http\Controllers\CustomerAccountController::class, 'wishlistSync'])->name('customer.wishlist.sync');
Route::post('/account/wishlist/toggle', [\App\Http\Controllers\CustomerAccountController::class, 'wishlistToggle'])->name('customer.wishlist.toggle');

