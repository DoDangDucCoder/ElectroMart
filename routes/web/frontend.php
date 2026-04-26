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
Route::get('/san-pham-ban-chay', [HomeController::class, 'best_selling']);
Route::post('/autocomplete-ajax', [HomeController::class, 'autocomplete_ajax']);
Route::get('/yeu-thich', [HomeController::class, 'yeu_thich']);
Route::get('/goi-y-san-pham', [HomeController::class, 'recommendations']);
Route::get('/chatbot/conversation', [ChatController::class, 'conversation']);
Route::post('/chatbot/messages', [ChatController::class, 'store']);

// Contact page
Route::get('/lien-he', [ContactController::class, 'lien_he']);

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
Route::get('/danh-muc-bai-viet/{post_slug}', [PostController::class, 'danh_muc_bai_viet']);
Route::get('/bai-viet/{post_slug}', [PostController::class, 'bai_viet']);
Route::get('/thuong-hieu/{brandSlug}', [BrandProduct::class, 'show_brand_home']);

// Coupon (customer side)
Route::post('/check-coupon', [CartController::class, 'check_coupon']);
Route::get('/unset-coupon', [CouponController::class, 'unset_coupon']);

// Cart routes
Route::post('/update-cart-quantity', [CartController::class, 'update_cart_quantity']);
Route::post('/update-cart', [CartController::class, 'update_cart']);
Route::post('/save-cart', [CartController::class, 'save_cart']);
Route::post('/add-cart-ajax', [CartController::class, 'add_cart_ajax']);
Route::get('/show-cart', [CartController::class, 'show_cart']);
Route::get('/gio-hang', [CartController::class, 'gio_hang']);
// Xóa item: dùng GET để link trực tiếp từ trang cart (có nút xóa), giữ nguyên để tương thích
Route::get('/delete-to-cart/{rowId}', [CartController::class, 'delete_to_cart']);
Route::get('/del-product/{session_id}', [CartController::class, 'delete_product']);
Route::post('/update-quick-cart', [CartController::class, 'update_quick_cart']);
// Xóa toàn bộ giỏ hàng: giữ GET cho các link legacy, POST cho form/AJAX mới.
Route::match(['get', 'post'], '/del-all-product', [CartController::class, 'delete_all_product']);
Route::get('/show-cart-menu', [CartController::class, 'show_cart_menu']);
Route::get('/hover-cart', [CartController::class, 'hover_cart']);
// remove-item dùng POST/DELETE để tránh CSRF
Route::match(['get', 'post', 'delete'], '/remove-item', [CartController::class, 'remove_item']);
Route::get('/cart-session', [CartController::class, 'cart_session']);

// Checkout flow
Route::get('/dang-nhap', [CheckoutController::class, 'login_checkout']);
Route::get('/dang-ky', [CheckoutController::class, 'register_checkout']);
Route::get('/dang-xuat-khach-hang', [CheckoutController::class, 'dang_xuat_khachhang']);
Route::get('/del-fee', [CheckoutController::class, 'del_fee']);
Route::get('/logout-checkout', [CheckoutController::class, 'logout_checkout']);
Route::post('/add-customer', [CheckoutController::class, 'add_customer']);
Route::post('/login-customer', [CheckoutController::class, 'login_customer']);
Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
Route::get('/payment', [CheckoutController::class, 'payment']);
Route::post('/calculate-fee', [CheckoutController::class, 'calculate_fee']);
Route::post('/select-delivery-home', [CheckoutController::class, 'select_delivery_home']);
Route::post('/confirm-order', [CheckoutController::class, 'confirm_order']);

// Customer order history
Route::get('/view-history-order/{order_code}', [OrderController::class, 'view_history_order']);
Route::get('/history', [OrderController::class, 'history']);
Route::post('/huy-don-hang', [OrderController::class, 'huy_don_hang']);

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

// Laravel default auth scaffolding
Auth::routes();
Route::get('/home', [HomeController::class, 'index'])->name('home');

// VNPay Mock Routes
Route::get('/vnpay-mock/select-method', [\App\Http\Controllers\VnPayMockController::class, 'selectMethod']);
Route::get('/vnpay-mock/ncb', [\App\Http\Controllers\VnPayMockController::class, 'ncb']);
Route::get('/vnpay-mock/otp', [\App\Http\Controllers\VnPayMockController::class, 'otp']);

// Account management routes
Route::get('/account', [\App\Http\Controllers\CustomerAccountController::class, 'index'])->name('customer.account');
Route::post('/account/update', [\App\Http\Controllers\CustomerAccountController::class, 'updateProfile'])->name('customer.account.update');
Route::post('/account/review', [\App\Http\Controllers\CustomerAccountController::class, 'postReview'])->name('customer.account.review');
Route::post('/account/change-password', [\App\Http\Controllers\CustomerAccountController::class, 'changePassword'])->name('customer.account.change-password');
