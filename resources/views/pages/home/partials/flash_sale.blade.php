@php
    $cards = collect($flashSale['products'] ?? [])->values();
    $countdownEndsAt = $flashSale['countdown_ends_at'] ?? null;
@endphp

@if ($cards->isNotEmpty())
    <section class="features_items shopw-home-section shopw-flash-sale-section">
        <div class="shopw-flash-sale-shell">
            <div class="shopw-flash-sale-head">
                <div class="shopw-flash-sale-copy">
                    <span class="shopw-flash-sale-kicker">{{ $flashSale['kicker'] ?? 'Hot deal' }}</span>
                    <h2 class="title text-center">{{ $flashSale['title'] ?? 'Flash Sale' }}</h2>
                </div>

                @if ($countdownEndsAt)
                    <div class="shopw-flash-sale-countdown" data-flash-sale-countdown="{{ $countdownEndsAt }}">
                        @foreach ([
                            'days' => 'Ngày',
                            'hours' => 'Giờ',
                            'minutes' => 'Phút',
                            'seconds' => 'Giây',
                        ] as $unit => $label)
                            <div class="shopw-flash-sale-pill">
                                <strong data-flash-unit="{{ $unit }}">00</strong>
                                <span>{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="shopw-flash-sale-track" data-flash-sale-track>
                @foreach ($cards as $card)
                    <article class="shopw-flash-sale-card">
                        <a href="{{ $card['url'] }}" class="shopw-flash-sale-link">
                            <span class="shopw-flash-sale-media">
                                @if (!empty($card['discount_percent']))
                                    <span class="shopw-flash-sale-discount">-{{ $card['discount_percent'] }}%</span>
                                @endif
                                <img src="{{ $card['image'] }}" alt="{{ $card['name'] }}" loading="lazy">
                            </span>

                            <span class="shopw-flash-sale-body">
                                <strong class="shopw-flash-sale-title">{{ $card['name'] }}</strong>

                                <span class="shopw-flash-sale-pricing">
                                    <strong>{{ number_format((float) $card['final_price'], 0, ',', '.') }}đ</strong>
                                    @if (!empty($card['discount_percent']) && (float) ($card['price'] ?? 0) > (float) ($card['final_price'] ?? 0))
                                        <small>{{ number_format((float) $card['price'], 0, ',', '.') }}đ</small>
                                    @endif
                                </span>

                                @if (!empty($card['specs']))
                                    <span class="shopw-flash-sale-specs">
                                        @foreach ($card['specs'] as $spec)
                                            <span class="shopw-flash-sale-spec">
                                                <i class="fa {{ $spec['icon'] }}"></i>
                                                <span>{{ $spec['label'] }}</span>
                                            </span>
                                        @endforeach
                                    </span>
                                @endif

                                @if ($card['sold_quantity'] !== null)
                                    <span class="shopw-flash-sale-progress-copy">
                                        <i class="fa fa-fire"></i>
                                        <span>Đã bán {{ number_format((int) $card['sold_quantity']) }} sản phẩm</span>
                                    </span>
                                    <span class="shopw-flash-sale-progress" aria-hidden="true">
                                        <span style="width: {{ max(0, min(100, (int) ($card['sold_progress'] ?? 0))) }}%"></span>
                                    </span>
                                @endif
                            </span>
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="shopw-flash-sale-foot">
                <a href="{{ $flashSale['cta_url'] ?? url('/tim-kiem') }}" class="shopw-flash-sale-cta">
                    {{ $flashSale['cta_label'] ?? 'Xem tất cả deal' }}
                </a>
            </div>
        </div>
    </section>
@endif
