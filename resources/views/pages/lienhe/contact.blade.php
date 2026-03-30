@extends('layout')

@section('content')
    @php
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
    @endphp

    <div class="g-contact-page">
        @php
            $supportConfig = config('services.storefront_support', []);
            $supportPhoneDisplay = $supportConfig['phone_display'] ?? '1900.0000';
            $supportPhoneHref = $supportConfig['phone_href'] ?? 'tel:19000000';
            $contactQuickLinks = [
                [
                    'icon' => 'fa-phone',
                    'label' => config('storefront.labels.hotline_support', 'Hotline hỗ trợ'),
                    'value' => $supportPhoneDisplay,
                    'url' => $supportPhoneHref,
                ],
                [
                    'icon' => 'fa-comments',
                    'label' => 'Chat mua hàng',
                    'value' => config('storefront.copy.contact_chat_value', 'Nhận tư vấn nhanh trước khi chốt đơn hoặc cần hỗ trợ sau mua'),
                    'url' => url('/lien-he'),
                ],
                [
                    'icon' => 'fa-history',
                    'label' => 'Lịch sử đơn hàng',
                    'value' => config('storefront.copy.contact_history_value', 'Theo dõi trạng thái đơn đã đặt'),
                    'url' => url('/history'),
                ],
            ];
        @endphp
        <section class="shopw-contact-hero">
            <div class="shopw-contact-hero-copy">
                <span class="shopw-contact-kicker">Hỗ trợ trước và sau mua hàng</span>
                <h1>Liên hệ với đội ngũ cửa hàng</h1>
                <p>Hỏi về sản phẩm, kiểm tra showroom, hỗ trợ kỹ thuật hoặc bảo hành. Tất cả thông tin quan trọng được gom gọn để bạn liên hệ nhanh và theo dõi dễ hơn trên cả desktop lẫn mobile.</p>
            </div>
            <div class="shopw-contact-hero-metrics">
                <article>
                    <strong>24/7</strong>
                    <span>CSKH online</span>
                </article>
                <article>
                    <strong>Showroom</strong>
                    <span>Thông tin cửa hàng</span>
                </article>
                <article>
                    <strong>Social</strong>
                    <span>Fanpage và cộng đồng</span>
                </article>
            </div>
        </section>

        <div class="shopw-content-support-strip">
            @foreach ($contactQuickLinks as $quickLink)
                <a href="{{ $quickLink['url'] }}" class="shopw-content-support-item">
                    <span class="shopw-content-support-icon">
                        <i class="fa {{ $quickLink['icon'] }}"></i>
                    </span>
                    <span class="shopw-content-support-copy">
                        <strong>{{ $quickLink['label'] }}</strong>
                        <small>{{ $quickLink['value'] }}</small>
                    </span>
                    <span class="shopw-content-support-arrow">
                        <i class="fa fa-angle-right"></i>
                    </span>
                </a>
            @endforeach
        </div>

        @forelse ($contact as $cont)
            <div class="shopw-contact-grid">
                <section class="shopw-contact-card">
                    <div class="shopw-contact-card-head">
                        <span class="shopw-contact-icon"><i class="fa fa-comments-o"></i></span>
                        <div>
                            <h2>Thông tin liên hệ</h2>
                            <p>Kênh liên hệ chính thức, hotline và địa chỉ tiếp nhận.</p>
                        </div>
                    </div>
                    <div class="shopw-contact-richtext">
                        {!! $stripSelfIframe($cont->info_contact) !!}
                    </div>
                </section>

                <section class="shopw-contact-card">
                    <div class="shopw-contact-card-head">
                        <span class="shopw-contact-icon"><i class="fa fa-facebook-square"></i></span>
                        <div>
                            <h2>Cộng đồng và fanpage</h2>
                            <p>Cập nhật tin mới, trả lời nhanh và nội dung hướng dẫn.</p>
                        </div>
                    </div>
                    <div class="shopw-contact-richtext">
                        {!! $stripSelfIframe($cont->info_fanpage) !!}
                    </div>
                </section>
            </div>

            <section class="shopw-contact-map-card">
                <div class="shopw-contact-card-head">
                    <span class="shopw-contact-icon"><i class="fa fa-map-marker"></i></span>
                    <div>
                        <h2>Bản đồ và showroom</h2>
                        <p>Xác định điểm đến nhanh trước khi ghé cửa hàng hoặc gọi hỗ trợ tại chỗ.</p>
                    </div>
                </div>
                <div class="shopw-contact-map-frame">
                    {!! $stripSelfIframe($cont->info_map) !!}
                </div>
            </section>
        @empty
            <section class="shopw-contact-empty">
                <h2>Chưa có thông tin liên hệ</h2>
                <p>Thông tin liên hệ đang được cập nhật. Bạn có thể quay lại sau hoặc dùng các kênh hỗ trợ nhanh ở phía trên để được trợ giúp.</p>
            </section>
        @endforelse
    </div>
@endsection

