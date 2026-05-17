@php
    $campaignCards = collect($campaignCards ?? [])->values();
    $campaignGridClass = match (true) {
        $campaignCards->count() <= 1 => 'shopw-home-campaign-grid--solo',
        $campaignCards->count() === 2 => 'shopw-home-campaign-grid--duo',
        default => 'shopw-home-campaign-grid--full',
    };
@endphp

@if ($campaignCards->isNotEmpty())
    <section class="features_items shopw-home-section shopw-home-campaign-section">
        <div class="shopw-section-head shopw-section-head-split">
            <div>
                <span class="shopw-section-kicker">Ưu đãi và chiến dịch</span>
                <h2 class="title text-center">Điểm chạm nổi bật trong ngày</h2>
                <p class="shopw-section-copy">Không dàn đều quá nhiều banner. Mỗi block chỉ giữ một mục tiêu rõ để người dùng dễ hiểu và đi tiếp nhanh hơn.</p>
            </div>
            <a href="{{ url('/san-pham') }}" class="shopw-section-link">Mở bộ lọc sản phẩm</a>
        </div>

        <div class="shopw-home-campaign-grid {{ $campaignGridClass }}">
            @foreach ($campaignCards as $card)
                <article class="shopw-home-campaign-card shopw-home-campaign-card--{{ $card['tone'] }} {{ $loop->first ? 'is-featured' : '' }}">
                    <a href="{{ $card['url'] }}" class="shopw-home-campaign-link">
                        <span class="shopw-home-campaign-media" @if (!empty($card['image'])) style="background-image:url('{{ $card['image'] }}')" @endif></span>
                        <span class="shopw-home-campaign-copy">
                            <span class="shopw-home-campaign-kicker">{{ $card['kicker'] }}</span>
                            <strong>{{ $card['title'] }}</strong>
                            <p>{{ $card['description'] }}</p>
                            @if (!empty($card['tags']))
                                <span class="shopw-home-campaign-tags">
                                    @foreach ($card['tags'] as $tag)
                                        <em>{{ $tag }}</em>
                                    @endforeach
                                </span>
                            @endif
                            <span class="shopw-home-campaign-cta">
                                {{ $card['cta'] ?? 'Khám phá ngay' }}
                                <i class="fa fa-angle-right" aria-hidden="true"></i>
                            </span>
                        </span>
                    </a>
                </article>
            @endforeach
        </div>
    </section>
@endif

