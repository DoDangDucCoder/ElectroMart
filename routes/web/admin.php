<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\Catalog\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\Catalog\CategoryController as AdminCategoryController;
use App\Http\Controllers\BrandProduct;
use App\Http\Controllers\CategoryPost;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductAttributeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRolesController;
use App\Http\Controllers\VideoController;

// Contact & site information management
Route::get('/information', [ContactController::class, 'information']);
Route::get('/list-nut', [ContactController::class, 'list_nut']);
Route::get('/delete-icons', [ContactController::class, 'delete_icons']);
Route::get('/list-doitac', [ContactController::class, 'list_doitac']);
Route::post('/add-doitac', [ContactController::class, 'add_doitac']);
Route::post('/add-nut', [ContactController::class, 'add_nut']);
Route::post('/save-info', [ContactController::class, 'save_info']);
Route::post('/update-info/{info_id}', [ContactController::class, 'update_info']);

// Admin authentication & dashboard
Route::get('/admin', [AuthController::class, 'login_auth']);
Route::get('/dashboard', [AdminController::class, 'show_dashboard'])->name('show_dashboard');
Route::post('/admin-dashboard', [AdminController::class, 'dashboard']);
Route::post('/dashboard-filter', [AdminController::class, 'dashboard_filter']);
Route::post('/filter-by-date', [AdminController::class, 'filter_by_date']);
Route::post('/days-order', [AdminController::class, 'days_order']);
Route::get('/order-date', [AdminController::class, 'order_date']);
Route::get('/logout', [AdminController::class, 'logout']);

// Social auth for admin
Route::get('/login-facebook', [AdminController::class, 'login_facebook']);
Route::get('/admin/callback', [AdminController::class, 'callback_facebook']);
Route::get('/login-google', [AdminController::class, 'login_google']);
Route::get('/google/callback', [AdminController::class, 'callback_google']);

// Catalog - categories (RESTful JSON API)
Route::prefix('catalog/categories')->name('admin.catalog.categories.')->group(function () {
    Route::get('/', [AdminCategoryController::class, 'index'])->name('index');
    Route::post('/', [AdminCategoryController::class, 'store'])->name('store');
    Route::get('{category}', [AdminCategoryController::class, 'show'])->name('show');
    Route::put('{category}', [AdminCategoryController::class, 'update'])->name('update');
    Route::patch('{category}', [AdminCategoryController::class, 'update'])->name('partial-update');
    Route::delete('{category}', [AdminCategoryController::class, 'destroy'])->name('destroy');
    Route::patch('{category}/status', [AdminCategoryController::class, 'updateStatus'])->name('status');
    Route::post('reorder', [AdminCategoryController::class, 'reorder'])->name('reorder');
});

// Catalog - brands (RESTful JSON API)
Route::prefix('catalog/brands')->name('admin.catalog.brands.')->group(function () {
    Route::get('/', [AdminBrandController::class, 'index'])->name('index');
    Route::post('/', [AdminBrandController::class, 'store'])->name('store');
    Route::get('{brand}', [AdminBrandController::class, 'show'])->name('show');
    Route::put('{brand}', [AdminBrandController::class, 'update'])->name('update');
    Route::patch('{brand}', [AdminBrandController::class, 'update'])->name('partial-update');
    Route::delete('{brand}', [AdminBrandController::class, 'destroy'])->name('destroy');
    Route::patch('{brand}/status', [AdminBrandController::class, 'updateStatus'])->name('status');
});

// Brand management (legacy blade interface)
Route::get('/add-brand-product', [BrandProduct::class, 'add_brand_product']);
Route::get('/all-brand-product', [BrandProduct::class, 'all_brand_product']);
Route::post('/save-brand-product', [BrandProduct::class, 'save_brand_product']);
Route::get('/edit-brand-product/{brand_id}', [BrandProduct::class, 'edit_brand_product']);
Route::post('/update-brand-product/{brand_id}', [BrandProduct::class, 'update_brand_product']);
Route::get('/delete-brand-product/{brand_id}', [BrandProduct::class, 'delete_brand_product']);
Route::get('/unactive-brand-product/{brand_id}', [BrandProduct::class, 'unactive_brand_product']);
Route::get('/active-brand-product/{brand_id}', [BrandProduct::class, 'active_brand_product']);

// Category post management
Route::get('/add-category-post', [CategoryPost::class, 'add_category_post']);
Route::get('/all-category-post', [CategoryPost::class, 'all_category_post']);
Route::get('/edit-category-post/{category_post_id}', [CategoryPost::class, 'edit_category_post']);
Route::post('/save-category-post', [CategoryPost::class, 'save_category_post']);
Route::post('/update-category-post/{cate_id}', [CategoryPost::class, 'update_category_post']);
Route::get('/delete-category-post/{cate_id}', [CategoryPost::class, 'delete_category_post']);

// Post management
Route::get('/add-post', [PostController::class, 'add_post']);
Route::get('/all-post', [PostController::class, 'all_post']);
Route::get('/delete-post/{post_id}', [PostController::class, 'delete_post']);
Route::get('/edit-post/{post_id}', [PostController::class, 'edit_post']);
Route::post('/save-post', [PostController::class, 'save_post']);
Route::post('/update-post/{post_id}', [PostController::class, 'update_post']);

// Product management
Route::get('/add-product', [ProductController::class, 'add_product']);
Route::get('/edit-product/{product_id}', [ProductController::class, 'edit_product']);
Route::get('/delete-product/{product_id}', [ProductController::class, 'delete_product']);
Route::get('/all-product', [ProductController::class, 'all_product']);
Route::get('/unactive-product/{product_id}', [ProductController::class, 'unactive_product']);
Route::get('/active-product/{product_id}', [ProductController::class, 'active_product']);
Route::post('/save-product', [ProductController::class, 'save_product']);
Route::post('/update-product/{product_id}', [ProductController::class, 'update_product']);
Route::post('/delete-document', [ProductController::class, 'delete_document']);
Route::get('/gg-document', [ProductController::class, 'gg_document']);

// Comment moderation & editor utilities
Route::get('/comment', [ProductController::class, 'list_comment']);
Route::post('/allow-comment', [ProductController::class, 'allow_comment']);
Route::post('/reply-comment', [ProductController::class, 'reply_comment']);
Route::post('/uploads-ckeditor', [ProductController::class, 'ckeditor_image']);
Route::get('/file-browser', [ProductController::class, 'file_browser']);

// Coupon management
Route::get('/insert-coupon', [CouponController::class, 'insert_coupon']);
Route::get('/delete-coupon/{coupon_id}', [CouponController::class, 'delete_coupon']);
Route::get('/list-coupon', [CouponController::class, 'list_coupon']);
Route::post('/insert-coupon-code', [CouponController::class, 'insert_coupon_code']);

// User administration
Route::get('users', [UserController::class, 'index']);
Route::get('add-users', [UserController::class, 'add_users']);
Route::get('delete-user-roles/{admin_id}', [UserController::class, 'delete_user_roles']);
Route::post('store-users', [UserController::class, 'store_users']);
Route::post('assign-roles', [UserController::class, 'assign_roles']);
Route::get('impersonate/{admin_id}', [UserController::class, 'impersonate']);
Route::get('impersonate-destroy', [UserController::class, 'impersonate_destroy']);

// Order administration
Route::get('/delete-order/{order_code}', [OrderController::class, 'order_code']);
Route::get('/print-order/{checkout_code}', [OrderController::class, 'print_order']);
Route::get('/manage-order', [OrderController::class, 'manage_order']);
Route::get('/view-order/{order_code}', [OrderController::class, 'view_order']);
Route::post('/update-order-qty', [OrderController::class, 'update_order_qty']);
Route::post('/update-qty', [OrderController::class, 'update_qty']);

// Delivery / shipping fee management
Route::get('/delivery', [DeliveryController::class, 'delivery']);
Route::post('/select-delivery', [DeliveryController::class, 'select_delivery']);
Route::post('/insert-delivery', [DeliveryController::class, 'insert_delivery']);
Route::post('/select-feeship', [DeliveryController::class, 'select_feeship']);
Route::post('/update-delivery', [DeliveryController::class, 'update_delivery']);

// Slider management
Route::get('/manage-slider', [SliderController::class, 'manage_slider']);
Route::get('/add-slider', [SliderController::class, 'add_slider']);
Route::get('/delete-slide/{slide_id}', [SliderController::class, 'delete_slide']);
Route::post('/insert-slider', [SliderController::class, 'insert_slider']);
Route::get('/unactive-slide/{slide_id}', [SliderController::class, 'unactive_slide']);
Route::get('/active-slide/{slide_id}', [SliderController::class, 'active_slide']);

// Auth controller (custom admin auth flow)
Route::get('/register-auth', [AuthController::class, 'register_auth']);
Route::get('/login-auth', [AuthController::class, 'login_auth']);
Route::get('/logout-auth', [AuthController::class, 'logout_auth']);
Route::post('/auth-register', [AuthController::class, 'register_auth_dashboard']);
Route::post('/auth-login', [AuthController::class, 'login']);

// Gallery management
Route::get('add-gallery/{product_id}', [GalleryController::class, 'add_gallery']);
Route::post('select-gallery', [GalleryController::class, 'select_gallery']);
Route::post('insert-gallery/{pro_id}', [GalleryController::class, 'insert_gallery']);
Route::post('update-gallery-name', [GalleryController::class, 'update_gallery_name']);
Route::post('delete-gallery', [GalleryController::class, 'delete_gallery']);
Route::post('update-gallery', [GalleryController::class, 'update_gallery']);

// Video management
Route::get('video', [VideoController::class, 'video']);
Route::post('select-video', [VideoController::class, 'select_video']);
Route::post('insert-video', [VideoController::class, 'insert_video']);
Route::post('update-video', [VideoController::class, 'update_video']);
Route::post('delete-video', [VideoController::class, 'delete_video']);
Route::post('update-video-image', [VideoController::class, 'update_video_image']);

// Document management
Route::get('upload_file', [DocumentController::class, 'upload_file']);
Route::get('upload_image', [DocumentController::class, 'upload_image']);
Route::get('upload_video', [DocumentController::class, 'upload_video']);
Route::get('download_document/{path}/{name}', [DocumentController::class, 'download_document']);
Route::get('create_document', [DocumentController::class, 'create_document']);
Route::get('create_sub_dir', [DocumentController::class, 'create_sub_dir']);
Route::get('delete_document/{path}', [DocumentController::class, 'delete_document']);
Route::get('create_folder', [DocumentController::class, 'create_folder']);
Route::get('rename_folder', [DocumentController::class, 'rename_folder']);
Route::get('delete_folder', [DocumentController::class, 'delete_folder']);
Route::get('list_document', [DocumentController::class, 'list_document']);
Route::get('read_data', [DocumentController::class, 'read_data']);

// Product attributes
Route::get('product-attribute/{product_id}', [ProductAttributeController::class, 'show']);
Route::post('/product-attribute', [ProductAttributeController::class, 'store'])->name('product-attribute.store');
Route::delete('/product-attribute/{id}', [ProductAttributeController::class, 'deleteProductAttribute'])->name('product-attribute.delete');
Route::get('/attribute', [AttributeController::class, 'index'])->name('attribute.index');
Route::get('/attribute/create', [AttributeController::class, 'create'])->name('attribute.create');
Route::post('/attribute', [AttributeController::class, 'store'])->name('attribute.store');
Route::get('/attribute/{id}/edit', [AttributeController::class, 'edit'])->name('attribute.edit');
Route::put('/attribute/{id}', [AttributeController::class, 'update'])->name('attribute.update');
Route::delete('/attribute/{id}', [AttributeController::class, 'destroy'])->name('attribute.destroy');

// Marketing mail & coupon utilities
Route::get('/mail-example', [MailController::class, 'mail_example']);
Route::get('/send-mail', [MailController::class, 'send_mail']);
Route::get(
    '/send-coupon-vip/{coupon_time}/{coupon_condition}/{coupon_number}/{coupon_code}',
    [MailController::class, 'send_coupon_vip']
);
Route::get(
    '/send-coupon/{coupon_time}/{coupon_condition}/{coupon_number}/{coupon_code}',
    [MailController::class, 'send_coupon']
);

// Role & permission management
Route::get('/create-spatie/{admin_id}', [UserRolesController::class, 'create']);
Route::get('/create-permission/{admin_id}', [UserRolesController::class, 'create_permission']);
Route::get('/index-spatie', [UserRolesController::class, 'index']);
Route::post('/assign-role/{admin_id}', [UserRolesController::class, 'assign_role']);
Route::post('/assign-permission/{admin_id}', [UserRolesController::class, 'assign_permission']);
