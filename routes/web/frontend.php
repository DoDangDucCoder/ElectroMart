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
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

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
Route::post('/autocomplete-ajax', [HomeController::class, 'autocomplete_ajax']);
Route::get('/yeu-thich', [HomeController::class, 'yeu_thich']);
Route::get('/goi-y-san-pham', [HomeController::class, 'recommendations']);
Route::get('/khuyen-mai', [HomeController::class, 'khuyenMai']);
Route::get('/kiem-tra-don-hang', [HomeController::class, 'orderTracking']);
Route::get('/chatbot/conversation', [ChatController::class, 'conversation']);
Route::post('/chatbot/messages', [ChatController::class, 'store']);

// Contact page
Route::get('/lien-he', [ContactController::class, 'lien_he']);
Route::post('/lien-he', [ContactController::class, 'submitContact']);

// Customer support center
Route::view('/cham-soc-khach-hang', 'pages.support.index')->name('support.index');
Route::view('/cau-hoi-thuong-gap', 'pages.support.faq')->name('support.faq');
Route::view('/chinh-sach-bao-hanh', 'pages.support.warranty')->name('support.warranty');
Route::view('/chinh-sach-doi-tra', 'pages.support.doi-tra')->name('support.doi-tra');
Route::view('/chinh-sach-bao-mat', 'pages.support.bao-mat')->name('support.bao-mat');
Route::view('/chinh-sach-thanh-toan', 'pages.support.thanh-toan')->name('support.thanh-toan');
Route::view('/chinh-sach-van-chuyen', 'pages.support.van-chuyen')->name('support.van-chuyen');
Route::view('/dieu-khoan-su-dung', 'pages.support.dieu-khoan')->name('support.dieu-khoan');
Route::view('/huong-dan-mua-hang', 'pages.support.huong-dan-mua-hang')->name('support.huong-dan-mua-hang');
Route::view('/huong-dan-thanh-toan', 'pages.support.huong-dan-thanh-toan')->name('support.huong-dan-thanh-toan');
Route::view('/gioi-thieu', 'pages.support.gioi-thieu')->name('support.gioi-thieu');

// Catalog & product detail
Route::get('/danh-muc/{slug_category_product}', [CategoryProduct::class, 'show_category_home']);
Route::post('/product-tabs', [CategoryProduct::class, 'product_tabs']);
Route::get('/chi-tiet/{product_slug}', [ProductController::class, 'details_product']);
Route::get('/tag/{product_tag}', [ProductController::class, 'tag']);
Route::post('/quickview', [ProductController::class, 'quickview']);
Route::post('/load-comment', [ProductController::class, 'load_comment']);
Route::post('/send-comment', [ProductController::class, 'send_comment']);
Route::post('/insert-rating', [ProductController::class, 'insert_rating']);
Route::get('/so-sanh', [CompareController::class, 'index']);

// Blog
Route::get('/bai-viet', [PostController::class, 'index'])->name('blog.index');
Route::get('/danh-muc-bai-viet/{post_slug}', [PostController::class, 'danh_muc_bai_viet']);
Route::get('/bai-viet/{post_slug}', [PostController::class, 'bai_viet']);
Route::get('/thuong-hieu/{brandSlug}', [BrandProduct::class, 'show_brand_home']);

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
Route::post('/add-customer', [CheckoutController::class, 'add_customer']);
Route::post('/login-customer', [CheckoutController::class, 'login_customer']);
Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
Route::get('/payment', [CheckoutController::class, 'payment']);
Route::post('/calculate-fee', [CheckoutController::class, 'calculate_fee']);
Route::post('/select-delivery-home', [CheckoutController::class, 'select_delivery_home']);
Route::post('/confirm-order', [CheckoutController::class, 'confirm_order']);

// Order result pages
Route::get('/dat-hang-thanh-cong/{order_code}', [CheckoutController::class, 'orderSuccess'])->name('order.success');
Route::get('/dat-hang-that-bai/{order_code}', [CheckoutController::class, 'orderFailed'])->name('order.failed');

// Customer order history
Route::get('/view-history-order/{order_code}', [OrderController::class, 'view_history_order']);
Route::get('/history', [OrderController::class, 'history']);
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
Route::get('video-shop', [VideoController::class, 'video_shop']);
Route::post('watch-video', [VideoController::class, 'watch_video']);

// Forgot password flow
Route::get('/quen-mat-khau', [MailController::class, 'quen_mat_khau']);
Route::get('/update-new-pass', [MailController::class, 'update_new_pass']);
Route::post('/recover-pass', [MailController::class, 'recover_pass']);
Route::post('/reset-new-pass', [MailController::class, 'reset_new_pass']);

// Payment gateways
Route::get('process-transaction', [PayPalController::class, 'processTransaction'])->name('processTransaction');
Route::get('success-transaction', [PayPalController::class, 'successTransaction'])->name('successTransaction');
Route::get('cancel-transaction', [PayPalController::class, 'cancelTransaction'])->name('cancelTransaction');
// Route::post('/vnpay_payment', [CheckoutController::class, 'vnpay_payment']);
// Route::post('/momo_payment', [CheckoutController::class, 'momo_payment']);

// Compatibility redirects for old Laravel scaffold auth URLs.
// Official auth flows live under the custom admin/customer routes above.
Route::get('/login', fn () => Redirect::to('/admin'))->name('login');
Route::post('/login', fn () => Redirect::to('/admin')
    ->with('error', 'Vui lòng đăng nhập bằng trang quản trị chính thức.'));

Route::get('/register', fn () => Redirect::to('/admin/register'));
Route::post('/register', fn () => Redirect::to('/admin/register')
    ->with('error', 'Vui lòng đăng ký bằng trang quản trị chính thức.'));

Route::get('/password/reset', fn () => Redirect::to('/admin/forgot-password'));
Route::post('/password/email', fn () => Redirect::to('/admin/forgot-password')
    ->with('error', 'Vui lòng dùng trang quên mật khẩu quản trị chính thức.'));
Route::get('/password/reset/{token}', function (string $token) {
    $email = request()->query('email');
    $query = $email ? '?'.http_build_query(['email' => $email]) : '';

    return Redirect::to('/admin/reset-password/'.$token.$query);
});
Route::post('/password/reset', fn () => Redirect::to('/admin/forgot-password')
    ->with('error', 'Vui lòng dùng trang đặt lại mật khẩu quản trị chính thức.'));

Route::get('/password/confirm', fn () => Redirect::to('/admin'));
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
