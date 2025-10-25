<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Video;
use App\Models\Customer;
use App\Models\Post;
use App\Models\Order;
use App\Models\Icons;
use App\Models\Contact;
use Illuminate\Support\ServiceProvider;
use App\Domain\Catalog\Repositories\CategoryRepositoryInterface;
use App\Domain\Catalog\Repositories\BrandRepositoryInterface;
use App\Application\Shared\Services\CacheService;
use App\Application\Shared\Services\DatabaseOptimizationService;
use App\Application\Shared\Services\QueryOptimizationService;
use App\Application\Shared\Services\EnhancedCacheService;
use App\Application\Shared\Services\QueryMonitoringService;
use App\Application\Shared\Services\EagerLoadingService;
use App\Application\Shared\Services\CacheWarmingService;
use App\Application\Shared\Services\PerformanceTestingService;
use App\Application\Shared\Services\AuthenticationSecurityService;
use App\Application\Shared\Services\AuthorizationSecurityService;
use App\Application\Shared\Services\DataEncryptionService;
use App\Application\Shared\Services\SecurityMonitoringService;
use App\Application\Shared\Services\APISecurityService;
use App\Application\Shared\Services\FrontendOptimizationService;
use App\Application\Shared\Services\ResponsiveDesignService;
use App\Application\Shared\Services\UserExperienceEnhancementService;
use App\Application\Shared\Services\ModernUIComponentsService;
use App\Application\Shared\Services\APITestingService;
use App\Application\Shared\Services\DatabaseTestingService;
use App\Application\Shared\Services\SecurityTestingService;
use App\Application\Shared\Services\BrowserAutomationTestingService;
use App\Application\Shared\Services\UserJourneyTestingService;
use App\Application\Shared\Services\CrossBrowserCompatibilityTestingService;
use App\Application\Shared\Services\MonitoringLoggingService;
use App\Application\Shared\Services\BackupRecoveryService;
use App\Domain\Catalog\Repositories\ProductRepositoryInterface;
use App\Domain\Catalog\Repositories\ProductReviewRepositoryInterface;
use App\Domain\Checkout\Repositories\CouponRepositoryInterface;
use App\Domain\Checkout\Repositories\ShippingFeeRepositoryInterface;
use App\Domain\Cms\Repositories\BlogCategoryRepositoryInterface;
use App\Domain\Cms\Repositories\BlogPostRepositoryInterface;
use App\Infrastructure\Persistence\Repositories\Catalog\EloquentBrandRepository;
use App\Infrastructure\Persistence\Repositories\Catalog\EloquentCategoryRepository;
use App\Infrastructure\Persistence\Repositories\Catalog\EloquentProductRepository;
use App\Infrastructure\Persistence\Repositories\Catalog\EloquentProductReviewRepository;
use App\Infrastructure\Persistence\Repositories\Checkout\EloquentCouponRepository;
use App\Infrastructure\Persistence\Repositories\Checkout\EloquentShippingFeeRepository;
use App\Infrastructure\Persistence\Repositories\Cms\EloquentBlogCategoryRepository;
use App\Infrastructure\Persistence\Repositories\Cms\EloquentBlogPostRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class, EloquentCategoryRepository::class);
        $this->app->bind(BrandRepositoryInterface::class, EloquentBrandRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(ProductReviewRepositoryInterface::class, EloquentProductReviewRepository::class);
        $this->app->bind(CouponRepositoryInterface::class, EloquentCouponRepository::class);
        $this->app->bind(ShippingFeeRepositoryInterface::class, EloquentShippingFeeRepository::class);
        $this->app->bind(BlogCategoryRepositoryInterface::class, EloquentBlogCategoryRepository::class);
        $this->app->bind(BlogPostRepositoryInterface::class, EloquentBlogPostRepository::class);
        
                    // Bind shared services
                    $this->app->singleton(CacheService::class);
                    $this->app->singleton(DatabaseOptimizationService::class);
                    $this->app->singleton(QueryOptimizationService::class);
                    $this->app->singleton(EnhancedCacheService::class);
                    $this->app->singleton(QueryMonitoringService::class);
                    $this->app->singleton(EagerLoadingService::class);
                    $this->app->singleton(CacheWarmingService::class);
                    $this->app->singleton(PerformanceTestingService::class);
                    $this->app->singleton(AuthenticationSecurityService::class);
                    $this->app->singleton(AuthorizationSecurityService::class);
                    $this->app->singleton(DataEncryptionService::class);
                    $this->app->singleton(SecurityMonitoringService::class);
                    $this->app->singleton(APISecurityService::class);
                    $this->app->singleton(FrontendOptimizationService::class);
                    $this->app->singleton(ResponsiveDesignService::class);
        $this->app->singleton(UserExperienceEnhancementService::class);
        $this->app->singleton(ModernUIComponentsService::class);
        $this->app->singleton(APITestingService::class);
        $this->app->singleton(DatabaseTestingService::class);
        $this->app->singleton(SecurityTestingService::class);
        $this->app->singleton(BrowserAutomationTestingService::class);
        $this->app->singleton(UserJourneyTestingService::class);
        $this->app->singleton(CrossBrowserCompatibilityTestingService::class);
        $this->app->singleton(MonitoringLoggingService::class);
        $this->app->singleton(BackupRecoveryService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            //get information 
            $post_footer = Post::where('blog_category_id', 7)->get();
            //get information 
            $contact_footer = Contact::where('info_id', 1)->get();
            //get icons social
            $icons = Icons::where('category', 'icons')->orderBy('id_icons', 'DESC')->get();
            //get icons doi tac
            $icons_doitac = Icons::where('category', 'doitac')->orderBy('id_icons', 'DESC')->get();

            $min_price = Product::min('product_price');
            $max_price = Product::max('product_price');

            $min_price_range = $min_price + 500000;
            $max_price_range = $max_price + 10000000;

            $app_product = Product::all()->count();
            $app_post = Post::all()->count();
            $app_order = Order::all()->count();
            $app_video = Video::all()->count();
            $app_customer = Customer::all()->count();
            $share_image = '';

            $view->with('min_price', $min_price)->with('max_price', $max_price)->with('min_price_range', $min_price_range)->with('max_price_range', $max_price_range)->with('app_product', $app_product)->with('app_post', $app_post)->with('app_order', $app_order)->with('app_video', $app_video)->with('app_customer', $app_customer)->with('share_image', $share_image)->with('icons', $icons)->with('icons_doitac', $icons_doitac)->with('contact_footer', $contact_footer)->with('post_footer', $post_footer);
        });
    }
}
