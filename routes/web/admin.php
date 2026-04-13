<?php

use Illuminate\Support\Facades\Route;
use App\Support\AdminPermissions;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\AdminWorkspaceController;
use App\Http\Controllers\CategoryProductAdminController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\Catalog\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\Catalog\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ChatInboxController;
use App\Http\Controllers\Admin\ProductDiscoveryReportController;
use App\Http\Controllers\Admin\ProductProfitController;
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

Route::middleware('guest:web')->group(function () {
    Route::get('/admin', [AuthController::class, 'login_auth'])->name('admin.login');
    Route::get('/admin/register', [AuthController::class, 'showRegisterForm'])->name('admin.register');
    Route::post('/admin/register', [AuthController::class, 'register'])->name('admin.register.store');
    Route::get('/admin/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('admin.password.request');
    Route::post('/admin/forgot-password', [AuthController::class, 'sendResetLink'])->name('admin.password.email');
    Route::get('/admin/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('admin.password.reset');
    Route::post('/admin/reset-password', [AuthController::class, 'resetPassword'])->name('admin.password.update');
    Route::post('/auth-login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('admin.login.attempt');
});

Route::middleware(['auth:web', 'admin.access'])->group(function () {
    // Admin dashboard
    Route::get('/dashboard', [AdminController::class, 'show_dashboard'])->name('admin.dashboard');
    Route::get('/admin/quick-search', [AdminWorkspaceController::class, 'search'])->name('admin.tools.search');
    Route::get('/admin/profile', [AdminWorkspaceController::class, 'profile'])->name('admin.profile.show');
    Route::put('/admin/profile', [AdminWorkspaceController::class, 'updateProfile'])->name('admin.profile.update');
    Route::get('/admin/settings', [AdminWorkspaceController::class, 'settings'])->name('admin.settings');
    Route::post('/logout-auth', [AuthController::class, 'logout_auth'])->name('admin.logout');
    Route::post('/dashboard-filter', [AdminController::class, 'dashboard_filter']);
    Route::post('/filter-by-date', [AdminController::class, 'filter_by_date']);
    Route::post('/days-order', [AdminController::class, 'days_order']);
    Route::get('/order-date', [AdminController::class, 'order_date']);

    Route::middleware('admin.permission:' . AdminPermissions::MANAGE_SYSTEM)->group(function () {
        // Contact & site information management
        Route::get('/information', [ContactController::class, 'information']);
        Route::get('/mail-preview', [MailController::class, 'preview'])->name('admin.mail.preview');
        Route::get('/mail-preview/render', [MailController::class, 'preview_render'])->name('admin.mail.preview.render');
        Route::get('/product-discovery-report', [ProductDiscoveryReportController::class, 'index'])->name('admin.product-discovery-report');
        Route::get('/chat-inbox', [ChatInboxController::class, 'index'])->name('admin.chat-inbox');
        Route::get('/chat-inbox/conversations', [ChatInboxController::class, 'list'])->name('admin.chat-inbox.list');
        Route::get('/chat-inbox/conversations/{conversation}', [ChatInboxController::class, 'show'])->name('admin.chat-inbox.show');
        Route::post('/chat-inbox/conversations/{conversation}/reply', [ChatInboxController::class, 'reply'])->name('admin.chat-inbox.reply');
        Route::post('/chat-inbox/conversations/{conversation}/assign', [ChatInboxController::class, 'assign'])->name('admin.chat-inbox.assign');
        Route::patch('/chat-inbox/conversations/{conversation}/status', [ChatInboxController::class, 'updateStatus'])->name('admin.chat-inbox.status');
        Route::get('/list-nut', [ContactController::class, 'list_nut']);
        Route::delete('/delete-icons', [ContactController::class, 'delete_icons']);
        Route::get('/list-doitac', [ContactController::class, 'list_doitac']);
        Route::post('/add-doitac', [ContactController::class, 'add_doitac']);
        Route::post('/add-nut', [ContactController::class, 'add_nut']);
        Route::post('/save-info', [ContactController::class, 'save_info']);
        Route::post('/update-info/{info_id}', [ContactController::class, 'update_info']);
    });

    Route::middleware('admin.permission:' . AdminPermissions::MANAGE_PRODUCTS)->group(function () {
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

        // Product category management (legacy blade interface, read-only safe routes)
        Route::get('/add-category-product', [CategoryProductAdminController::class, 'add_category_product']);
        Route::get('/all-category-product', [CategoryProductAdminController::class, 'all_category_product']);
        Route::get('/edit-category-product/{category_id}', [CategoryProductAdminController::class, 'edit_category_product']);

        // Brand management (legacy blade interface)
        Route::get('/add-brand-product', [BrandProduct::class, 'add_brand_product']);
        Route::get('/all-brand-product', [BrandProduct::class, 'all_brand_product']);
        Route::post('/save-brand-product', [BrandProduct::class, 'save_brand_product']);
        Route::get('/edit-brand-product/{brand_id}', [BrandProduct::class, 'edit_brand_product']);
        Route::post('/update-brand-product/{brand_id}', [BrandProduct::class, 'update_brand_product']);
        Route::delete('/delete-brand-product/{brand_id}', [BrandProduct::class, 'delete_brand_product']);
        Route::patch('/unactive-brand-product/{brand_id}', [BrandProduct::class, 'unactive_brand_product']);
        Route::patch('/active-brand-product/{brand_id}', [BrandProduct::class, 'active_brand_product']);
    });

    Route::middleware('admin.permission:' . AdminPermissions::MANAGE_CONTENT)->group(function () {
        // Category post management
        Route::get('/add-category-post', [CategoryPost::class, 'add_category_post']);
        Route::get('/all-category-post', [CategoryPost::class, 'all_category_post']);
        Route::get('/edit-category-post/{category_post_id}', [CategoryPost::class, 'edit_category_post']);
        Route::post('/save-category-post', [CategoryPost::class, 'save_category_post']);
        Route::post('/update-category-post/{cate_id}', [CategoryPost::class, 'update_category_post']);
        Route::delete('/delete-category-post/{cate_id}', [CategoryPost::class, 'delete_category_post']);

        // Post management
        Route::get('/add-post', [PostController::class, 'add_post']);
        Route::get('/all-post', [PostController::class, 'all_post']);
        Route::delete('/delete-post/{post_id}', [PostController::class, 'delete_post']);
        Route::get('/edit-post/{post_id}', [PostController::class, 'edit_post']);
        Route::post('/save-post', [PostController::class, 'save_post']);
        Route::post('/update-post/{post_id}', [PostController::class, 'update_post']);

        // Slider management
        Route::get('/manage-slider', [SliderController::class, 'manage_slider']);
        Route::get('/add-slider', [SliderController::class, 'add_slider']);
        Route::delete('/delete-slide/{slide_id}', [SliderController::class, 'delete_slide']);
        Route::post('/insert-slider', [SliderController::class, 'insert_slider']);
        Route::patch('/unactive-slide/{slide_id}', [SliderController::class, 'unactive_slide']);
        Route::patch('/active-slide/{slide_id}', [SliderController::class, 'active_slide']);

        // Video management
        Route::get('video', [VideoController::class, 'video']);
        Route::post('select-video', [VideoController::class, 'select_video']);
        Route::post('insert-video', [VideoController::class, 'insert_video']);
        Route::post('update-video', [VideoController::class, 'update_video']);
        Route::post('delete-video', [VideoController::class, 'delete_video']);
        Route::post('update-video-image', [VideoController::class, 'update_video_image']);
    });

    Route::middleware('admin.permission:' . AdminPermissions::MANAGE_PRODUCTS)->group(function () {
        // Product management
        Route::get('/add-product', [ProductController::class, 'add_product']);
        Route::get('/edit-product/{product_id}', [ProductController::class, 'edit_product']);
        Route::delete('/delete-product/{product_id}', [ProductController::class, 'delete_product']);
        Route::get('/all-product', [ProductController::class, 'all_product']);
        Route::patch('/unactive-product/{product_id}', [ProductController::class, 'unactive_product']);
        Route::patch('/active-product/{product_id}', [ProductController::class, 'active_product']);
        Route::post('/save-product', [ProductController::class, 'save_product']);
        Route::post('/update-product/{product_id}', [ProductController::class, 'update_product']);
        Route::post('/delete-document', [ProductController::class, 'delete_document']);
        Route::get('/gg-document', [ProductController::class, 'gg_document']);
    });

    Route::middleware('admin.permission:' . AdminPermissions::MANAGE_PRODUCTS)->group(function () {
        // Comment moderation & editor utilities
        Route::get('/comment', [ProductController::class, 'list_comment']);
        Route::post('/allow-comment', [ProductController::class, 'allow_comment']);
        Route::post('/reply-comment', [ProductController::class, 'reply_comment']);
        Route::post('/uploads-ckeditor', [ProductController::class, 'ckeditor_image']);
        Route::get('/file-browser', [ProductController::class, 'file_browser']);

        // Gallery management
        Route::get('add-gallery/{product_id}', [GalleryController::class, 'add_gallery']);
        Route::post('select-gallery', [GalleryController::class, 'select_gallery']);
        Route::post('insert-gallery/{pro_id}', [GalleryController::class, 'insert_gallery']);
        Route::post('update-gallery-name', [GalleryController::class, 'update_gallery_name']);
        Route::post('delete-gallery', [GalleryController::class, 'delete_gallery']);
        Route::post('update-gallery', [GalleryController::class, 'update_gallery']);

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
    });

    Route::middleware('admin.permission:' . AdminPermissions::MANAGE_COUPONS)->group(function () {
        // Coupon management
        Route::get('/insert-coupon', [CouponController::class, 'insert_coupon']);
        Route::delete('/delete-coupon/{coupon_id}', [CouponController::class, 'delete_coupon']);
        Route::get('/list-coupon', [CouponController::class, 'list_coupon']);
        Route::post('/insert-coupon-code', [CouponController::class, 'insert_coupon_code']);
    });

    Route::middleware('admin.permission:' . AdminPermissions::MANAGE_USERS)->group(function () {
        // User administration
        Route::get('users', [UserController::class, 'index']);
        Route::get('add-users', [UserController::class, 'add_users']);
        Route::delete('delete-user-roles/{admin_id}', [UserController::class, 'delete_user_roles']);
        Route::post('store-users', [UserController::class, 'store_users']);
        Route::post('assign-roles', [UserController::class, 'assign_roles']);
    });

    Route::middleware('admin.permission:' . AdminPermissions::MANAGE_ORDERS)->group(function () {
        // Order administration
        Route::delete('/delete-order/{order_code}', [OrderController::class, 'order_code']);
        Route::get('/print-order/{checkout_code}', [OrderController::class, 'print_order']);
        Route::get('/manage-order', [OrderController::class, 'manage_order']);
        Route::get('/product-profit', [ProductProfitController::class, 'index'])->name('admin.product-profit');
        Route::get('/view-order/{order_code}', [OrderController::class, 'view_order']);
        Route::post('/update-order-qty', [OrderController::class, 'update_order_qty']);
        Route::post('/update-qty', [OrderController::class, 'update_qty']);

        // Delivery / shipping fee management
        Route::get('/delivery', [DeliveryController::class, 'delivery']);
        Route::post('/select-delivery', [DeliveryController::class, 'select_delivery']);
        Route::post('/insert-delivery', [DeliveryController::class, 'insert_delivery']);
        Route::post('/select-feeship', [DeliveryController::class, 'select_feeship']);
        Route::post('/update-delivery', [DeliveryController::class, 'update_delivery']);
    });

    Route::middleware('admin.permission:' . AdminPermissions::MANAGE_DOCUMENTS)->group(function () {
        // Document management
        Route::get('download_document/{path}/{name}', [DocumentController::class, 'download_document'])
            ->where('path', '.*');
        Route::delete('delete_document/{path}', [DocumentController::class, 'delete_document'])
            ->where('path', '.*');
        Route::get('read_data', [DocumentController::class, 'read_data']);
    });

    Route::middleware('admin.permission:' . AdminPermissions::MANAGE_COUPONS)->group(function () {
        // Marketing mail & coupon utilities
        Route::post(
            '/send-coupon-vip/{coupon_time}/{coupon_condition}/{coupon_number}/{coupon_code}',
            [MailController::class, 'send_coupon_vip']
        );
        Route::post(
            '/send-coupon/{coupon_time}/{coupon_condition}/{coupon_number}/{coupon_code}',
            [MailController::class, 'send_coupon']
        );
    });

    Route::middleware('admin.permission:' . AdminPermissions::MANAGE_ROLES)->group(function () {
        // Role & permission management
        Route::get('/create-spatie/{admin_id}', [UserRolesController::class, 'create']);
        Route::get('/create-permission/{admin_id}', [UserRolesController::class, 'create_permission']);
        Route::get('/index-spatie', [UserRolesController::class, 'index']);
        Route::post('/assign-role/{admin_id}', [UserRolesController::class, 'assign_role']);
        Route::post('/assign-permission/{admin_id}', [UserRolesController::class, 'assign_permission']);
    });
});
