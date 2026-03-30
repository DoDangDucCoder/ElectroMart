<?php

namespace Tests\Feature;

use App\Application\Shared\MailData\CouponCampaignMailData;
use App\Application\Shared\MailData\OrderConfirmationMailData;
use App\Application\Shared\MailData\PasswordRecoveryMailData;
use App\Mail\LegacyCouponCampaignMail;
use App\Mail\LegacyPasswordRecoveryMail;
use Illuminate\Mail\Markdown;
use Tests\TestCase;

class LegacyMailSnapshotTest extends TestCase
{
    public function test_coupon_campaign_mail_matches_snapshot(): void
    {
        $mail = new LegacyCouponCampaignMail(
            mailData: new CouponCampaignMailData(
                couponCode: 'SPRING15',
                couponTime: 25,
                couponCondition: 1,
                couponNumber: 15,
                startCoupon: '01-03-2026',
                endCoupon: '31-03-2026',
                audienceLabel: 'khách hàng thường',
                shopName: 'Laravel Shop',
                shopUrl: url('/'),
                loginUrl: url('/dang-nhap')
            ),
            subjectLine: 'Mã khuyến mãi ngày 23-03-2026 10:00:00'
        );

        $this->assertHtmlSnapshot('coupon-campaign.standard', $mail->render());
    }

    public function test_coupon_campaign_vip_mail_matches_snapshot(): void
    {
        $mail = new LegacyCouponCampaignMail(
            mailData: new CouponCampaignMailData(
                couponCode: 'VIP25',
                couponTime: 10,
                couponCondition: 2,
                couponNumber: 250000,
                startCoupon: '01-03-2026',
                endCoupon: '30-04-2026',
                audienceLabel: 'khách hàng thân thiết',
                shopName: 'Laravel Shop',
                shopUrl: url('/'),
                loginUrl: url('/dang-nhap')
            ),
            subjectLine: 'Mã khuyến mãi VIP ngày 23-03-2026 10:00:00'
        );

        $this->assertHtmlSnapshot('coupon-campaign.vip', $mail->render());
    }

    public function test_password_recovery_mail_matches_snapshot(): void
    {
        $mail = new LegacyPasswordRecoveryMail(
            mailData: new PasswordRecoveryMailData(
                title: 'Lấy lại mật khẩu Laravel Shop',
                email: 'recover@example.com',
                resetUrl: url('/update-new-pass?email=recover%40example.com&token=secure-token-123'),
                shopName: 'Laravel Shop'
            ),
            subjectLine: 'Lấy lại mật khẩu Laravel Shop'
        );

        $this->assertHtmlSnapshot('password-recovery', $mail->render());
    }

    public function test_order_confirmation_preview_matches_snapshot(): void
    {
        config()->set('mail.markdown.theme', 'laravel-shop');

        $html = app(Markdown::class)->render('mail.legacy.order-confirmation', [
            'mailData' => OrderConfirmationMailData::fromLegacyPayload(
                shipping: [
                    'customer_name' => 'Nguyễn Văn A',
                    'shipping_name' => 'Nguyễn Văn A',
                    'shipping_email' => 'customer@example.com',
                    'shipping_address' => '123 Lê Lợi, Quận 1',
                    'shipping_phone' => '0909000001',
                    'shipping_notes' => 'Giao giờ hành chính',
                    'shipping_method' => 1,
                    'fee_ship' => 30000,
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
            ),
        ])->toHtml();

        $this->assertHtmlSnapshot('order-confirmation', $html);
    }

    public function test_order_confirmation_atm_preview_matches_snapshot(): void
    {
        config()->set('mail.markdown.theme', 'laravel-shop');

        $html = app(Markdown::class)->render('mail.legacy.order-confirmation', [
            'mailData' => OrderConfirmationMailData::fromLegacyPayload(
                shipping: [
                    'customer_name' => 'Trần Thị B',
                    'shipping_name' => 'Trần Thị B',
                    'shipping_email' => 'atm@example.com',
                    'shipping_address' => '45 Nguyễn Huệ, Quận 1',
                    'shipping_phone' => '0908000002',
                    'shipping_notes' => '',
                    'shipping_method' => 0,
                    'fee_ship' => 45000,
                ],
                cartItems: [
                    [
                        'product_name' => 'Màn hình 27 inch',
                        'product_price' => 5200000,
                        'product_qty' => 1,
                    ],
                    [
                        'product_name' => 'Bàn phím cơ',
                        'product_price' => 1890000,
                        'product_qty' => 1,
                    ],
                ],
                code: [
                    'order_code' => 'ORD-ATM-002',
                    'coupon_code' => 'Không áp dụng',
                ],
            ),
        ])->toHtml();

        $this->assertHtmlSnapshot('order-confirmation.atm', $html);
    }

    protected function assertHtmlSnapshot(string $name, string $html): void
    {
        $snapshotPath = base_path('tests/Fixtures/mail/' . $name . '.sha1');
        $hash = sha1($this->normalizeHtml($html));

        if (env('UPDATE_MAIL_SNAPSHOTS')) {
            if (! is_dir(dirname($snapshotPath))) {
                mkdir(dirname($snapshotPath), 0777, true);
            }

            file_put_contents($snapshotPath, $hash . PHP_EOL);
        }

        $this->assertFileExists($snapshotPath);
        $this->assertSame(
            trim((string) file_get_contents($snapshotPath)),
            $hash
        );
    }

    protected function normalizeHtml(string $html): string
    {
        $normalized = preg_replace('/©\s+\d{4}/u', '© YYYY', $html) ?? $html;
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;
        $normalized = preg_replace('/>\s+</', '><', $normalized) ?? $normalized;

        return trim($normalized);
    }
}
