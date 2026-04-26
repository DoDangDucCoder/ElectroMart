@php
    $supportConfig = config('services.storefront_support', []);
    $supportChannels = [
        [
            'variant' => 'facebook',
            'title' => 'Chat Facebook',
            'meta' => $supportConfig['facebook_hours'] ?? '8h-21h',
            'href' => $supportConfig['facebook_url'] ?? 'https://m.me/100332251915013',
            'icon' => 'M',
            'external' => true,
        ],
        [
            'variant' => 'zalo',
            'title' => 'Chat Zalo',
            'meta' => $supportConfig['zalo_hours'] ?? '8h-21h',
            'href' => filled($supportConfig['zalo_url'] ?? null) ? $supportConfig['zalo_url'] : url('/lien-he'),
            'icon' => 'Zalo',
            'external' => filled($supportConfig['zalo_url'] ?? null),
        ],
        [
            'variant' => 'hotline',
            'title' => $supportConfig['contact_phone_display'] ?? '1900.0000',
            'meta' => $supportConfig['contact_hours'] ?? '8h-21h',
            'href' => 'tel:' . ($supportConfig['contact_phone_href'] ?? '19000000'),
            'icon_class' => 'fa fa-phone',
            'external' => false,
        ],
    ];
    $heroSupportCta = $supportConfig['chat_cta'] ?? 'Hỏi sản phẩm, giá khuyến mãi, bảo hành';
@endphp

<section class="shopw-support-dock-host" data-storefront-support-dock>
    <div class="container shopw-support-dock-container">
        <aside class="shopw-hero-support shopw-hero-support--dock shopw-support-dock"
            aria-label="Kênh hỗ trợ nhanh"
            data-support-placement="fixed-right"
            data-support-rail="icon-first"
            data-support-restored="true">
            <div class="shopw-hero-support-head">
                <span class="shopw-hero-support-kicker">{{ config('storefront.labels.support_quick', 'Hỗ trợ nhanh') }}</span>
                <strong class="shopw-hero-support-title">{{ config('storefront.labels.support_connect_title', 'Kết nối với cửa hàng') }}</strong>
            </div>

            <div class="shopw-hero-support-stack">
                @foreach ($supportChannels as $channel)
                    <a href="{{ $channel['href'] }}"
                        class="shopw-hero-support-card shopw-hero-support-card--{{ $channel['variant'] }}"
                        aria-label="{{ $channel['title'] }} - {{ $channel['meta'] }}"
                        @if ($channel['external']) target="_blank" rel="noopener noreferrer" @endif>
                        <span class="shopw-hero-support-icon" aria-hidden="true">
                            @if (!empty($channel['icon_class']))
                                <i class="{{ $channel['icon_class'] }}"></i>
                            @else
                                <span>{{ $channel['icon'] }}</span>
                            @endif
                        </span>
                        <span class="shopw-hero-support-copy">
                            <strong>{{ $channel['title'] }}</strong>
                            <small>{{ $channel['meta'] }}</small>
                        </span>
                        <span class="shopw-hero-support-arrow" aria-hidden="true">
                            <i class="fa fa-angle-right"></i>
                        </span>
                    </a>
                @endforeach
            </div>

            <a href="{{ url('/lien-he') }}" class="shopw-hero-support-cta"
                aria-label="{{ config('storefront.labels.chat_assistant', 'Trợ lý bán hàng') }} - {{ $heroSupportCta }}"
                data-chatbot-launch>
                <span class="shopw-hero-support-cta-main">
                    <span class="shopw-hero-support-cta-icon" aria-hidden="true">
                        <i class="fa fa-comments-o"></i>
                    </span>
                    <span class="shopw-hero-support-cta-copy">
                        <strong>{{ config('storefront.labels.chat_assistant', 'Trợ lý bán hàng') }}</strong>
                        <small>{{ $heroSupportCta }}</small>
                    </span>
                </span>
                <span class="shopw-hero-support-cta-tail" aria-hidden="true">
                    <i class="fa fa-angle-up"></i>
                </span>
            </a>
        </aside>
    </div>
</section>
