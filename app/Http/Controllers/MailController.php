<?php

namespace App\Http\Controllers;

use App\Application\Catalog\Services\BrandService;
use App\Application\Catalog\Services\CategoryService;
use App\Application\Catalog\Services\ProductService;
use App\Application\Shared\MailData\CouponCampaignMailData;
use App\Application\Shared\MailData\OrderConfirmationMailData;
use App\Application\Shared\MailData\PasswordRecoveryMailData;
use App\Application\Shared\Services\LegacyMailWorkflowService;
use App\Http\Controllers\Concerns\LegacyStorefrontContext;
use App\Http\Requests\Admin\Legacy\Mail\SendCouponCampaignRequest;
use App\Http\Requests\Storefront\Checkout\RecoverPasswordRequest;
use App\Http\Requests\Storefront\Checkout\ResetPasswordRequest;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon as MarketingCoupon;
use App\Mail\LegacyCouponCampaignMail;
use App\Mail\LegacyPasswordRecoveryMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class MailController extends Controller
{
    use LegacyStorefrontContext;

    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly BrandService $brandService,
        private readonly ProductService $productService,
    ) {
    }

    public function preview(Request $request)
    {
        $previews = $this->mailPreviewDefinitions();
        $selectedKey = (string) $request->query('preview', 'coupon-standard');
        $selectedKey = array_key_exists($selectedKey, $previews) ? $selectedKey : 'coupon-standard';

        return view('admin.mail.preview', [
            'previews' => $previews,
            'selectedPreviewKey' => $selectedKey,
            'selectedPreview' => $previews[$selectedKey],
            'selectedRenderUrl' => route('admin.mail.preview.render', ['preview' => $selectedKey]),
            'campaignCoupons' => $this->mailCampaignCoupons(),
            'campaignStats' => $this->mailCampaignStats(),
        ]);
    }

    public function preview_render(Request $request): Response
    {
        $previewKey = (string) $request->query('preview', 'coupon-standard');
        $previews = $this->mailPreviewDefinitions();

        abort_unless(array_key_exists($previewKey, $previews), 404);

        return response($this->renderPreviewHtml($previewKey), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    public function send_coupon(SendCouponCampaignRequest $request, $coupon_time, $coupon_condition, $coupon_number, $coupon_code)
    {
        $result = app(LegacyMailWorkflowService::class)->sendCouponCampaign(
            (string) $request->validated('coupon_code'),
            null
        );

        return $this->mailRedirectResponse($result, '/list-coupon');
    }

    public function send_coupon_vip(SendCouponCampaignRequest $request, $coupon_time, $coupon_condition, $coupon_number, $coupon_code)
    {
        $result = app(LegacyMailWorkflowService::class)->sendCouponCampaign(
            (string) $request->validated('coupon_code'),
            true
        );

        return $this->mailRedirectResponse($result, '/list-coupon');
    }
    public function recover_pass(RecoverPasswordRequest $request)
    {
        $result = app(LegacyMailWorkflowService::class)->sendPasswordRecovery(
            (string) $request->validated('email_account')
        );

        return $this->mailRedirectResponse($result, '/quen-mat-khau');
    }
    public function reset_new_pass(ResetPasswordRequest $request)
    {
        $result = app(LegacyMailWorkflowService::class)->resetPassword(
            (string) $request->validated('email'),
            (string) $request->validated('token'),
            (string) $request->validated('password_account')
        );

        if (! $result['ok']) {
            return redirect('quen-mat-khau')->with('error', $result['message']);
        }

        return redirect('dang-nhap')->with('message', $result['message']);
    }
    public function update_new_pass(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'token' => ['required', 'string', 'min:10', 'max:255'],
        ], [
            'email.required' => 'Thiếu email khôi phục mật khẩu.',
            'email.email' => 'Email khôi phục không hợp lệ.',
            'token.required' => 'Thiếu mã xác thực đặt lại mật khẩu.',
            'token.min' => 'Mã xác thực đặt lại mật khẩu không hợp lệ.',
        ]);

        $tokenValidation = app(LegacyMailWorkflowService::class)->validatePasswordResetToken(
            (string) $validated['email'],
            (string) $validated['token']
        );

        if (! $tokenValidation['ok']) {
            return redirect('quen-mat-khau')->with('error', $tokenValidation['message']);
        }

        return view('pages.checkout.new_pass', array_merge(
            $this->passwordPageContext($request, 'Quên mật khẩu'),
            [
                'resetPasswordEmail' => $validated['email'],
                'resetPasswordToken' => $validated['token'],
            ]
        ));
    }
    public function quen_mat_khau(Request $request)
    {
        return view('pages.auth.forgot-password', $this->passwordPageContext($request, 'Quên mật khẩu'));
    }

    protected function passwordPageContext(Request $request, string $title): array
    {
        $categoryPost = $this->legacyCategoryPosts();
        $slider = $this->legacyActiveSliders();
        $categories = $this->categoryService->listAll(['is_active' => true]);
        $brands = $this->brandService->listAll(['is_active' => true]);
        $allProducts = $this->productService->getRandomProducts(6);
        $categoryTabs = $categories
            ->filter(fn ($category) => ! empty($category->parent_id))
            ->sortBy('display_order')
            ->values();

        return [
            'category' => $categories,
            'brand' => $brands,
            'all_product' => $allProducts,
            'meta_desc' => $title,
            'meta_keywords' => $title,
            'meta_title' => $title,
            'url_canonical' => $request->url(),
            'slider' => $slider,
            'category_post' => $categoryPost,
            'cate_pro_tabs' => $categoryTabs,
        ];
    }

    protected function mailRedirectResponse(array $result, string $fallbackPath)
    {
        $target = $this->safeBackTarget($fallbackPath);

        if ($result['ok']) {
            return redirect()->to($target)->with('message', $result['message']);
        }

        return redirect()->to($target)->with('error', $result['message']);
    }

    protected function safeBackTarget(string $fallbackPath): string
    {
        $previous = URL::previous();
        $current = url()->current();
        $root = url('/');

        if (! is_string($previous) || $previous === '' || $previous === $current || $previous === $root) {
            return url($fallbackPath);
        }

        return $previous;
    }

    protected function mailPreviewDefinitions(): array
    {
        return [
            'coupon-standard' => [
                'label' => 'Email ưu đãi khách hàng',
                'category' => 'Khuyến mãi',
                'description' => 'Mẫu email hệ thống gửi mã giảm giá phần trăm cho khách hàng đã đăng ký.',
                'snapshot' => 'coupon-campaign.standard',
                'snapshot_label' => 'Mẫu gửi khách hàng',
            ],
            'coupon-vip' => [
                'label' => 'Email ưu đãi khách thân thiết',
                'category' => 'Khuyến mãi',
                'description' => 'Mẫu email hệ thống gửi mã giảm giá theo tiền cho nhóm khách hàng thân thiết.',
                'snapshot' => 'coupon-campaign.vip',
                'snapshot_label' => 'Mẫu gửi khách thân thiết',
            ],
            'password-recovery' => [
                'label' => 'Khôi phục mật khẩu',
                'category' => 'Tài khoản',
                'description' => 'Email đặt lại mật khẩu cho khách hàng trên website.',
                'snapshot' => 'password-recovery',
                'snapshot_label' => 'Mẫu khôi phục mật khẩu',
            ],
            'order-cod' => [
                'label' => 'Xác nhận đơn COD',
                'category' => 'Đơn hàng',
                'description' => 'Biến thể xác nhận đơn hàng mặc định với thanh toán tiền mặt.',
                'snapshot' => 'order-confirmation',
                'snapshot_label' => 'Mẫu xác nhận đơn COD',
            ],
            'order-atm' => [
                'label' => 'Xác nhận đơn chuyển khoản',
                'category' => 'Đơn hàng',
                'description' => 'Biến thể xác nhận đơn hàng với chuyển khoản ATM và không áp mã.',
                'snapshot' => 'order-confirmation.atm',
                'snapshot_label' => 'Mẫu xác nhận đơn chuyển khoản',
            ],
        ];
    }

    protected function renderPreviewHtml(string $previewKey): string
    {
        $brandName = $this->previewBrandName();

        return match ($previewKey) {
            'coupon-standard' => (new LegacyCouponCampaignMail(
                mailData: new CouponCampaignMailData(
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
                subjectLine: 'Mã khuyến mãi ngày 23-03-2026 10:00:00'
            ))->render(),
            'coupon-vip' => (new LegacyCouponCampaignMail(
                mailData: new CouponCampaignMailData(
                    couponCode: 'THANTHIET25',
                    couponTime: 10,
                    couponCondition: 2,
                    couponNumber: 250000,
                    startCoupon: '01-03-2026',
                    endCoupon: '30-04-2026',
                    audienceLabel: 'khách hàng thân thiết',
                    shopName: $brandName,
                    shopUrl: url('/'),
                    loginUrl: url('/dang-nhap')
                ),
                subjectLine: 'Mã khuyến mãi khách thân thiết ngày 23-03-2026 10:00:00'
            ))->render(),
            'password-recovery' => (new LegacyPasswordRecoveryMail(
                mailData: new PasswordRecoveryMailData(
                    title: 'Lấy lại mật khẩu ' . $brandName,
                    email: 'recover@example.com',
                    resetUrl: url('/update-new-pass?email=recover%40example.com&token=secure-token-123'),
                    shopName: $brandName
                ),
                subjectLine: 'Lấy lại mật khẩu ' . $brandName
            ))->render(),
            'order-cod' => $this->renderOrderPreview($this->makeOrderPreviewData()),
            'order-atm' => $this->renderOrderPreview($this->makeOrderPreviewData([
                'shipping_method' => 0,
                'shipping_notes' => '',
            ], [
                'coupon_code' => null,
            ])),
            default => abort(404),
        };
    }

    protected function renderOrderPreview(OrderConfirmationMailData $mailData): string
    {
        return app(Markdown::class)
            ->render('mail.legacy.order-confirmation', [
                'mailData' => $mailData,
            ])
            ->toHtml();
    }

    protected function makeOrderPreviewData(array $shippingOverrides = [], array $codeOverrides = []): OrderConfirmationMailData
    {
        $brandName = $this->previewBrandName();

        $shipping = array_merge([
            'customer_name' => 'Nguyễn Văn A',
            'shipping_name' => 'Nguyễn Văn A',
            'shipping_email' => 'customer@example.com',
            'shipping_address' => '123 Lê Lợi, Quận 1',
            'shipping_phone' => '0909000001',
            'shipping_notes' => 'Giao giờ hành chính',
            'shipping_method' => 1,
            'fee_ship' => 30000,
        ], $shippingOverrides);

        $code = array_merge([
            'order_code' => 'ORD-TEST-001',
            'coupon_code' => 'SALE30',
        ], $codeOverrides);

        if (blank(Arr::get($code, 'coupon_code'))) {
            $code['coupon_code'] = 'Không áp dụng';
        }

        return OrderConfirmationMailData::fromLegacyPayload(
            shipping: $shipping,
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
            code: $code,
            overrides: [
                'brandName' => $brandName,
            ]
        );
    }

    protected function previewBrandName(): string
    {
        return (string) config('services.storefront_brand.name', 'ShopW');
    }

    protected function mailCampaignCoupons()
    {
        if (! Schema::hasTable((new MarketingCoupon())->getTable())) {
            return collect();
        }

        $today = Carbon::now('Asia/Ho_Chi_Minh')->startOfDay();

        return MarketingCoupon::query()
            ->where('is_active', true)
            ->where('usage_limit', '>', 0)
            ->whereDate('expires_at', '>=', $today->toDateString())
            ->orderByDesc('id')
            ->limit(8)
            ->get();
    }

    protected function mailCampaignStats(): array
    {
        if (! Schema::hasTable((new Customer())->getTable())) {
            return ['all' => 0, 'vip' => 0];
        }

        $customers = Customer::query()->where('is_active', true)->get();
        $validCustomers = $customers->filter(fn (Customer $customer): bool => filter_var($customer->email, FILTER_VALIDATE_EMAIL) !== false);

        return [
            'all' => $validCustomers->count(),
            'vip' => $validCustomers->filter(fn (Customer $customer): bool => $this->isPreviewVipCustomer($customer))->count(),
        ];
    }

    protected function isPreviewVipCustomer(Customer $customer): bool
    {
        $meta = is_array($customer->meta) ? $customer->meta : [];

        if (array_key_exists('legacy_vip', $meta)) {
            return (int) $meta['legacy_vip'] === 1;
        }

        return (float) ($customer->loyalty_points_balance ?? 0) >= 1000;
    }
}
