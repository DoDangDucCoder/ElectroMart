<?php

namespace Tests\Feature;

use App\Application\Shared\MailData\CouponCampaignMailData;
use App\Application\Shared\MailData\OrderConfirmationMailData;
use Illuminate\Mail\Markdown;
use Tests\TestCase;

class LegacyMailTemplateRenderTest extends TestCase
{
    public function test_order_confirmation_mail_templates_render_consistently(): void
    {
        config()->set('mail.markdown.theme', 'laravel-shop');
        $brandName = (string) config('services.storefront_brand.name', 'ShopW');

        $html = $this->renderMarkdown('mail.legacy.order-confirmation', [
            'mailData' => $this->sampleOrderMailData($brandName),
        ]);

        $this->assertStringContainsString('Xác nhận đơn hàng', $html);
        $this->assertStringContainsString('ORD-TEST-001', $html);
        $this->assertStringContainsString('lịch sử đơn hàng', $html);
        $this->assertStringContainsString('Tổng thanh toán dự kiến', $html);
        $this->assertStringContainsString($brandName, $html);
    }

    public function test_coupon_mail_templates_render_consistently(): void
    {
        config()->set('mail.markdown.theme', 'laravel-shop');
        $brandName = (string) config('services.storefront_brand.name', 'ShopW');

        $payload = [
            'mailData' => new CouponCampaignMailData(
                couponCode: 'SPRING15',
                couponTime: 25,
                couponCondition: 1,
                couponNumber: 15,
                startCoupon: '01-03-2026',
                endCoupon: '31-03-2026',
                audienceLabel: 'khách hàng thường',
                shopName: $brandName,
                shopUrl: url('/'),
                loginUrl: url('/dang-nhap')
            ),
        ];

        $standardHtml = $this->renderMarkdown('mail.legacy.coupon-campaign', $payload);
        $this->assertStringContainsString('Ưu đãi dành cho khách hàng thường', $standardHtml);
        $this->assertStringContainsString('SPRING15', $standardHtml);
        $this->assertStringContainsString('01-03-2026 đến 31-03-2026', $standardHtml);

        $vipHtml = $this->renderMarkdown('mail.legacy.coupon-campaign', [
            'mailData' => new CouponCampaignMailData(
                couponCode: 'SPRING15',
                couponTime: 25,
                couponCondition: 1,
                couponNumber: 15,
                startCoupon: '01-03-2026',
                endCoupon: '31-03-2026',
                audienceLabel: 'khách hàng thân thiết',
                shopName: $brandName,
                shopUrl: url('/'),
                loginUrl: url('/dang-nhap')
            ),
        ]);

        $this->assertStringContainsString('Ưu đãi dành cho khách hàng thân thiết', $vipHtml);
        $this->assertStringContainsString('SPRING15', $vipHtml);
        $this->assertStringContainsString($brandName, $vipHtml);
    }

    protected function sampleOrderMailData(string $brandName): OrderConfirmationMailData
    {
        return OrderConfirmationMailData::fromLegacyPayload(
            shipping: [
                'customer_name' => 'Nguyễn Văn A',
                'shipping_name' => 'Nguyễn Văn A',
                'shipping_email' => 'customer@example.com',
                'shipping_address' => '123 Lê Lợi, Quận 1',
                'shipping_phone' => '0909000001',
                'shipping_notes' => 'Giao giờ hành chính',
                'shipping_method' => 1,
                'fee_ship' => 30000,
                'fee' => 30000,
            ],
            cartItems: [
                [
                    'product_name' => 'Laptop Gaming',
                    'product_price' => 15000000,
                    'product_qty' => 1,
                ],
                [
                    'product_name' => 'Chuột không dây',
                    'product_price' => 450000,
                    'product_qty' => 2,
                ],
            ],
            code: [
                'order_code' => 'ORD-TEST-001',
                'coupon_code' => 'SALE30',
            ],
            overrides: [
                'brandName' => $brandName,
            ],
        );
    }

    protected function renderMarkdown(string $view, array $data): string
    {
        return app(Markdown::class)->render($view, $data)->toHtml();
    }
}
