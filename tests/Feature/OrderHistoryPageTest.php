<?php

namespace Tests\Feature;

use App\Application\Catalog\Services\ProductService;
use App\Application\Order\Services\LegacyOrderPresentationService;
use App\Models\Customer as LegacyCustomer;
use App\Models\Coupon as LegacyCoupon;
use App\Models\Order as LegacyOrder;
use App\Models\OrderDetails as LegacyOrderDetails;
use App\Models\Product as LegacyProduct;
use App\Models\Shipping as LegacyShipping;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderHistoryPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_guest_is_redirected_from_history(): void
    {
        $this->get('/history')
            ->assertRedirect('dang-nhap');
    }

    public function test_customer_can_view_history_from_modern_orders(): void
    {
        [$customer, $order] = $this->createModernOrder();

        $this->withSession(['customer_id' => $customer->id])
            ->get('/history')
            ->assertOk()
            ->assertSee('g-history')
            ->assertSee('shopw-history-support-strip', false)
            ->assertSee('Quay lại trang chủ để tiếp tục chọn hàng')
            ->assertDontSee('Quay lại storefront để tiếp tục chọn hàng')
            ->assertSee($order->code)
            ->assertSee('Thanh toán khi nhận hàng')
            ->assertSee('Lịch sử đơn hàng');
    }

    public function test_customer_can_view_modern_order_detail(): void
    {
        [$customer, $order] = $this->createModernOrder();

        $this->withSession(['customer_id' => $customer->id])
            ->get('/view-history-order/' . $order->code)
            ->assertOk()
            ->assertSee('g-order-detail')
            ->assertSee('shopw-order-detail-support-strip', false)
            ->assertSee($order->code)
            ->assertSee('History Product')
            ->assertSee('123 Test Street')
            ->assertSee('SAVE20');
    }

    public function test_stale_modern_customer_session_is_redirected_from_history(): void
    {
        $this->withSession(['customer_id' => 999999])
            ->get('/history')
            ->assertRedirect('dang-nhap');
    }

    public function test_customer_can_cancel_pending_modern_order(): void
    {
        [$customer, $order] = $this->createModernOrder();

        $this->withSession(['customer_id' => $customer->id])
            ->post('/huy-don-hang', [
                'order_code' => $order->code,
                'lydo' => 'Changed plans',
            ])
            ->assertOk()
            ->assertJson([
                'message' => 'Hủy đơn hàng thành công.',
                'order_code' => $order->code,
            ]);

        $order->refresh();

        $this->assertSame('cancelled', $order->status);
        $this->assertSame('Changed plans', data_get($order->meta, 'cancel_reason'));
        $this->assertStringContainsString('Khách hủy đơn: Changed plans', (string) $order->note);
    }

    public function test_guest_cannot_cancel_order(): void
    {
        $this->post('/huy-don-hang', [
            'order_code' => 'ORDER404',
            'lydo' => 'No login',
        ])->assertStatus(401)
            ->assertJson([
                'message' => 'Vui lòng đăng nhập để hủy đơn hàng.',
            ]);
    }

    public function test_stale_modern_customer_session_cannot_cancel_order(): void
    {
        $this->withSession(['customer_id' => 999999])
            ->post('/huy-don-hang', [
                'order_code' => 'ORDER404',
                'lydo' => 'Invalid session',
            ])->assertStatus(401)
            ->assertJson([
                'message' => 'Phiên đăng nhập không còn hợp lệ.',
            ]);
    }

    public function test_customer_cannot_cancel_completed_modern_order(): void
    {
        [$customer, $order] = $this->createModernOrder([
            'status' => 'completed',
        ]);

        $this->withSession(['customer_id' => $customer->id])
            ->post('/huy-don-hang', [
                'order_code' => $order->code,
                'lydo' => 'Too late',
            ])->assertStatus(422)
            ->assertJson([
                'message' => 'Đơn hàng này không thể hủy ở trạng thái hiện tại.',
            ]);
    }

    public function test_customer_cannot_cancel_completed_legacy_order(): void
    {
        $legacyOrder = $this->createLegacyOrder(orderStatus: 2);

        $this->withSession([
            'customer_id' => $legacyOrder->customer_id,
            'customer_name' => 'Legacy History Customer',
        ])->post('/huy-don-hang', [
            'order_code' => $legacyOrder->order_code,
            'lydo' => 'Legacy too late',
            ])->assertStatus(422)
            ->assertJson([
                'message' => 'Đơn hàng này không thể hủy ở trạng thái hiện tại.',
            ]);
    }

    public function test_customer_can_view_legacy_order_detail_with_multi_product_coupon_and_shipping_totals(): void
    {
        $order = $this->createLegacyDetailedOrder();

        $this->withSession([
            'customer_id' => $order->customer_id,
            'customer_name' => 'Legacy History Customer',
        ])->get('/view-history-order/' . $order->order_code)
            ->assertOk()
            ->assertSee('g-order-detail')
            ->assertSee($order->order_code)
            ->assertSee('Legacy History Product')
            ->assertSee('Legacy History Product Two')
            ->assertSee('VIP10')
            ->assertSee('45.000 VND')
            ->assertSee('3.105.000 VND');

        $totals = app(LegacyOrderPresentationService::class)->calculateTotals($order->order_code);
        $this->assertSame(3105000.0, (float) $totals['total']);
        $this->assertSame(340000.0, (float) $totals['discount']);
        $this->assertSame(45000.0, (float) $totals['shipping']);
    }

    public function test_customer_can_view_legacy_history_list_with_fixed_coupon_and_payment_method(): void
    {
        $order = $this->createLegacyDetailedOrder([
            'coupon_code' => 'FIX300',
            'coupon_number' => 300000,
            'coupon_condition' => 2,
        ]);

        $this->withSession([
            'customer_id' => $order->customer_id,
            'customer_name' => 'Legacy History Customer',
        ])->get('/history')
            ->assertOk()
            ->assertSee('g-history')
            ->assertSee($order->order_code)
            ->assertSee('Tiền mặt')
            ->assertSee('3.145.000 VND')
            ->assertSee('Đơn hàng mới');

        $totals = app(LegacyOrderPresentationService::class)->calculateTotals($order->order_code);
        $this->assertSame(3145000.0, (float) $totals['total']);
        $this->assertSame(300000.0, (float) $totals['discount']);
        $this->assertSame(3, (int) $totals['items_count']);
    }

    protected function createModernOrder(array $overrides = []): array
    {
        $customer = Customer::create([
            'name' => 'History Customer',
            'email' => 'history@example.com',
            'phone' => '0900000001',
            'password' => bcrypt('secret123'),
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'name' => 'History Brand',
            'slug' => 'history-brand',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $category = ProductCategory::create([
            'name' => 'History Category',
            'slug' => 'history-category',
            'is_active' => true,
            'display_order' => 0,
        ]);

        $product = $this->app->make(ProductService::class)->createProduct([
            'name' => 'History Product',
            'slug' => 'history-product-' . Str::lower(Str::random(6)),
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
            'price' => 200000,
            'cost_price' => 150000,
            'stock_quantity' => 20,
            'description' => 'History description',
            'content' => 'History content',
            'category_ids' => [$category->id],
            'tags' => 'history',
            'is_active' => true,
            'thumbnail_path' => 'uploads/product/no-image.svg',
        ]);

        $order = Order::create(array_merge([
            'code' => 'HIST' . strtoupper(Str::random(6)),
            'customer_id' => $customer->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'fulfillment_status' => 'unfulfilled',
            'currency' => 'VND',
            'subtotal' => 400000,
            'discount_total' => 20000,
            'shipping_total' => 15000,
            'tax_total' => 0,
            'total' => 395000,
            'coupon_code' => 'SAVE20',
            'coupon_discount' => 20000,
            'source' => 'web',
            'note' => 'Call before delivery',
            'placed_at' => now(),
            'meta' => [
                'shipping_method' => 'cod',
            ],
        ], $overrides));

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => 'History Product',
            'attributes' => ['attribute' => 'Màu đen | 256GB'],
            'price' => 200000,
            'quantity' => 2,
            'subtotal' => 400000,
            'tax_amount' => 0,
            'meta' => [
                'thumbnail' => 'uploads/product/no-image.svg',
            ],
        ]);

        $order->shipping()->create([
            'recipient_name' => 'Receiver History',
            'recipient_phone' => '0900000002',
            'email' => 'receiver@example.com',
            'country' => 'Vietnam',
            'address_line1' => '123 Test Street',
            'address_line2' => 'Ring the bell',
            'shipping_method' => 'cod',
            'shipping_fee' => 15000,
            'insurance_fee' => 0,
        ]);

        $order->payments()->create([
            'provider' => 'cod',
            'status' => 'pending',
            'currency' => 'VND',
            'amount' => 395000,
            'fee' => 0,
        ]);

        return [$customer, $order->fresh(['items.product', 'shipping', 'payments'])];
    }

    protected function createLegacyOrder(int $orderStatus = 1): LegacyOrder
    {
        $this->ensureLegacyOrderTablesExist();

        $customer = LegacyCustomer::create([
            'customer_name' => 'Legacy History Customer',
            'customer_email' => 'legacy-history@example.com',
            'customer_password' => md5('secret123'),
            'customer_phone' => '0900000099',
        ]);

        return LegacyOrder::create([
            'customer_id' => $customer->customer_id,
            'shipping_id' => 1,
            'order_status' => $orderStatus,
            'order_code' => 'LEG' . strtoupper(Str::random(6)),
            'order_destroy' => null,
            'created_at' => now(),
            'order_date' => now()->toDateString(),
        ]);
    }

    protected function createLegacyDetailedOrder(array $couponOverrides = []): LegacyOrder
    {
        $this->ensureLegacyOrderTablesExist();

        $couponPayload = array_merge([
            'coupon_name' => 'Giảm 10%',
            'coupon_code' => 'VIP10',
            'coupon_time' => 10,
            'coupon_number' => 10,
            'coupon_condition' => 1,
            'coupon_date_start' => '01/03/2026',
            'coupon_date_end' => '31/12/2026',
            'coupon_status' => 1,
            'coupon_used' => '',
        ], $couponOverrides);

        LegacyCoupon::create([
            'coupon_name' => $couponPayload['coupon_name'],
            'coupon_code' => $couponPayload['coupon_code'],
            'coupon_time' => $couponPayload['coupon_time'],
            'coupon_number' => $couponPayload['coupon_number'],
            'coupon_condition' => $couponPayload['coupon_condition'],
            'coupon_date_start' => $couponPayload['coupon_date_start'],
            'coupon_date_end' => $couponPayload['coupon_date_end'],
            'coupon_status' => $couponPayload['coupon_status'],
            'coupon_used' => $couponPayload['coupon_used'],
        ]);

        $customer = LegacyCustomer::create([
            'customer_name' => 'Legacy History Customer',
            'customer_email' => 'legacy-history@example.com',
            'customer_password' => md5('secret123'),
            'customer_phone' => '0900000099',
        ]);

        $shipping = LegacyShipping::create([
            'shipping_name' => 'Legacy Receiver',
            'shipping_address' => '123 Legacy Street',
            'shipping_phone' => '0900000011',
            'shipping_email' => 'legacy-receiver@example.com',
            'shipping_notes' => 'Giao giờ hành chính',
            'shipping_method' => 1,
        ]);

        $order = LegacyOrder::create([
            'customer_id' => $customer->customer_id,
            'shipping_id' => $shipping->shipping_id,
            'order_status' => 1,
            'order_code' => 'LEG' . strtoupper(Str::random(6)),
            'order_destroy' => null,
            'created_at' => now(),
            'order_date' => now()->toDateString(),
        ]);

        $firstProduct = new LegacyProduct();
        $firstProduct->forceFill([
            'product_id' => 11,
            'product_name' => 'Legacy History Product',
            'product_slug' => 'legacy-history-product',
            'category_id' => null,
            'brand_id' => null,
            'product_desc' => 'Legacy history description',
            'product_content' => 'Legacy history content',
            'product_price' => 1250000,
            'product_image' => 'no-image.svg',
            'product_status' => 0,
            'product_tags' => 'legacy,history',
            'product_views' => 0,
            'product_quantity' => 20,
            'product_sold' => 0,
            'price_cost' => 980000,
        ]);
        $firstProduct->save();

        $secondProduct = new LegacyProduct();
        $secondProduct->forceFill([
            'product_id' => 12,
            'product_name' => 'Legacy History Product Two',
            'product_slug' => 'legacy-history-product-two',
            'category_id' => null,
            'brand_id' => null,
            'product_desc' => 'Legacy history description 2',
            'product_content' => 'Legacy history content 2',
            'product_price' => 900000,
            'product_image' => 'no-image.svg',
            'product_status' => 0,
            'product_tags' => 'legacy,history,two',
            'product_views' => 0,
            'product_quantity' => 15,
            'product_sold' => 0,
            'price_cost' => 650000,
        ]);
        $secondProduct->save();

        $firstDetail = new LegacyOrderDetails();
        $firstDetail->forceFill([
            'order_code' => $order->order_code,
            'product_id' => 11,
            'product_name' => 'Legacy History Product',
            'product_attribute' => 'Màu đen',
            'product_price' => 1250000,
            'product_sales_quantity' => 2,
            'product_coupon' => $couponPayload['coupon_code'],
            'product_feeship' => 45000,
        ]);
        $firstDetail->save();

        $secondDetail = new LegacyOrderDetails();
        $secondDetail->forceFill([
            'order_code' => $order->order_code,
            'product_id' => 12,
            'product_name' => 'Legacy History Product Two',
            'product_attribute' => 'Màu bạc',
            'product_price' => 900000,
            'product_sales_quantity' => 1,
            'product_coupon' => $couponPayload['coupon_code'],
            'product_feeship' => 45000,
        ]);
        $secondDetail->save();

        return $order;
    }

    protected function ensureLegacyOrderTablesExist(): void
    {
        if (! Schema::hasTable('tbl_customers')) {
            Schema::create('tbl_customers', function (Blueprint $table) {
                $table->increments('customer_id');
                $table->string('customer_name');
                $table->string('customer_email')->nullable();
                $table->string('customer_password')->nullable();
                $table->string('customer_phone')->nullable();
                $table->integer('customer_vip')->nullable();
                $table->string('customer_picture')->nullable();
            });
        }

        if (! Schema::hasTable('tbl_order')) {
            Schema::create('tbl_order', function (Blueprint $table) {
                $table->increments('order_id');
                $table->unsignedInteger('customer_id');
                $table->unsignedInteger('shipping_id')->nullable();
                $table->integer('order_status')->default(1);
                $table->string('order_code');
                $table->string('order_destroy')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->date('order_date')->nullable();
            });
        }

        if (! Schema::hasTable('tbl_shipping')) {
            Schema::create('tbl_shipping', function (Blueprint $table) {
                $table->increments('shipping_id');
                $table->string('shipping_name')->nullable();
                $table->string('shipping_address')->nullable();
                $table->string('shipping_phone')->nullable();
                $table->string('shipping_email')->nullable();
                $table->text('shipping_notes')->nullable();
                $table->integer('shipping_method')->default(1);
            });
        }

        if (! Schema::hasTable('tbl_product')) {
            Schema::create('tbl_product', function (Blueprint $table) {
                $table->increments('product_id');
                $table->string('product_name');
                $table->string('product_slug')->nullable();
                $table->integer('category_id')->nullable();
                $table->integer('brand_id')->nullable();
                $table->text('product_desc')->nullable();
                $table->longText('product_content')->nullable();
                $table->bigInteger('product_price')->default(0);
                $table->string('product_image')->nullable();
                $table->integer('product_status')->default(0);
                $table->string('product_tags')->nullable();
                $table->integer('product_views')->default(0);
                $table->integer('product_quantity')->default(0);
                $table->integer('product_sold')->default(0);
                $table->bigInteger('price_cost')->default(0);
            });
        }

        if (! Schema::hasTable('tbl_order_details')) {
            Schema::create('tbl_order_details', function (Blueprint $table) {
                $table->increments('order_details_id');
                $table->string('order_code');
                $table->integer('product_id');
                $table->string('product_name');
                $table->string('product_attribute')->nullable();
                $table->bigInteger('product_price')->default(0);
                $table->integer('product_sales_quantity')->default(0);
                $table->string('product_coupon')->default('no');
                $table->bigInteger('product_feeship')->default(0);
            });
        }

        if (! Schema::hasTable('tbl_coupon')) {
            Schema::create('tbl_coupon', function (Blueprint $table) {
                $table->increments('coupon_id');
                $table->string('coupon_name')->nullable();
                $table->string('coupon_code')->nullable();
                $table->integer('coupon_time')->default(0);
                $table->bigInteger('coupon_number')->default(0);
                $table->integer('coupon_condition')->default(1);
                $table->string('coupon_date_start')->nullable();
                $table->string('coupon_date_end')->nullable();
                $table->integer('coupon_status')->default(1);
                $table->text('coupon_used')->nullable();
            });
        }
    }
}

