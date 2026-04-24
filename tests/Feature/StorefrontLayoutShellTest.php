<?php

namespace Tests\Feature;

use App\Application\Catalog\Services\ProductService;
use App\Application\Checkout\Services\CartService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Models\Slider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class StorefrontLayoutShellTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $productService;
    protected CartService $cartService;
    protected ProductCategory $category;
    protected Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->productService = $this->app->make(ProductService::class);
        $this->cartService = $this->app->make(CartService::class);

        $this->category = ProductCategory::create([
            'name' => 'Layout Shell Category',
            'slug' => 'layout-shell-category',
            'description' => 'Category for storefront shell tests',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ]);

        $this->brand = Brand::create([
            'name' => 'Layout Shell Brand',
            'slug' => 'layout-shell-brand',
            'description' => 'Brand for storefront shell tests',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ]);
    }

    public function test_home_route_renders_storefront_shell_stack(): void
    {
        $this->createProduct('Shell Home Product');

        $this->get('/')
            ->assertOk()
            ->assertSee('<meta name="csrf-token"', false)
            ->assertSee('frontend/css/shopw-theme.css')
            ->assertDontSee('theme-toggle-container')
            ->assertDontSee('Storefront công nghệ')
            ->assertSee('class="shopw-topbar shopw-topbar--compact"', false)
            ->assertSee('class="shopw-header"', false)
            ->assertDontSee('class="header_top"', false)
            ->assertDontSee('social-icons', false)
            ->assertSee('ShopW')
            ->assertSee('shopw-logo-copy', false)
            ->assertSee('shopw-search-hints', false)
            ->assertSee('shopw-search-hints--compact', false)
            ->assertSee('Bạn cần gì?')
            ->assertSee('shopw-search-rotator', false)
            ->assertDontSee('shopw-subnav', false)
            ->assertSee('shopw-header-brand', false)
            ->assertSee('shopw-header-center', false)
            ->assertSee('shopw-header-actions', false)
            ->assertSee('shopw-cat-btn-copy', false)
            ->assertSee('shopw-header-category-shell', false)
            ->assertSee('shopw-desktop-category-panel', false)
            ->assertSee('shopw-desktop-category-nav', false)
            ->assertSee('shopw-desktop-category-trigger', false)
            ->assertSee('shopw-desktop-category-pane', false)
            ->assertSee('shopw-desktop-category-highlights', false)
            ->assertSee('shopw-desktop-category-section', false)
            ->assertSee('shopw-desktop-category-rail', false)
            ->assertSee('shopw-desktop-category-section-link-icon', false)
            ->assertSee('shopw-desktop-category-promo-icon', false)
            ->assertSee('shopw-desktop-category-promo-stats', false)
            ->assertSee('Thương hiệu nổi bật')
            ->assertSee('Theo tầm giá')
            ->assertSee('start_price=', false)
            ->assertDontSee('price_min=', false)
            ->assertSee('id="keywords"', false)
            ->assertDontSee('placeholder="Tìm sản phẩm, thương hiệu, cấu hình..."', false)
            ->assertSee('shopw-featured-showcase-shell', false)
            ->assertSee('shopw-featured-showcase-card', false)
            ->assertSee('data-search-trends=', false)
            ->assertSee('data-desktop-panel="#shopw-desktop-category-panel"', false)
            ->assertDontSee('aria-controls="shopw-mobile-drawer"', false)
            ->assertSee('data-storefront-nav-parent="categories"', false)
            ->assertSee('data-storefront-nav-parent="blogs"', false)
            ->assertSee('shopw-utilities--desktop', false)
            ->assertSee('shopw-utilities--compact', false)
            ->assertSee('shopw-util-label', false)
            ->assertSee('Đăng nhập')
            ->assertSee('redirect_to=', false)
            ->assertDontSee('shopw-util-minor', false)
            ->assertDontSee('shopw-util-text', false)
            ->assertSee('id="sosanh"', false)
            ->assertSee('So sánh sản phẩm')
            ->assertSee('Sản phẩm')
            ->assertSee('shopw-mobile-quicklink-arrow', false)
            ->assertSee('frontend/js/jquery.js')
            ->assertSee('function loadStorefrontFragment', false)
            ->assertSee('data-chatbot-widget', false)
            ->assertSee('shopw-chatbot-toggle-tail', false)
            ->assertSee('Chat hỗ trợ sản phẩm')
            ->assertSee('shopw-footer-shell', false)
            ->assertSee('shopw-footer-brand-card', false)
            ->assertSee('shopw-footer-newsletter-card', false)
            ->assertSee('shopw-footer-bottom', false)
            ->assertSee('function escapeHtml', false)
            ->assertSee('function add_compare', false)
            ->assertDontSee('frontend/js/dark-light-mode.js')
            ->assertDontSee('href="tel:tel:', false);

        $homeMarkup = $this->get('/')->getContent();
        $this->assertDoesNotMatchRegularExpression(
            '/<details class="shopw-account-menu"[^>]*>/',
            $homeMarkup
        );
        $this->assertDoesNotMatchRegularExpression(
            '/<a[^>]*data-storefront-nav-parent="categories"[^>]*href="#"/',
            $homeMarkup
        );
        $this->assertDoesNotMatchRegularExpression(
            '/<a[^>]*data-storefront-nav-parent="blogs"[^>]*href="#"/',
            $homeMarkup
        );
        $this->assertMatchesRegularExpression(
            '/<div class="shopw-search-shell">[\s\S]*data-search-rotator[\s\S]*<form[^>]*class="shopw-search"/',
            $homeMarkup
        );
    }

    public function test_login_route_reuses_same_storefront_shell(): void
    {
        $product = $this->createProduct('Shell Login Product');
        $this->cartService->addItem($product->product_id, 1);

        $this->get('/dang-nhap')
            ->assertOk()
            ->assertSee('g-login-checkout')
            ->assertSee('class="shopw-topbar shopw-topbar--compact"', false)
            ->assertDontSee('class="header_top"', false)
            ->assertDontSee('social-icons', false)
            ->assertDontSee('Storefront công nghệ')
            ->assertSee('ShopW')
            ->assertSee('shopw-logo-copy', false)
            ->assertDontSee('theme-toggle-container')
            ->assertSee('shopw-mobile-drawer', false)
            ->assertSee('shopw-mobile-quicklink-arrow', false)
            ->assertSee('shopw-search-hints', false)
            ->assertSee('shopw-search-hints--compact', false)
            ->assertDontSee('shopw-subnav', false)
            ->assertSee('shopw-header-brand', false)
            ->assertSee('shopw-header-center', false)
            ->assertSee('shopw-header-actions', false)
            ->assertSee('shopw-cat-btn-copy', false)
            ->assertSee('shopw-header-category-shell', false)
            ->assertSee('shopw-desktop-category-panel', false)
            ->assertSee('shopw-desktop-category-nav', false)
            ->assertSee('shopw-desktop-category-trigger', false)
            ->assertSee('shopw-desktop-category-pane', false)
            ->assertSee('shopw-desktop-category-section', false)
            ->assertSee('shopw-desktop-category-rail', false)
            ->assertSee('shopw-desktop-category-section-link-icon', false)
            ->assertSee('shopw-desktop-category-promo-icon', false)
            ->assertSee('id="keywords"', false)
            ->assertDontSee('placeholder="Tìm sản phẩm, thương hiệu, cấu hình..."', false)
            ->assertSee('data-search-trends=', false)
            ->assertSee('data-desktop-panel="#shopw-desktop-category-panel"', false)
            ->assertDontSee('aria-controls="shopw-mobile-drawer"', false)
            ->assertSee('shopw-utilities--desktop', false)
            ->assertSee('shopw-utilities--compact', false)
            ->assertSee('shopw-util-label', false)
            ->assertDontSee('shopw-util-minor', false)
            ->assertDontSee('shopw-util-text', false)
            ->assertSee('shopw-footer-shell', false)
            ->assertSee('shopw-footer-brand-card', false)
            ->assertSee('shopw-footer-newsletter-card', false)
            ->assertSee('shopw-footer-bottom', false)
            ->assertSee('id="sosanh"', false)
            ->assertSee('frontend/css/shopw-theme.css')
            ->assertSee('frontend/js/jquery.js')
            ->assertSee('function requestQuickview', false)
            ->assertSee('function add_wistlist', false)
            ->assertDontSee('frontend/js/dark-light-mode.js');
    }

    public function test_register_route_reuses_same_storefront_shell(): void
    {
        $product = $this->createProduct('Shell Register Product');
        $this->cartService->addItem($product->product_id, 1);

        $this->get('/dang-ky')
            ->assertOk()
            ->assertSee('g-login-checkout')
            ->assertSee('class="shopw-topbar shopw-topbar--compact"', false)
            ->assertSee('shopw-header-brand', false)
            ->assertSee('shopw-header-center', false)
            ->assertSee('shopw-header-actions', false)
            ->assertSee('shopw-utilities--desktop', false)
            ->assertSee('shopw-footer-shell', false)
            ->assertSee('frontend/css/shopw-theme.css')
            ->assertSee('frontend/js/jquery.js');
    }

    public function test_logged_in_customer_sees_account_dropdown_in_storefront_header(): void
    {
        $product = $this->createProduct('Shell Logged Header Product');
        $customer = Customer::create([
            'name' => 'Header Customer',
            'email' => 'header-customer@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0977000000',
            'is_active' => true,
        ]);

        $this->cartService->addItem($product->product_id, 1);

        $this->withSession([
            'customer_id' => $customer->id,
        ])->get('/')
            ->assertOk()
            ->assertSee('shopw-account-menu', false)
            ->assertSee('shopw-account-summary--avatar-only', false)
            ->assertDontSee('shopw-account-caret', false)
            ->assertSee('frontend/images/storefront-account-avatar.svg', false)
            ->assertSee('Header Customer')
            ->assertSee('Đơn hàng của tôi')
            ->assertSee('Đăng xuất')
            ->assertSee('/dang-xuat-khach-hang?redirect=login', false);
    }

    public function test_category_route_marks_storefront_navigation_state_with_real_links(): void
    {
        $this->createProduct('Shell Category Product');

        $response = $this->get('/danh-muc/' . $this->category->slug);

        $response
            ->assertOk()
            ->assertSee('data-storefront-nav-parent="categories"', false)
            ->assertSee('data-storefront-nav-parent="blogs"', false)
            ->assertDontSee('href="tel:tel:', false);

        $this->assertMatchesRegularExpression(
            '/<a[^>]*data-storefront-nav-parent="categories"[^>]*aria-current="page"/',
            $response->getContent()
        );
        $this->assertDoesNotMatchRegularExpression(
            '/<a[^>]*data-storefront-nav-parent="categories"[^>]*href="#"/',
            $response->getContent()
        );
    }

    public function test_storefront_header_price_links_use_discounted_final_price(): void
    {
        $this->createProduct('Shell Discounted Price Product', [
            'price' => 22000000,
            'sale_type' => 'fixed',
            'sale_value' => 7000000,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('start_price=15000000', false)
            ->assertDontSee('start_price=22000000', false);
    }

    public function test_storefront_header_renders_after_view_recompile_without_sale_price_column(): void
    {
        $this->createProduct('Shell Schema Fallback Product', [
            'price' => 22000000,
            'sale_type' => 'fixed',
            'sale_value' => 7000000,
        ]);

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sale_price');
        });

        Artisan::call('view:clear');

        $this->get('/')
            ->assertOk()
            ->assertSee('start_price=22000000', false)
            ->assertDontSee('start_price=15000000', false);

        $this->get('/')
            ->assertOk()
            ->assertSee('start_price=22000000', false)
            ->assertDontSee('start_price=15000000', false);
    }

    public function test_storefront_homepage_renders_when_sale_value_column_is_missing(): void
    {
        $this->createProduct('Shell Sale Value Schema Fallback Product', [
            'price' => 22000000,
            'sale_type' => 'fixed',
            'sale_value' => 7000000,
        ]);

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sale_value');
        });

        Artisan::call('view:clear');

        $this->get('/')
            ->assertOk();

        $this->get('/')
            ->assertOk();
    }

    public function test_home_route_renders_when_active_slider_exists(): void
    {
        $this->createProduct('Shell Slider Target Product');
        $this->ensureSliderTableExists();
        $sliderDirectory = public_path('uploads/slider');
        $sliderImage = 'shell-slider-test.jpg';
        $sliderPath = $sliderDirectory . DIRECTORY_SEPARATOR . $sliderImage;

        File::ensureDirectoryExists($sliderDirectory);
        File::put($sliderPath, 'slider');

        try {
            Slider::create([
                'slider_name' => 'Shell Hero Slider',
                'slider_alt' => 'Banner test có dữ liệu',
                'slider_image' => $sliderImage,
                'slider_status' => 1,
                'slider_desc' => 'Slider này dùng để kiểm tra scope biến trong view.',
                'slider_tone' => 'mobile',
            ]);

            $this->get('/')
                ->assertOk();
        } finally {
            File::delete($sliderPath);
        }
    }

    public function test_brand_route_renders_when_legacy_slider_table_is_missing(): void
    {
        $product = $this->createProduct('Shell Brand Product');
        Schema::dropIfExists('tbl_slider');

        $this->get('/thuong-hieu/' . $this->brand->slug)
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee($this->brand->name);
    }

    protected function createProduct(string $name, array $overrides = []): Product
    {
        return $this->productService->createProduct(array_merge([
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'brand_id' => $this->brand->id,
            'primary_category_id' => $this->category->id,
            'price' => 13500000,
            'cost_price' => 10000000,
            'stock_quantity' => 12,
            'description' => '<p>Mô tả ' . $name . '</p>',
            'content' => '<p>Nội dung ' . $name . '</p>',
            'category_ids' => [$this->category->id],
            'tags' => 'layout,shell',
            'is_active' => true,
        ], $overrides));
    }

    protected function ensureSliderTableExists(): void
    {
        if (Schema::hasTable('tbl_slider')) {
            return;
        }

        Schema::create('tbl_slider', function (Blueprint $table) {
            $table->increments('slider_id');
            $table->string('slider_name')->nullable();
            $table->string('slider_alt')->nullable();
            $table->string('slider_image')->nullable();
            $table->integer('slider_status')->default(0);
            $table->text('slider_desc')->nullable();
            $table->string('slider_tone', 30)->nullable();
        });
    }
}

