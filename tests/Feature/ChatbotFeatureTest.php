<?php

namespace Tests\Feature;

use App\Application\Catalog\Services\ProductService;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use App\Infrastructure\Persistence\Eloquent\Order\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ChatbotFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $productService;
    protected Brand $brand;
    protected ProductCategory $phones;
    protected ProductCategory $accessories;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->productService = $this->app->make(ProductService::class);

        $this->brand = Brand::create([
            'name' => 'Chatbot Brand',
            'slug' => 'chatbot-brand',
            'description' => 'Brand for chatbot feature tests',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ]);

        $this->phones = ProductCategory::create([
            'name' => 'Điện thoại',
            'slug' => 'dien-thoai',
            'description' => 'Danh mục điện thoại',
            'is_active' => true,
            'display_order' => 0,
            'meta' => null,
        ]);

        $this->accessories = ProductCategory::create([
            'name' => 'Phụ kiện',
            'slug' => 'phu-kien',
            'description' => 'Danh mục phụ kiện',
            'is_active' => true,
            'display_order' => 1,
            'meta' => null,
        ]);
    }

    public function test_chatbot_bootstraps_guest_conversation_with_welcome_message(): void
    {
        $this->getJson('/chatbot/conversation')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.channel', 'storefront')
            ->assertJsonPath('data.messages.0.response_type', 'welcome')
            ->assertJsonPath('data.messages.0.sender', 'bot');

        $this->assertDatabaseCount('chat_conversations', 1);
        $this->assertDatabaseCount('chat_messages', 1);
    }

    public function test_chatbot_answers_faq_question_with_rule_based_reply(): void
    {
        $this->postJson('/chatbot/messages', [
            'message' => 'Chính sách bảo hành thế nào?',
        ])
            ->assertOk()
            ->assertJsonPath('data.bot_message.response_type', 'faq')
            ->assertJsonPath('data.bot_message.intent', 'warranty')
            ->assertJsonFragment([
                'sender' => 'bot',
            ]);

        $this->assertDatabaseHas('chat_messages', [
            'sender' => 'user',
            'message' => 'Chính sách bảo hành thế nào?',
        ]);
    }

    public function test_chatbot_can_search_products_with_category_and_price_signal(): void
    {
        $matching = $this->createProduct('iPhone 17', $this->phones, [
            'price' => 1500000,
        ]);

        $this->createProduct('Chuột Gaming', $this->accessories, [
            'price' => 500000,
        ]);

        $this->postJson('/chatbot/messages', [
            'message' => 'Tìm điện thoại dưới 2 triệu',
        ])
            ->assertOk()
            ->assertJsonPath('data.bot_message.response_type', 'product_list')
            ->assertJsonFragment([
                'id' => $matching->product_id,
                'name' => 'iPhone 17',
            ]);
    }

    public function test_chatbot_recommendation_uses_customer_order_history(): void
    {
        $customer = $this->createCustomer();
        $purchased = $this->createProduct('iPhone 17 Purchased', $this->phones);
        $candidate = $this->createProduct('iPhone 17 Candidate', $this->phones);

        $order = Order::create([
            'code' => 'ORD-' . Str::upper(Str::random(8)),
            'customer_id' => $customer->id,
            'status' => 'completed',
            'payment_status' => 'paid',
            'fulfillment_status' => 'fulfilled',
            'currency' => 'VND',
            'subtotal' => $purchased->final_price,
            'discount_total' => 0,
            'shipping_total' => 0,
            'tax_total' => 0,
            'total' => $purchased->final_price,
            'placed_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $purchased->id,
            'product_name' => $purchased->name,
            'sku' => $purchased->sku,
            'original_price' => $purchased->price,
            'price' => $purchased->final_price,
            'sale_price' => $purchased->sale_price,
            'cost_price' => $purchased->cost_price,
            'sale_type' => $purchased->sale_type,
            'sale_value' => $purchased->sale_value,
            'sale_amount' => $purchased->sale_amount,
            'discount_amount' => $purchased->sale_amount,
            'quantity' => 1,
            'subtotal' => $purchased->final_price,
            'tax_amount' => 0,
        ]);

        $this->withSession(['customer_id' => $customer->id])
            ->postJson('/chatbot/messages', [
                'message' => 'Gợi ý cho tôi một sản phẩm phù hợp',
            ])
            ->assertOk()
            ->assertJsonPath('data.bot_message.response_type', 'product_list')
            ->assertJsonFragment([
                'id' => $candidate->product_id,
                'name' => 'iPhone 17 Candidate',
            ]);
    }

    public function test_api_chatbot_message_endpoint_returns_guest_reply_with_session_token(): void
    {
        $response = $this->postJson('/api/v1/chatbot/messages', [
            'message' => 'Shop có hỗ trợ giao hàng không?',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.bot_message.response_type', 'faq')
            ->assertJsonPath('data.conversation.channel', 'api');

        $this->assertNotEmpty($response->json('data.conversation.session_token'));
    }

    protected function createCustomer(string $email = 'chatbot@example.com'): Customer
    {
        return Customer::create([
            'name' => 'Chatbot Customer',
            'email' => $email,
            'password' => bcrypt('secret123'),
            'phone' => '09' . random_int(10000000, 99999999),
            'is_active' => true,
        ]);
    }

    protected function createProduct(string $name, ProductCategory $category, array $overrides = []): Product
    {
        return $this->productService->createProduct(array_merge([
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'brand_id' => $this->brand->id,
            'primary_category_id' => $category->id,
            'price' => 1500000,
            'cost_price' => 1000000,
            'stock_quantity' => 10,
            'description' => '<p>Mô tả ' . $name . '</p>',
            'content' => '<p>Nội dung ' . $name . '</p>',
            'category_ids' => [$category->id],
            'tags' => 'chatbot,search',
            'is_active' => true,
            'meta' => [],
        ], $overrides));
    }
}
