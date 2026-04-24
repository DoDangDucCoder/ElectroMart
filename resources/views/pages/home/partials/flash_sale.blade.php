@php
    $cards = collect($flashSale['products'] ?? [])->values()->take(6);
    $flashSaleKicker = trim((string) ($flashSale['kicker'] ?? ''));
    $flashSaleTitle = trim((string) ($flashSale['title'] ?? ''));
    $flashSaleTitle = $flashSaleTitle !== '' ? $flashSaleTitle : 'FLASH SALE';
    $flashSaleHeading = trim($flashSaleKicker !== '' ? $flashSaleKicker . ' ' . $flashSaleTitle : $flashSaleTitle);
    $flashSaleCtaLabel = filled($flashSale['cta_label'] ?? null) ? $flashSale['cta_label'] : 'Xem tất cả';
    $flashSaleCtaUrl = filled($flashSale['cta_url'] ?? null) ? $flashSale['cta_url'] : url('/tim-kiem');
    $countdownEndsAt = $flashSale['countdown_ends_at'] ?? null;
    if (! filled($countdownEndsAt)) {
        $countdownEndsAt = now()->addHours(4)->addMinutes(7)->addSeconds(46)->toIso8601String();
    }
    try {
        $countdownEnd = \Illuminate\Support\Carbon::parse($countdownEndsAt);
    } catch (\Throwable) {
        $countdownEnd = now()->addHours(4)->addMinutes(7)->addSeconds(46);
        $countdownEndsAt = $countdownEnd->toIso8601String();
    }
    $totalSeconds = max(0, $countdownEnd->getTimestamp() - now()->getTimestamp());
    $countdownUnits = [
        'hours' => sprintf('%02d', intdiv($totalSeconds % 86400, 3600)),
        'minutes' => sprintf('%02d', intdiv($totalSeconds % 3600, 60)),
        'seconds' => sprintf('%02d', $totalSeconds % 60),
    ];
@endphp

@if ($cards->isNotEmpty())
    @once
        <style type="text/css">
            .g-home .shopw-flash-sale-section {
                margin-top: 22px;
            }

            .g-home .shopw-flash-sale-shell {
                padding: 16px 18px 18px;
                border-radius: 2px;
                background: #fff;
                border: 1px solid #f1f2f4;
                box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
            }

            .g-home .shopw-flash-sale-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 14px;
                margin-bottom: 14px;
                flex-wrap: wrap;
            }

            .g-home .shopw-flash-sale-brand {
                display: flex;
                align-items: center;
                gap: 14px;
                min-width: 0;
                flex: 1 1 auto;
            }

            .g-home .shopw-flash-sale-title {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                margin: 0;
                color: #e31d2a;
                font-size: 24px;
                font-weight: 900;
                line-height: 1;
                letter-spacing: 0.02em;
                text-transform: uppercase;
                white-space: nowrap;
            }

            .g-home .shopw-flash-sale-title .fa-bolt {
                color: #ffc400;
                font-size: 22px;
                text-shadow: 0 1px 0 rgba(227, 29, 42, 0.35);
                transform: skew(-6deg);
            }

            .g-home .shopw-flash-sale-countdown {
                display: inline-flex;
                align-items: center;
                gap: 0;
                flex: 0 0 auto;
            }

            .g-home .shopw-flash-sale-pill {
                position: relative;
                min-width: 40px;
                height: 32px;
                padding: 0 8px;
                border-radius: 4px;
                background: linear-gradient(180deg, #1a1a1a 0%, #0a0a0a 55%, #141414 100%);
                color: #f8fafc;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 1px solid #2d2d2d;
                box-shadow:
                    inset 0 1px 0 rgba(255, 255, 255, 0.12),
                    inset 0 -2px 0 rgba(0, 0, 0, 0.45),
                    0 2px 4px rgba(0, 0, 0, 0.2);
                overflow: hidden;
            }

            .g-home .shopw-flash-sale-pill strong {
                position: relative;
                z-index: 1;
                color: inherit;
                font-size: 15px;
                font-weight: 900;
                line-height: 1;
                font-variant-numeric: tabular-nums;
                letter-spacing: 0.04em;
            }

            .g-home .shopw-flash-sale-pill span {
                display: none;
            }

            .g-home .shopw-flash-sale-separator {
                color: #111827;
                font-size: 18px;
                font-weight: 900;
                line-height: 1;
                padding: 0 7px;
            }

            .g-home .shopw-flash-sale-see-all {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                flex: 0 0 auto;
                color: #e31d2a;
                font-size: 14px;
                font-weight: 700;
                text-decoration: none;
                white-space: nowrap;
                min-width: max-content;
            }

            .g-home .shopw-flash-sale-see-all:hover {
                color: #b91520;
            }

            .g-home .shopw-flash-sale-see-all-mark {
                font-size: 15px;
                line-height: 1;
            }

            .g-home .shopw-flash-sale-carousel {
                position: relative;
                display: flex;
                align-items: stretch;
                gap: 6px;
                padding: 0;
                user-select: none;
            }

            .g-home .shopw-flash-sale-track-outer {
                flex: 1 1 auto;
                min-width: 0;
                overflow: hidden;
            }

            .g-home .shopw-flash-sale-track {
                display: grid;
                grid-template-columns: repeat(6, minmax(0, 1fr));
                gap: 10px;
                overflow-x: auto;
                overflow-y: hidden;
                padding: 2px 0 4px;
                scroll-snap-type: x proximity;
                scrollbar-width: none;
                -ms-overflow-style: none;
                -webkit-overflow-scrolling: touch;
            }

            .g-home .shopw-flash-sale-track::-webkit-scrollbar {
                display: none;
            }

            .g-home .shopw-flash-sale-card {
                min-width: 0;
            }

            .g-home .shopw-flash-sale-link {
                display: grid;
                grid-template-rows: auto 1fr;
                height: 100%;
                overflow: hidden;
                color: inherit;
                text-decoration: none;
                background: #fff;
                border: 1px solid #edf0f3;
                border-radius: 4px;
                box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
                transition: transform 0.16s ease, box-shadow 0.16s ease;
            }

            .g-home .shopw-flash-sale-link:hover {
                transform: translateY(-1px);
                box-shadow: 0 5px 14px rgba(15, 23, 42, 0.12);
            }

            .g-home .shopw-flash-sale-thumb-shell {
                position: relative;
                display: grid;
                grid-template-rows: minmax(0, 1fr) auto;
                overflow: hidden;
                aspect-ratio: 1 / 1.06;
                background: #fff;
            }

            .g-home .shopw-flash-sale-tag {
                position: absolute;
                top: 7px;
                left: 0;
                z-index: 3;
                display: inline-flex;
                align-items: center;
                min-height: 19px;
                padding: 0 7px;
                border-radius: 0 2px 2px 0;
                background: #ee4d2d;
                color: #fff;
                font-size: 10px;
                font-weight: 800;
                line-height: 1;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.12);
            }

            .g-home .shopw-flash-sale-discount {
                position: absolute;
                top: 0;
                right: 0;
                z-index: 4;
                width: 58px;
                height: 58px;
                background: linear-gradient(145deg, #fff0a3 0%, #ffd428 42%, #f5a623 100%);
                clip-path: polygon(0 0, 100% 0, 100% 100%);
                pointer-events: none;
                filter: drop-shadow(-1px 2px 3px rgba(0, 0, 0, 0.12));
            }

            .g-home .shopw-flash-sale-discount strong {
                position: absolute;
                top: 11px;
                right: 1px;
                width: 46px;
                text-align: center;
                transform: rotate(42deg);
                transform-origin: center;
                font-size: 12px;
                font-weight: 900;
                line-height: 1;
                color: #b42318;
                letter-spacing: -0.03em;
                text-shadow: 0 1px 0 rgba(255, 255, 255, 0.35);
            }

            .g-home .shopw-flash-sale-media {
                display: flex;
                align-items: center;
                justify-content: center;
                min-width: 0;
                min-height: 0;
                padding: 8px 10px;
                background:
                    radial-gradient(ellipse 85% 70% at 50% 45%, #ffffff 0%, #f3f4f6 72%, #e8eaef 100%);
            }

            .g-home .shopw-flash-sale-media img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                object-position: center;
                display: block;
                filter: drop-shadow(0 6px 14px rgba(15, 23, 42, 0.08));
            }

            .g-home .shopw-flash-sale-badges {
                display: flex;
                align-items: stretch;
                justify-content: space-between;
                gap: 6px;
                width: 100%;
                min-width: 0;
                min-height: 26px;
                padding: 3px 8px;
                box-sizing: border-box;
                background: linear-gradient(180deg, #0b74f5 0%, #075bdc 100%);
                border-top: 1px solid rgba(255, 255, 255, 0.35);
            }

            .g-home .shopw-flash-sale-badges em {
                display: flex;
                align-items: center;
                justify-content: center;
                min-width: 0;
                flex: 1 1 0;
                padding: 0 2px;
                color: #fff;
                font-size: 7px;
                font-style: normal;
                font-weight: 900;
                line-height: 1.1;
                text-align: center;
                text-transform: uppercase;
                word-break: break-word;
            }

            .g-home .shopw-flash-sale-badges em:first-child {
                flex: 0 0 auto;
                min-width: 2.4em;
                color: #fff7c2;
                font-size: 12px;
                font-weight: 900;
                letter-spacing: 0.02em;
            }

            .g-home .shopw-flash-sale-title-card,
            .g-home .shopw-flash-sale-meta {
                display: none;
            }

            .g-home .shopw-flash-sale-body {
                display: grid;
                gap: 8px;
                align-content: end;
                min-width: 0;
                padding: 10px 8px 10px;
                text-align: center;
            }

            .g-home .shopw-flash-sale-pricing {
                display: block;
                min-height: 24px;
                line-height: 1;
                text-align: center;
            }

            .g-home .shopw-flash-sale-pricing strong {
                color: #b83019;
                font-size: 20px;
                font-weight: 500;
                line-height: 1.1;
                font-variant-numeric: tabular-nums;
            }

            .g-home .shopw-flash-sale-pricing small {
                display: none;
            }

            .g-home .shopw-flash-sale-progress-shell {
                position: relative;
                display: grid;
                align-items: center;
                min-width: 0;
            }

            .g-home .shopw-flash-sale-progress {
                position: relative;
                display: block;
                width: 100%;
                height: 17px;
                overflow: hidden;
                border-radius: 4px;
                background: rgba(245, 197, 24, 0.35);
            }

            .g-home .shopw-flash-sale-progress > span {
                display: block;
                height: 100%;
                border-radius: inherit;
                background: linear-gradient(90deg, #f5d547 0%, #f0b429 52%, #e9a30d 100%);
            }

            .g-home .shopw-flash-sale-progress-shell.is-sold-meter .shopw-flash-sale-progress {
                background: rgba(240, 93, 43, 0.25);
            }

            .g-home .shopw-flash-sale-progress-shell.is-sold-meter .shopw-flash-sale-progress > span {
                background: linear-gradient(90deg, #ff9a4d 0%, #f05d2b 100%);
            }

            .g-home .shopw-flash-sale-progress-label {
                position: absolute;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                padding: 0 6px;
                color: #1f2937;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: 0.02em;
                line-height: 1;
                text-overflow: ellipsis;
                text-transform: uppercase;
                white-space: nowrap;
            }

            .g-home .shopw-flash-sale-progress-shell.is-sold-meter .shopw-flash-sale-progress-label {
                color: #fff;
                text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
            }

            .g-home .shopw-flash-sale-nav {
                flex: 0 0 auto;
                align-self: center;
                width: 32px;
                height: 72px;
                margin: 0;
                padding: 0;
                border: 1px solid #e5e7eb;
                border-radius: 4px;
                background: #fff;
                color: #374151;
                font-size: 22px;
                font-weight: 700;
                line-height: 1;
                cursor: pointer;
                box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
                transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
            }

            .g-home .shopw-flash-sale-nav:hover:not(:disabled) {
                background: #f9fafb;
                border-color: #d1d5db;
                color: #111827;
            }

            .g-home .shopw-flash-sale-nav:disabled {
                opacity: 0.35;
                cursor: not-allowed;
            }

            @media (max-width: 1199px) {
                .g-home .shopw-flash-sale-track {
                    grid-template-columns: repeat(6, minmax(152px, 1fr));
                    scroll-snap-type: x mandatory;
                }

                .g-home .shopw-flash-sale-card {
                    scroll-snap-align: start;
                }
            }

            @media (max-width: 767px) {
                .g-home .shopw-flash-sale-shell {
                    padding: 14px 12px 13px;
                }

                .g-home .shopw-flash-sale-head {
                    gap: 8px;
                    margin-bottom: 12px;
                }

                .g-home .shopw-flash-sale-brand {
                    flex-wrap: wrap;
                    gap: 8px;
                }

                .g-home .shopw-flash-sale-title {
                    font-size: 17px;
                }

                .g-home .shopw-flash-sale-title .fa-bolt {
                    font-size: 16px;
                }

                .g-home .shopw-flash-sale-pill {
                    min-width: 34px;
                    height: 28px;
                    padding: 0 6px;
                }

                .g-home .shopw-flash-sale-pill strong {
                    font-size: 13px;
                }

                .g-home .shopw-flash-sale-see-all {
                    font-size: 13px;
                }

                .g-home .shopw-flash-sale-track {
                    grid-template-columns: repeat(6, minmax(142px, 1fr));
                    gap: 8px;
                }

                .g-home .shopw-flash-sale-nav {
                    width: 28px;
                    height: 64px;
                    font-size: 18px;
                }

                .g-home .shopw-flash-sale-body {
                    padding: 9px 7px 9px;
                }

                .g-home .shopw-flash-sale-pricing strong {
                    font-size: 17px;
                }

                .g-home .shopw-flash-sale-progress-label {
                    font-size: 7px;
                }
            }
        </style>
    @endonce

    <section class="features_items shopw-home-section shopw-flash-sale-section">
        <div class="shopw-flash-sale-shell">
            <div class="shopw-flash-sale-head">
                <div class="shopw-flash-sale-brand">
                    <h2 class="shopw-flash-sale-title">
                        <i class="fa fa-bolt" aria-hidden="true"></i>
                        <span>{{ $flashSaleHeading }}</span>
                    </h2>

                    <div class="shopw-flash-sale-countdown" data-flash-sale-countdown="{{ $countdownEndsAt }}">
                        @foreach ($countdownUnits as $unit => $value)
                            @if (! $loop->first)
                                <span class="shopw-flash-sale-separator" aria-hidden="true">:</span>
                            @endif

                            <div class="shopw-flash-sale-pill">
                                <strong data-flash-unit="{{ $unit }}">{{ $value }}</strong>
                                <span>{{ $unit }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <a href="{{ $flashSaleCtaUrl }}" class="shopw-flash-sale-see-all">
                    <span>{{ $flashSaleCtaLabel }}</span>
                    <span class="shopw-flash-sale-see-all-mark" aria-hidden="true">&gt;</span>
                </a>
            </div>

            <div class="shopw-flash-sale-carousel" data-flash-sale-carousel>
                <button type="button" class="shopw-flash-sale-nav shopw-flash-sale-nav--prev" data-flash-nav="prev" aria-label="Xem sản phẩm trước">&lt;</button>
                <div class="shopw-flash-sale-track-outer">
                    <div class="shopw-flash-sale-track" data-flash-sale-track>
                        @foreach ($cards as $card)
                            @include('pages.home.partials.flash_sale_card', ['card' => $card])
                        @endforeach
                    </div>
                </div>
                <button type="button" class="shopw-flash-sale-nav shopw-flash-sale-nav--next" data-flash-nav="next" aria-label="Xem sản phẩm tiếp theo">&gt;</button>
            </div>
        </div>
    </section>
@endif
