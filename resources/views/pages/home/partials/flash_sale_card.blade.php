@php
    $campaignTags = collect($card['campaign_tags'] ?? ['25.4', 'PHIẾU GIẢM', 'SIÊU RẺ'])
        ->filter()
        ->take(3)
        ->values();
    $dealBarLabel = filled($card['deal_bar_label'] ?? null)
        ? $card['deal_bar_label']
        : (filled($card['sold_quantity'] ?? null) ? 'ĐÃ BÁN ' . $card['sold_quantity'] : 'ĐANG BÁN CHẠY');
    $dealBarIsSold = str_starts_with($dealBarLabel, 'ĐÃ BÁN');
@endphp

<article class="shopw-flash-sale-card{{ !empty($card['is_mock']) ? ' is-mock' : '' }}">
    <a href="{{ $card['url'] }}" class="shopw-flash-sale-link" aria-label="{{ $card['name'] }}">
        <span class="shopw-flash-sale-thumb-shell">
            @if (!empty($card['seller_badge']))
                <span class="shopw-flash-sale-tag shopw-flash-sale-tag--{{ $card['seller_badge_variant'] ?? 'favorite' }}">
                    {{ $card['seller_badge'] }}
                </span>
            @endif

            @if (!empty($card['discount_percent']))
                <span class="shopw-flash-sale-discount">
                    <strong>-{{ $card['discount_percent'] }}%</strong>
                </span>
            @endif

            <span class="shopw-flash-sale-media">
                <img src="{{ $card['image'] }}" alt="{{ $card['name'] }}" loading="lazy" draggable="false">
            </span>

            @if ($campaignTags->isNotEmpty())
                <span class="shopw-flash-sale-badges" aria-hidden="true">
                    @foreach ($campaignTags as $tag)
                        <em>{{ $tag }}</em>
                    @endforeach
                </span>
            @endif
        </span>

        <span class="shopw-flash-sale-body">
            <span class="shopw-flash-sale-pricing">
                <strong>{{ number_format((float) $card['final_price'], 0, ',', '.') }} ₫</strong>
            </span>

            <span class="shopw-flash-sale-progress-shell{{ $dealBarIsSold ? ' is-sold-meter' : '' }}">
                <span class="shopw-flash-sale-progress" aria-hidden="true">
                    <span style="width: {{ max(0, min(100, (int) ($card['sold_progress'] ?? 0))) }}%"></span>
                </span>
                <span class="shopw-flash-sale-progress-label">
                    {{ $dealBarLabel }}
                </span>
            </span>
        </span>
    </a>
</article>
