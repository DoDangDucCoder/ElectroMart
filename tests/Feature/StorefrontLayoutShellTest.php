<?php

namespace Tests\Feature;

use App\Application\Catalog\Services\ProductService;
use App\Application\Checkout\Services\CartService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertDontSee('shopw-subnav', false)
            ->assertSee('shopw-header-brand', false)
            ->assertSee('shopw-header-center', false)
            ->assertSee('shopw-header-actions', false)
            ->assertSee('shopw-cat-btn-copy', false)
            ->assertSee('data-desktop-target="#shopw-nav-group-categories"', false)
            ->assertDontSee('aria-controls="shopw-mobile-drawer"', false)
            ->assertSee('shopw-utilities--desktop', false)
            ->assertSee('shopw-utilities--compact', false)
            ->assertSee('shopw-util-label', false)
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
            ->assertDontSee('frontend/js/dark-light-mode.js');
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
            ->assertSee('data-desktop-target="#shopw-nav-group-categories"', false)
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

    protected function createProduct(string $name): Product
    {
        return $this->productService->createProduct([
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
        ]);
    }
}

