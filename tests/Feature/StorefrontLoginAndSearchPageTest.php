<?php

namespace Tests\Feature;

use App\Application\Catalog\Services\ProductService;
use App\Application\Checkout\Services\CartService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StorefrontLoginAndSearchPageTest extends TestCase
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
            'name' => 'Storefront Auth Category',
            'slug' => 'storefront-auth-category',
            'description' => 'Category for storefront auth/search tests',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ]);

        $this->brand = Brand::create([
            'name' => 'Storefront Auth Brand',
            'slug' => 'storefront-auth-brand',
            'description' => 'Brand for storefront auth/search tests',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ]);
    }

    public function test_login_checkout_page_renders_live_auth_layout(): void
    {
        $product = $this->createProduct('Gaming Search Laptop');
        $this->cartService->addItem($product->product_id, 2);

        $this->get('/dang-nhap')
            ->assertOk()
            ->assertSee('g-login-checkout')
            ->assertSee('shopw-auth-flow', false)
            ->assertSee('shopw-auth-assurance-grid', false)
            ->assertSee('shopw-auth-support-strip', false)
            ->assertSee('Đăng nhập để tiếp tục thanh toán')
            ->assertSee('name="email_account"', false)
            ->assertSee('name="customer_name"', false)
            ->assertSee('Đăng nhập bằng Google');
    }

    public function test_logged_in_customer_sees_continue_checkout_state_on_login_page(): void
    {
        $customer = Customer::create([
            'name' => 'Logged Customer',
            'email' => 'logged@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '0912345678',
            'is_active' => true,
        ]);

        $this->withSession([
            'customer_id' => $customer->id,
        ])->get('/dang-nhap?redirect=checkout')
            ->assertOk()
            ->assertSee('Bạn đã đăng nhập thành công')
            ->assertSee('Tài khoản đã xác thực')
            ->assertSee('Tiếp tục thanh toán')
            ->assertSee($customer->email)
            ->assertSee('shopw-auth-switcher', false);
    }

    public function test_search_page_renders_live_result_cards(): void
    {
        $matching = $this->createProduct('Gaming Beast Laptop');
        $this->createProduct('Office Ultrabook');

        $this->post('/tim-kiem', [
            'keywords_submit' => 'Gaming',
        ])->assertOk()
            ->assertSee('g-search')
            ->assertSee('Tìm theo tên, mã, danh mục và khoảng giá')
            ->assertSee('shopw-search-submit-btn', false)
            ->assertSee('shopw-search-grid--single', false)
            ->assertSee('shopw-search-results-body--single', false)
            ->assertSee('shopw-search-featured-card', false)
            ->assertSee('Gaming Beast Laptop')
            ->assertSee('1 sản phẩm đang được hiển thị trong danh sách này.')
            ->assertSee('Tên: Gaming')
            ->assertSee('shopw-recommendation-section', false);
    }

    public function test_storefront_autocomplete_results_render_keyword_markers_for_safe_selection(): void
    {
        $product = $this->createProduct('Gaming Beast Laptop');

        $this->post('/autocomplete-ajax', [
            'query' => 'Gaming',
        ])->assertOk()
            ->assertSee('shopw-search-suggest-layout', false)
            ->assertSee('Sản phẩm phù hợp')
            ->assertSee('data-search-keyword="Gaming Beast Laptop"', false)
            ->assertSee('/chi-tiet/' . $product->product_slug);
    }

    public function test_storefront_autocomplete_no_result_state_keeps_search_cta_visible(): void
    {
        $this->post('/autocomplete-ajax', [
            'query' => 'KhongCoSanPhamNao',
        ])->assertOk()
            ->assertSeeText('Chưa thấy sản phẩm khớp với')
            ->assertSeeText('KhongCoSanPhamNao')
            ->assertSee('shopw-search-suggest-footer', false)
            ->assertSee('/tim-kiem?keywords_submit=KhongCoSanPhamNao');
    }

    public function test_search_page_renders_empty_state_when_no_match(): void
    {
        $this->post('/tim-kiem', [
            'keywords_submit' => 'Nonexistent',
        ])->assertOk()
            ->assertSee('g-search')
            ->assertSee('shopw-search-submit-btn', false)
            ->assertSee('shopw-empty-state', false)
            ->assertSee('Không tìm thấy sản phẩm phù hợp')
            ->assertSee('Tên: Nonexistent')
            ->assertSee('shopw-recommendation-section--fallback', false)
            ->assertSee('shopw-recommendation-fallback', false);
    }

    public function test_search_request_validation_error_is_rendered_back_on_storefront_shell(): void
    {
        $this->from('/')->post('/tim-kiem', [
            'keywords_submit' => str_repeat('a', 101),
        ])->assertRedirect('/');

        $this->followingRedirects()
            ->from('/')
            ->post('/tim-kiem', [
                'keywords_submit' => str_repeat('a', 101),
            ])->assertSee('Từ khóa tìm kiếm không được vượt quá 100 ký tự.');
    }

    protected function createProduct(string $name): Product
    {
        return $this->productService->createProduct([
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'brand_id' => $this->brand->id,
            'primary_category_id' => $this->category->id,
            'price' => 17500000,
            'cost_price' => 14000000,
            'stock_quantity' => 15,
            'description' => '<p>Mô tả ' . $name . '</p>',
            'content' => '<p>Nội dung ' . $name . '</p>',
            'category_ids' => [$this->category->id],
            'tags' => 'search,auth',
            'is_active' => true,
        ]);
    }
}

