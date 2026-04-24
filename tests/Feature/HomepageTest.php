<?php

namespace Tests\Feature;

use App\Application\Catalog\Services\ProductService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $productService;
    protected ProductCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->productService = $this->app->make(ProductService::class);
        $this->category = $this->createCategory();
        $this->createProducts(7, $this->category);
        $this->createBrandHighlights(3);
    }

    public function test_home_routes_render_live_home_sections(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('id="slider-carousel"', false)
            ->assertSee('data-hero-layout="wide"', false)
            ->assertSee('data-hero-rotate="carousel"', false)
            ->assertDontSee('data-hero-rotate="swap"', false)
            ->assertSee('shopw-hero-slide', false)
            ->assertSee('shopw-hero-slide-copy', false)
            ->assertSee('shopw-hero-slide-meta', false)
            ->assertSee('shopw-hero-support', false)
            ->assertSee('shopw-hero-support--dock', false)
            ->assertSee('data-support-placement="fixed-right"', false)
            ->assertSee('data-support-rail="icon-first"', false)
            ->assertSee('shopw-hero-support-cta', false)
            ->assertSee('shopw-home-discovery-grid', false)
            ->assertSee('shopw-home-discovery-grid--solo', false)
            ->assertSee('shopw-home-campaign-grid', false)
            ->assertSee('shopw-home-campaign-grid--solo', false)
            ->assertSee('shopw-home-ecosystem-grid', false)
            ->assertSee('shopw-home-brand-grid', false)
            ->assertSee('shopw-home-service-grid', false)
            ->assertSee('shopw-home-content-list', false)
            ->assertSee('shopw-flash-sale-shell', false)
            ->assertSee('data-flash-sale-carousel', false)
            ->assertSee('data-flash-nav="next"', false)
            ->assertSee('shopw-flash-sale-track', false)
            ->assertSee('Chat Facebook')
            ->assertSee('Chat Zalo')
            ->assertSee('1900.0000')
            ->assertSee('shopw-hero-slide--', false)
            ->assertDontSee('shopw-hero-ad', false)
            ->assertDontSee('shopw-hero-banners', false)
            ->assertDontSee('shopw-hero-tiles', false)
            ->assertDontSee('js-hero-slot', false)
            ->assertDontSee('shopw-mega', false)
            ->assertSee('id="tabs_product"', false)
            ->assertSee('id="all_product"', false)
            ->assertSee('Sản phẩm nên xem tiếp')
            ->assertSee('shopw-home-recommendation-grid--', false)
            ->assertSee('shopw-mobile-drawer', false)
            ->assertSee('shopw-mobile-quicklinks', false)
            ->assertSee('shopw-topbar--compact', false)
            ->assertDontSee('class="header_top"', false)
            ->assertDontSee('social-icons', false)
            ->assertSee('shopw-search-hints', false)
            ->assertSee('shopw-search-hints--compact', false)
            ->assertDontSee('shopw-subnav', false)
            ->assertSee('shopw-header-brand', false)
            ->assertSee('shopw-header-center', false)
            ->assertSee('shopw-header-actions', false)
            ->assertSee('shopw-utilities--desktop', false)
            ->assertSee('shopw-utilities--compact', false)
            ->assertSee('shopw-util-label', false)
            ->assertDontSee('shopw-util-minor', false)
            ->assertDontSee('shopw-util-text', false)
            ->assertDontSee('shopw-favorite', false)
            ->assertDontSee('shopw-checkout', false)
            ->assertDontSee('shopw-order', false)
            ->assertSee('/danh-muc/homepage-category')
            ->assertSee('/video-shop')
            ->assertDontSee('/danh-muc/laptop-gaming')
            ->assertDontSee('/danh-muc-bai-viet/tin-tuc-cong-nghe')
            ->assertSee('shopw-home-ecosystem-grid--with-brand', false);

        $this->get('/trang-chu')
            ->assertOk()
            ->assertSee('id="slider-carousel"', false)
            ->assertSee('data-hero-layout="wide"', false)
            ->assertSee('data-hero-rotate="carousel"', false)
            ->assertDontSee('data-hero-rotate="swap"', false)
            ->assertSee('shopw-hero-slide', false)
            ->assertSee('shopw-hero-slide-copy', false)
            ->assertSee('shopw-hero-slide-meta', false)
            ->assertSee('shopw-hero-support', false)
            ->assertSee('shopw-hero-support--dock', false)
            ->assertSee('data-support-placement="fixed-right"', false)
            ->assertSee('data-support-rail="icon-first"', false)
            ->assertSee('shopw-hero-support-cta', false)
            ->assertSee('shopw-home-discovery-grid', false)
            ->assertSee('shopw-home-discovery-grid--solo', false)
            ->assertSee('shopw-home-campaign-grid', false)
            ->assertSee('shopw-home-campaign-grid--solo', false)
            ->assertSee('shopw-home-ecosystem-grid', false)
            ->assertSee('shopw-home-brand-grid', false)
            ->assertSee('shopw-home-service-grid', false)
            ->assertSee('shopw-home-content-list', false)
            ->assertSee('Chat Facebook')
            ->assertSee('Chat Zalo')
            ->assertSee('1900.0000')
            ->assertSee('shopw-hero-slide--', false)
            ->assertDontSee('shopw-hero-ad', false)
            ->assertDontSee('shopw-hero-banners', false)
            ->assertDontSee('shopw-hero-tiles', false)
            ->assertDontSee('js-hero-slot', false)
            ->assertDontSee('shopw-mega', false)
            ->assertSee('id="tabs_product"', false)
            ->assertSee('id="all_product"', false)
            ->assertSee('Sản phẩm nên xem tiếp')
            ->assertSee('shopw-home-recommendation-grid--', false)
            ->assertSee('shopw-mobile-drawer', false)
            ->assertSee('shopw-mobile-quicklinks', false)
            ->assertSee('shopw-topbar--compact', false)
            ->assertDontSee('class="header_top"', false)
            ->assertDontSee('social-icons', false)
            ->assertSee('shopw-search-hints', false)
            ->assertSee('shopw-search-hints--compact', false)
            ->assertDontSee('shopw-subnav', false)
            ->assertSee('shopw-header-brand', false)
            ->assertSee('shopw-header-center', false)
            ->assertSee('shopw-header-actions', false)
            ->assertSee('shopw-utilities--desktop', false)
            ->assertSee('shopw-utilities--compact', false)
            ->assertSee('shopw-util-label', false)
            ->assertDontSee('shopw-util-minor', false)
            ->assertDontSee('shopw-util-text', false)
            ->assertDontSee('shopw-favorite', false)
            ->assertDontSee('shopw-checkout', false)
            ->assertDontSee('shopw-order', false)
            ->assertSee('/danh-muc/homepage-category')
            ->assertSee('/video-shop')
            ->assertDontSee('/danh-muc/laptop-gaming')
            ->assertDontSee('/danh-muc-bai-viet/tin-tuc-cong-nghe')
            ->assertSee('shopw-home-ecosystem-grid--with-brand', false);
    }

    public function test_homepage_collapses_brand_panel_when_brand_data_is_insufficient(): void
    {
        Brand::query()->update(['is_active' => false]);

        $this->get('/')
            ->assertOk()
            ->assertSee('shopw-home-ecosystem-grid--without-brand', false)
            ->assertDontSee('shopw-home-brand-cloud', false)
            ->assertDontSee('shopw-home-brand-grid', false)
            ->assertSee('shopw-home-service-grid', false)
            ->assertSee('shopw-home-content-list', false);
    }

    public function test_homepage_recommendation_fallback_renders_when_recommendations_are_unavailable(): void
    {
        Product::query()->delete();
        Brand::query()->delete();

        $this->get('/')
            ->assertOk()
            ->assertSee('shopw-home-recommendation-fallback', false)
            ->assertSee('Tìm sản phẩm phù hợp')
            ->assertSee('/tim-kiem')
            ->assertSee('/so-sanh')
            ->assertSee('/video-shop');
    }

    public function test_homepage_renders_flash_sale_when_curated_products_are_configured(): void
    {
        $flashBrand = Brand::create([
            'name' => 'Flash Brand',
            'slug' => 'flash-brand',
            'description' => 'Brand for flash sale test',
            'is_active' => true,
            'display_order' => 10,
            'meta' => null,
        ]);

        $flashOne = $this->productService->createProduct([
            'name' => 'Flash Sale Product One',
            'slug' => 'flash-sale-product-one',
            'brand_id' => $flashBrand->id,
            'primary_category_id' => $this->category->id,
            'price' => 24000000,
            'sale_price' => 21990000,
            'cost_price' => 18000000,
            'stock_quantity' => 10,
            'description' => 'Flash sale product one',
            'content' => 'Flash sale product one',
            'category_ids' => [$this->category->id],
            'is_active' => true,
        ]);

        $flashTwo = $this->productService->createProduct([
            'name' => 'Flash Sale Product Two',
            'slug' => 'flash-sale-product-two',
            'brand_id' => $flashBrand->id,
            'primary_category_id' => $this->category->id,
            'price' => 18000000,
            'sale_price' => 15990000,
            'cost_price' => 12000000,
            'stock_quantity' => 8,
            'description' => 'Flash sale product two',
            'content' => 'Flash sale product two',
            'category_ids' => [$this->category->id],
            'is_active' => true,
        ]);

        config()->set('storefront.flash_sale', [
            'enabled' => true,
            'kicker' => 'Hot deal',
            'title' => 'Flash Sale',
            'cta_label' => 'Xem tất cả deal',
            'cta_url' => '/tim-kiem',
            'countdown_ends_at' => now()->addDay()->toDateTimeString(),
            'product_slugs' => [$flashOne->slug, $flashTwo->slug],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('shopw-flash-sale-shell', false)
            ->assertSee('data-flash-sale-countdown=', false)
            ->assertSee('data-flash-nav="prev"', false)
            ->assertSee('Flash Sale Product One')
            ->assertSee('Flash Sale Product Two')
            ->assertSee('shopw-flash-sale-card', false)
            ->assertSee('Xem tất cả deal');
    }

    public function test_homepage_renders_demo_flash_sale_when_curated_products_are_missing(): void
    {
        config()->set('storefront.flash_sale', [
            'enabled' => true,
            'kicker' => 'Flash',
            'title' => 'Sale',
            'cta_label' => 'Xem tất cả',
            'cta_url' => '/tim-kiem',
            'countdown_ends_at' => now()->addDay()->toDateTimeString(),
            'product_slugs' => ['missing-flash-sale-product'],
            'target_count' => 8,
            'use_demo_products' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('shopw-flash-sale-shell', false)
            ->assertSee('shopw-flash-sale-card', false)
            ->assertSee('Ghế xếp cắm trại Camping Mountain')
            ->assertSee('Áo ống trắng nữ', false)
            ->assertSee('Xem tất cả');
    }

    public function test_product_tabs_returns_home_cards_fragment(): void
    {
        $this->post('/product-tabs', [
            'cate_id' => $this->category->id,
        ])->assertOk()
            ->assertSee('Xem nhanh')
            ->assertSee('home_cart_')
            ->assertSee('Test Product 7');
    }

    public function test_product_tabs_marks_single_product_as_featured_layout(): void
    {
        \App\Infrastructure\Persistence\Eloquent\Catalog\Product::query()->delete();
        Brand::query()->delete();
        $this->createProducts(1, $this->category);

        $this->post('/product-tabs', [
            'cate_id' => $this->category->id,
        ])->assertOk()
            ->assertSee('shopw-home-tab-products-single')
            ->assertSee('shopw-home-featured-card')
            ->assertSee('Test Product 1');
    }

    public function test_product_tabs_validates_invalid_category_input(): void
    {
        $this->postJson('/product-tabs', [
            'cate_id' => 'invalid',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['cate_id']);
    }

    public function test_product_tabs_renders_shared_empty_state_for_category_without_products(): void
    {
        $emptyCategory = ProductCategory::create([
            'name' => 'Empty Homepage Category',
            'slug' => 'empty-homepage-category',
            'description' => 'Empty category for homepage tabs',
            'is_active' => true,
            'display_order' => 1,
            'meta' => null,
        ]);

        $this->post('/product-tabs', [
            'cate_id' => $emptyCategory->id,
        ])->assertOk()
            ->assertSee('shopw-empty-state', false)
            ->assertSee('Hiện chưa có sản phẩm trong danh mục này.');
    }

    public function test_load_more_products_returns_cards_and_load_more_button(): void
    {
        $this->post('/load-more-product')->assertOk()
            ->assertSee('Xem nhanh')
            ->assertSee('id="load_more_button"', false)
            ->assertSee('Test Product 7');
    }

    public function test_load_more_products_marks_single_product_as_featured_card(): void
    {
        \App\Infrastructure\Persistence\Eloquent\Catalog\Product::query()->delete();
        Brand::query()->delete();
        $this->createProducts(1, $this->category);

        $this->post('/load-more-product')->assertOk()
            ->assertSee('shopw-home-featured-card')
            ->assertSee('Test Product 1');
    }

    public function test_load_more_products_returns_exhausted_state_when_no_more_items(): void
    {
        $smallestId = \App\Infrastructure\Persistence\Eloquent\Catalog\Product::query()->min('id');

        $this->post('/load-more-product', [
            'id' => $smallestId,
        ])->assertOk()
            ->assertSee('shopw-load-more-end');
    }

    public function test_load_more_products_validates_invalid_cursor(): void
    {
        $this->postJson('/load-more-product', [
            'id' => 'invalid',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['id']);
    }

    public function test_autocomplete_ajax_returns_results_fragment_and_validates_payload(): void
    {
        $this->post('/autocomplete-ajax', [
            'query' => 'Test Product',
        ])->assertOk()
            ->assertSee('dropdown-menu')
            ->assertSee('shopw-search-suggest-layout', false)
            ->assertSee('shopw-search-suggest-footer', false)
            ->assertSee('Test Product 7');

        $this->post('/autocomplete-ajax', [
            'query' => '',
        ])->assertOk()
            ->assertSee('Từ khóa nổi bật')
            ->assertSee('shopw-search-suggest-chip', false);

        $this->postJson('/autocomplete-ajax', [
            'query' => str_repeat('a', 101),
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['query']);
    }

    protected function createCategory(): ProductCategory
    {
        return ProductCategory::create([
            'name' => 'Homepage Category',
            'slug' => 'homepage-category',
            'description' => 'Homepage category description',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ]);
    }

    protected function createProducts(int $count, ProductCategory $category): void
    {
        $brand = Brand::create([
            'name' => 'Homepage Brand',
            'slug' => 'homepage-brand',
            'description' => 'Homepage brand description',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ]);

        for ($i = 1; $i <= $count; $i++) {
            $this->productService->createProduct([
                'name' => 'Test Product ' . $i,
                'slug' => 'homepage-product-' . $i . '-' . Str::random(4),
                'brand_id' => $brand->id,
                'primary_category_id' => $category->id,
                'price' => 100000 + ($i * 10000),
                'cost_price' => 80000 + ($i * 5000),
                'stock_quantity' => 20,
                'description' => 'Homepage product description ' . $i,
                'content' => 'Homepage product content ' . $i,
                'category_ids' => [$category->id],
                'tags' => 'home,test',
                'is_active' => true,
            ]);
        }
    }

    protected function createBrandHighlights(int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            Brand::create([
                'name' => 'Homepage Highlight Brand ' . $i,
                'slug' => 'homepage-highlight-brand-' . $i,
                'description' => 'Homepage highlight brand ' . $i,
                'is_active' => true,
                'display_order' => $i,
                'meta' => null,
            ]);
        }
    }
}

