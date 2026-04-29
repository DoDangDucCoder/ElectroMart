@php
    $supportConfig = config('services.storefront_support', []);
    $brandConfig = config('services.storefront_brand', []);
    $footerBrandName = $brandConfig['name'] ?? 'ShopW';
    $footerBrandDescriptor = $brandConfig['descriptor'] ?? 'Cửa hàng công nghệ';
    $footerPrimaryContact = $contact_footer->first();
    $isProductsCatalogFooter = request()->is('san-pham');
    $footerLogoPath = 'uploads/brand/dangduc-logo-dark.png.png';
    if (! file_exists(public_path($footerLogoPath))) {
        $footerLogoPath = 'uploads/brand/dangduc-logo-horizontal.png.png';
    }
    if (! file_exists(public_path($footerLogoPath))) {
        $footerLogoPath = 'uploads/brand/dangduc-logo-icon.png.png';
    }

    $selfHosts = array_filter(array_unique([
        parse_url(config('app.url'), PHP_URL_HOST),
        request()->getHost(),
        '127.0.0.1',
        'localhost',
    ]));

    $stripSelfIframe = function (?string $html) use ($selfHosts) {
        if (! $html) {
            return '';
        }

        $pattern = '#<(iframe|frame|embed|object)\b[^>]*\b(?:src|data)\s*=\s*["\']([^"\']+)["\'][^>]*>(?:.*?</\1>)?#is';

        return preg_replace_callback($pattern, function ($matches) use ($selfHosts) {
            $src = trim($matches[2] ?? '');
            if ($src === '') {
                return $matches[0];
            }

            $host = str_starts_with($src, '//')
                ? parse_url('http:' . $src, PHP_URL_HOST)
                : parse_url($src, PHP_URL_HOST);

            if (! $host && str_starts_with($src, '/')) {
                return '';
            }

            if ($host && in_array($host, $selfHosts, true)) {
                return '';
            }

            return $matches[0];
        }, $html);
    };

    $prepareFooterHtml = function (?string $html) use ($stripSelfIframe) {
        $clean = trim((string) $stripSelfIframe($html));

        if ($clean === '') {
            return ['', false];
        }

        $textContent = trim((string) preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($clean), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        $hasEmbed = preg_match('/<(iframe|frame|embed|object)\b/i', $clean) === 1;

        return [$clean, $textContent !== '' || $hasEmbed];
    };

    [$footerContactHtml, $hasFooterContactHtml] = $prepareFooterHtml(optional($footerPrimaryContact)->info_contact);
    [$footerFanpageHtml, $hasFooterFanpageHtml] = $prepareFooterHtml(optional($footerPrimaryContact)->info_fanpage);
    $footerSlogan = trim((string) (optional($footerPrimaryContact)->slogan_logo ?: 'Cập nhật sản phẩm, dịch vụ và hỗ trợ sau mua trong cùng một storefront gọn hơn.'));

    $footerLinks = collect($post_footer ?? [])->map(function ($post) {
        return [
            'label' => $post->post_title,
            'url' => url('/bai-viet/' . $post->post_slug),
        ];
    })->take(5);

    if ($footerLinks->isEmpty()) {
        $footerLinks = collect([
            [
                'label' => 'Liên hệ hỗ trợ',
                'url' => url('/lien-he'),
            ],
            [
                'label' => 'Video công nghệ',
                'url' => url('/video-shop'),
            ],
            [
                'label' => config('storefront.labels.recommend_for_you', 'Gợi ý cho bạn'),
                'url' => url('/goi-y-san-pham'),
            ],
            [
                'label' => config('storefront.labels.order_tracking', 'Theo dõi đơn hàng'),
                'url' => url('/history'),
            ],
        ]);
    }
@endphp

<footer id="footer" class="shopw-footer">
    <div class="shopw-footer-shell">
        <div class="shopw-footer-main">
            <section @class(['shopw-footer-brand-card', 'shopw-footer-brand-card--catalog' => $isProductsCatalogFooter])>
                @if ($isProductsCatalogFooter)
                    <img class="shopw-footer-brand-logo" src="{{ asset($footerLogoPath) }}" alt="Dang Duc">
                @endif
                <span class="shopw-footer-kicker">{{ $footerBrandDescriptor }}</span>
                <h2>{{ $footerBrandName }}</h2>
                <p>{{ $footerSlogan }}</p>

                <div class="shopw-footer-brand-pills">
                    <a href="{{ $supportConfig['phone_href'] ?? 'tel:19000000' }}" class="shopw-footer-pill">
                        <i class="fa fa-phone"></i>
                        <span>{{ $supportConfig['phone_display'] ?? '1900.0000' }}</span>
                    </a>
                    <a href="{{ url('/lien-he') }}" class="shopw-footer-pill">
                        <i class="fa fa-map-marker"></i>
                        <span>{{ config('storefront.labels.contact_fast', 'Liên hệ nhanh') }}</span>
                    </a>
                </div>
            </section>

            <section class="shopw-footer-card shopw-footer-link-card">
                <span class="shopw-footer-kicker">Dịch vụ</span>
                <h3>Dẫn tới đúng nơi cần xem</h3>
                <ul class="shopw-footer-link-list">
                    @foreach ($footerLinks as $footerLink)
                        <li>
                            <a href="{{ $footerLink['url'] }}">
                                <span>{{ $footerLink['label'] }}</span>
                                <i class="fa fa-angle-right" aria-hidden="true"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>

            <section class="shopw-footer-card shopw-footer-contact-card">
                <div class="shopw-footer-card-head">
                    <div>
                        <span class="shopw-footer-kicker">Thông tin cửa hàng</span>
                        <h3>Địa chỉ và kết nối hỗ trợ</h3>
                    </div>
                </div>

                <div
                    class="shopw-footer-contact-grid {{ $hasFooterContactHtml && $hasFooterFanpageHtml ? 'shopw-footer-contact-grid--split' : 'shopw-footer-contact-grid--single' }}">
                    @if ($hasFooterContactHtml && $hasFooterFanpageHtml)
                        <div class="shopw-footer-contact-body">
                            {!! $footerContactHtml !!}
                        </div>

                        <div class="shopw-footer-fanpage">
                            <span class="shopw-footer-fanpage-label">Fanpage</span>
                            <div class="shopw-footer-embed">
                                {!! $footerFanpageHtml !!}
                            </div>
                        </div>
                    @elseif ($hasFooterContactHtml)
                        <div class="shopw-footer-contact-body shopw-footer-contact-body--single">
                            {!! $footerContactHtml !!}
                        </div>
                    @elseif ($hasFooterFanpageHtml)
                        <div class="shopw-footer-fanpage shopw-footer-fanpage--single">
                            <span class="shopw-footer-fanpage-label">Fanpage</span>
                            <div class="shopw-footer-embed">
                                {!! $footerFanpageHtml !!}
                            </div>
                        </div>
                    @else
                        <div class="shopw-footer-contact-body shopw-footer-contact-body--fallback">
                            <p>Đội ngũ cửa hàng luôn sẵn sàng hỗ trợ qua hotline, trang liên hệ và các kênh chat trực tuyến.</p>

                            <div class="shopw-footer-fallback-links">
                                <a href="{{ $supportConfig['phone_href'] ?? 'tel:19000000' }}" class="shopw-footer-pill shopw-footer-pill--light">
                                    <i class="fa fa-phone"></i>
                                    <span>{{ $supportConfig['phone_display'] ?? '1900.0000' }}</span>
                                </a>
                                <a href="{{ $supportConfig['facebook_url'] ?? url('/lien-he') }}" class="shopw-footer-inline-link">
                                    Mở kênh Facebook
                                </a>
                                <a href="{{ url('/lien-he') }}" class="shopw-footer-inline-link">
                                    Đi tới trang liên hệ
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            <section class="shopw-footer-card shopw-footer-newsletter-card">
                <span class="shopw-footer-kicker">Đăng ký email</span>
                <h3>Nhận thông báo mới từ cửa hàng</h3>
                <p>Để lại email để theo dõi khuyến mãi, nội dung mới và các thông báo hữu ích liên quan tới sản phẩm bạn quan tâm.</p>

                <form action="#" class="shopw-footer-newsletter-form" onsubmit="return false;">
                    <input type="email" placeholder="Điền email của bạn..." aria-label="Email nhận tin">
                    <button type="button">
                        <i class="fa fa-arrow-circle-o-right"></i>
                    </button>
                </form>

                <small class="shopw-footer-newsletter-note">Bạn cũng có thể mở trang liên hệ để gửi yêu cầu hỗ trợ chi tiết hơn.</small>
                <a href="{{ url('/lien-he') }}" class="shopw-footer-inline-link">Đi tới trang liên hệ</a>
            </section>
        </div>
    </div>

    <div class="shopw-footer-bottom">
        <div class="shopw-footer-bottom-inner">
            <p>Bản quyền © 2025 {{ $footerBrandName }}</p>
            <span>{{ $footerBrandDescriptor }}</span>
        </div>
    </div>
</footer>
