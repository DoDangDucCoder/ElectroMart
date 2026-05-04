<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductReviewController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\ChatController as ChatbotController;
use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use App\Support\AdminPermissions;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes
Route::prefix('v1')->group(function () {
    
    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/latest', [ProductController::class, 'latest']);
    Route::get('/products/featured', [ProductController::class, 'featured']);
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/products/recommendations', [ProductController::class, 'recommendations']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::get('/products/category/{categorySlug}', [ProductController::class, 'byCategory']);
    Route::get('/products/brand/{brandSlug}', [ProductController::class, 'byBrand']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/tree', [CategoryController::class, 'tree']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);

    // Brands
    Route::get('/brands', [BrandController::class, 'index']);
    Route::get('/brands/{slug}', [BrandController::class, 'show']);

    // Authentication
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    // Health check
    Route::get('/health', [\App\Http\Controllers\Api\HealthController::class, 'check']);
    Route::get('/health/cache', [\App\Http\Controllers\Api\HealthController::class, 'cache']);
    Route::get('/health/performance', [\App\Http\Controllers\Api\HealthController::class, 'performance']);
    
    // API Documentation
    Route::get('/docs', [\App\Http\Controllers\Api\ApiDocumentationController::class, 'index']);

    // Product Reviews
    Route::get('/reviews', [ProductReviewController::class, 'index']);
    Route::get('/reviews/{id}', [ProductReviewController::class, 'show']);

    // Coupons
    Route::get('/coupons', [CouponController::class, 'index']);
    Route::get('/coupons/{code}', [CouponController::class, 'show']);
    Route::post('/coupons/validate', [CouponController::class, 'validate_coupon']);


    // Blog
    Route::get('/blog', [BlogController::class, 'index']);
    Route::get('/blog/featured', [BlogController::class, 'featured']);
    Route::get('/blog/latest', [BlogController::class, 'latest']);
    Route::get('/blog/search', [BlogController::class, 'search']);
    Route::get('/blog/{slug}', [BlogController::class, 'show']);

    // Settings
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::get('/settings/contact', [SettingsController::class, 'contact']);
    Route::get('/settings/seo', [SettingsController::class, 'seo']);
    Route::get('/settings/{key}', [SettingsController::class, 'show']);

    // Chatbot
    Route::middleware('throttle:10,1')->group(function () {
        Route::get('/chatbot/conversation', [ChatbotController::class, 'conversation']);
        Route::post('/chatbot/messages', [ChatbotController::class, 'store']);
    });

    // Admin authentication
    Route::post('/admin/auth/login', [AdminAuthController::class, 'login']);
});

// Protected customer API routes
Route::prefix('v1')->middleware(['auth:sanctum', 'customer.api'])->group(function () {
    
    // User profile
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user', [AuthController::class, 'updateProfile']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::put('/cart/update/{itemId}', [CartController::class, 'update']);
    Route::delete('/cart/remove/{itemId}', [CartController::class, 'remove']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel']);

    // Product Reviews (Authenticated)
    Route::post('/reviews', [ProductReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ProductReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ProductReviewController::class, 'destroy']);
});

// Admin API routes
Route::prefix('v1/admin')->middleware(['auth:sanctum', 'admin.access:sanctum'])->group(function () {
    Route::get('auth/me', [AdminAuthController::class, 'me']);
    Route::post('auth/logout', [AdminAuthController::class, 'logout']);

    Route::middleware('admin.permission:' . AdminPermissions::MANAGE_PRODUCTS)->group(function () {
        // Admin products
        Route::apiResource('products', \App\Http\Controllers\Api\Admin\ProductController::class);
        Route::post('products/{id}/toggle-status', [\App\Http\Controllers\Api\Admin\ProductController::class, 'toggleStatus']);

        // Admin categories
        Route::apiResource('categories', \App\Http\Controllers\Api\Admin\CategoryController::class);

        // Admin brands
        Route::apiResource('brands', \App\Http\Controllers\Api\Admin\BrandController::class);
    });

    Route::middleware('admin.permission:' . AdminPermissions::MANAGE_ORDERS)->group(function () {
        // Admin orders
        Route::get('orders', [\App\Http\Controllers\Api\Admin\OrderController::class, 'index']);
        Route::get('orders/{id}', [\App\Http\Controllers\Api\Admin\OrderController::class, 'show']);
        Route::put('orders/{id}/status', [\App\Http\Controllers\Api\Admin\OrderController::class, 'updateStatus']);
        Route::get('statistics/products-profit', [\App\Http\Controllers\Api\Admin\StatisticsController::class, 'productsProfit']);
    });

    Route::middleware('admin.permission:' . AdminPermissions::MANAGE_SYSTEM)->group(function () {
        // Admin statistics
        Route::get('statistics', [\App\Http\Controllers\Api\Admin\StatisticsController::class, 'index']);
        Route::get('statistics/sales', [\App\Http\Controllers\Api\Admin\StatisticsController::class, 'sales']);
        Route::get('statistics/products', [\App\Http\Controllers\Api\Admin\StatisticsController::class, 'products']);
        Route::get('statistics/product-discovery', [\App\Http\Controllers\Api\Admin\StatisticsController::class, 'productDiscovery']);
        Route::post('statistics/optimize-database', [\App\Http\Controllers\Api\Admin\StatisticsController::class, 'optimizeDatabase']);
        Route::post('statistics/add-indexes', [\App\Http\Controllers\Api\Admin\StatisticsController::class, 'addIndexes']);
        Route::get('statistics/slow-queries', [\App\Http\Controllers\Api\Admin\StatisticsController::class, 'slowQueries']);
    });
});
