@php
    $cards = collect($cards ?? [])->values();
    $title = $title ?? 'Sản phẩm nổi bật';
    $description = $description ?? 'Những mẫu đang được quan tâm nhiều trên storefront.';
    $ctaLabel = $ctaLabel ?? 'Xem thêm sản phẩm';
    $ctaUrl = $ctaUrl ?? url('/tim-kiem');
@endphp

@if ($cards->isNotEmpty())
    @once
        <style type="text/css">
            .g-home .shopw-featured-showcase {
                margin-top: 26px;
            }

            .g-home .shopw-featured-showcase-shell {
                position: relative;
                overflow: hidden;
                padding: 30px 24px 28px;
                border-radius: 32px;
                background:
                    radial-gradient(circle at top left, rgba(255, 228, 230, 0.9), transparent 26%),
                    radial-gradient(circle at top right, rgba(219, 234, 254, 0.9), transparent 24%),
                    linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
                border: 1px solid rgba(226, 232, 240, 0.96);
                box-shadow: 0 24px 50px rgba(15, 23, 42, 0.08);
            }

            .g-home .shopw-featured-showcase-shell::before,
            .g-home .shopw-featured-showcase-shell::after {
                content: "";
                position: absolute;
                border-radius: 999px;
                pointer-events: none;
            }

            .g-home .shopw-featured-showcase-shell::before {
                inset: 28px auto auto -88px;
                width: 220px;
                height: 220px;
                border: 22px solid rgba(14, 165, 233, 0.12);
            }

            .g-home .shopw-featured-showcase-shell::after {
                inset: auto -90px -96px auto;
                width: 240px;
                height: 240px;
                border: 24px solid rgba(251, 113, 133, 0.1);
            }

            .g-home .shopw-featured-showcase-head {
                position: relative;
                z-index: 1;
                display: grid;
                justify-items: center;
                gap: 14px;
                margin-bottom: 22px;
                text-align: center;
            }

            .g-home .shopw-featured-showcase-kicker {
                display: inline-flex;
                align-items: center;
                min-height: 32px;
                padding: 0 14px;
                border-radius: 999px;
                background: rgba(213, 0, 24, 0.08);
                color: #d50018;
                font-size: 11px;
                font-weight: 900;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            .g-home .shopw-featured-showcase-emblem {
                display: grid;
                justify-items: center;
                gap: 10px;
                max-width: 540px;
            }

            .g-home .shopw-featured-showcase-logo {
                width: auto;
                max-width: min(240px, 72vw);
                max-height: 82px;
                object-fit: contain;
                filter: drop-shadow(0 12px 26px rgba(15, 23, 42, 0.12));
            }

            .g-home .shopw-featured-showcase-mark {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 62px;
                height: 62px;
                border-radius: 20px;
                background: linear-gradient(135deg, rgba(14, 165, 233, 0.14), rgba(213, 0, 24, 0.16));
                color: #d50018;
                font-size: 22px;
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.84);
            }

            .g-home .shopw-featured-showcase-emblem strong {
                color: #0f172a;
                font-size: clamp(28px, 3vw, 38px);
                font-weight: 900;
                line-height: 1;
                letter-spacing: -0.04em;
            }

            .g-home .shopw-featured-showcase-emblem p {
                margin: 0;
                color: #64748b;
                font-size: 14px;
                line-height: 1.7;
            }

            .g-home .shopw-featured-showcase-cta {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 48px;
                padding: 0 20px;
                border-radius: 999px;
                background: linear-gradient(135deg, #ef4444 0%, #d50018 100%);
                color: #fff;
                font-size: 14px;
                font-weight: 800;
                text-decoration: none;
                box-shadow: 0 16px 30px rgba(213, 0, 24, 0.2);
            }

            .g-home .shopw-featured-showcase-cta:hover {
                color: #fff;
                transform: translateY(-1px);
            }

            .g-home .shopw-featured-showcase-grid {
                position: relative;
                z-index: 1;
                display: grid;
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 16px;
            }

            .g-home .shopw-featured-showcase-card {
                position: relative;
                min-width: 0;
                border-radius: 28px;
                background: rgba(255, 255, 255, 0.96);
                border: 1px solid rgba(226, 232, 240, 0.96);
                box-shadow: 0 20px 34px rgba(15, 23, 42, 0.08);
                transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
            }

            .g-home .shopw-featured-showcase-card:hover {
                transform: translateY(-4px);
                border-color: rgba(248, 113, 113, 0.32);
                box-shadow: 0 26px 40px rgba(15, 23, 42, 0.12);
            }

            .g-home .shopw-featured-showcase-card--hot {
                background: linear-gradient(180deg, rgba(255, 247, 248, 0.98) 0%, rgba(255, 255, 255, 0.98) 100%);
            }

            .g-home .shopw-featured-showcase-card--cool {
                background: linear-gradient(180deg, rgba(240, 249, 255, 0.98) 0%, rgba(255, 255, 255, 0.98) 100%);
            }

            .g-home .shopw-featured-showcase-card--warm {
                background: linear-gradient(180deg, rgba(255, 251, 235, 0.98) 0%, rgba(255, 255, 255, 0.98) 100%);
            }

            .g-home .shopw-featured-showcase-card-link {
                display: grid;
                gap: 12px;
                height: 100%;
                padding: 16px 16px 18px;
                color: inherit;
                text-decoration: none;
            }

            .g-home .shopw-featured-showcase-ribbon {
                position: absolute;
                top: 14px;
                left: 0;
                z-index: 2;
                display: inline-flex;
                align-items: center;
                min-height: 28px;
                padding: 0 14px 0 16px;
                border-radius: 0 999px 999px 0;
                background: linear-gradient(135deg, #fb7185 0%, #d50018 100%);
                color: #fff;
                font-size: 11px;
                font-weight: 900;
                letter-spacing: 0.04em;
                text-transform: uppercase;
                box-shadow: 0 12px 22px rgba(213, 0, 24, 0.22);
            }

            .g-home .shopw-featured-showcase-card.is-mock .shopw-featured-showcase-ribbon {
                background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
            }

            .g-home .shopw-featured-showcase-favorite {
                position: absolute;
                top: 16px;
                right: 16px;
                z-index: 2;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.94);
                border: 1px solid rgba(248, 113, 113, 0.22);
                color: #fb7185;
                font-size: 16px;
                box-shadow: 0 10px 18px rgba(15, 23, 42, 0.08);
            }

            .g-home .shopw-featured-showcase-media {
                display: flex;
                align-items: center;
                justify-content: center;
                aspect-ratio: 1 / 1;
                padding: 18px;
                border-radius: 22px;
                background:
                    linear-gradient(135deg, rgba(248, 250, 252, 0.92), rgba(255, 255, 255, 0.98));
                overflow: hidden;
            }

            .g-home .shopw-featured-showcase-media img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                filter: drop-shadow(0 12px 18px rgba(15, 23, 42, 0.12));
            }

            .g-home .shopw-featured-showcase-copy {
                display: grid;
                gap: 5px;
                min-width: 0;
            }

            .g-home .shopw-featured-showcase-brand {
                color: #0ea5e9;
                font-size: 11px;
                font-weight: 900;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            .g-home .shopw-featured-showcase-copy strong {
                color: #0f172a;
                font-size: 16px;
                font-weight: 800;
                line-height: 1.45;
                display: -webkit-box;
                overflow: hidden;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                min-height: calc(1.45em * 2);
            }

            .g-home .shopw-featured-showcase-copy small {
                color: #64748b;
                font-size: 12px;
                font-weight: 700;
                line-height: 1.6;
            }

            .g-home .shopw-featured-showcase-pricing {
                display: flex;
                align-items: baseline;
                flex-wrap: wrap;
                gap: 8px;
            }

            .g-home .shopw-featured-showcase-pricing strong {
                color: #d50018;
                font-size: 24px;
                font-weight: 900;
                line-height: 1;
            }

            .g-home .shopw-featured-showcase-pricing small {
                color: #94a3b8;
                font-size: 12px;
                font-weight: 800;
                text-decoration: line-through;
            }

            .g-home .shopw-featured-showcase-tags {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            .g-home .shopw-featured-showcase-tags em {
                display: inline-flex;
                align-items: center;
                min-height: 28px;
                padding: 0 10px;
                border-radius: 999px;
                background: #f8fafc;
                border: 1px solid rgba(226, 232, 240, 0.96);
                color: #475569;
                font-size: 11px;
                font-style: normal;
                font-weight: 800;
            }

            .g-home .shopw-featured-showcase-note {
                color: #475569;
                font-size: 12px;
                font-weight: 700;
                line-height: 1.65;
            }

            @media (max-width: 1199px) {
                .g-home .shopw-featured-showcase-grid {
                    grid-template-columns: repeat(3, minmax(0, 1fr));
                }
            }

            @media (max-width: 767px) {
                .g-home .shopw-featured-showcase-shell {
                    padding: 22px 16px 18px;
                    border-radius: 26px;
                }

                .g-home .shopw-featured-showcase-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 12px;
                }

                .g-home .shopw-featured-showcase-copy strong {
                    font-size: 14px;
                }

                .g-home .shopw-featured-showcase-pricing strong {
                    font-size: 20px;
                }
            }

            @media (max-width: 479px) {
                .g-home .shopw-featured-showcase-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    @endonce

    <section class="features_items shopw-home-section shopw-featured-showcase" aria-label="{{ $title }}">
        <div class="shopw-featured-showcase-shell">
            <div class="shopw-featured-showcase-head">
                <span class="shopw-featured-showcase-kicker">Tâm điểm storefront</span>
                <div class="shopw-featured-showcase-emblem">
                    @if (!empty($logo))
                        <img src="{{ $logo }}" alt="{{ $title }}" class="shopw-featured-showcase-logo">
                    @else
                        <span class="shopw-featured-showcase-mark" aria-hidden="true">
                            <i class="fa fa-diamond"></i>
                        </span>
                    @endif
                    <strong>{{ $title }}</strong>
                    <p>{{ $description }}</p>
                </div>
                <a href="{{ $ctaUrl }}" class="shopw-featured-showcase-cta">{{ $ctaLabel }}</a>
            </div>

            <div class="shopw-featured-showcase-grid">
                @foreach ($cards as $card)
                    <article
                        class="shopw-featured-showcase-card shopw-featured-showcase-card--{{ $card['tone'] ?? 'core' }}{{ !empty($card['is_mock']) ? ' is-mock' : '' }}">
                        <span class="shopw-featured-showcase-ribbon">{{ $card['badge'] ?? 'Nổi bật' }}</span>
                        <span class="shopw-featured-showcase-favorite" aria-hidden="true">
                            <i class="fa fa-heart-o"></i>
                        </span>

                        <a href="{{ $card['url'] ?? url('/tim-kiem') }}" class="shopw-featured-showcase-card-link">
                            <span class="shopw-featured-showcase-media">
                                <img src="{{ $card['image'] }}" alt="{{ $card['name'] }}">
                            </span>

                            <span class="shopw-featured-showcase-copy">
                                <span class="shopw-featured-showcase-brand">{{ $card['brand'] }}</span>
                                <strong>{{ $card['name'] }}</strong>
                                <small>{{ $card['category'] }}</small>
                            </span>

                            <span class="shopw-featured-showcase-pricing">
                                <strong>{{ $card['price'] }}</strong>
                                @if (!empty($card['original_price']))
                                    <small>{{ $card['original_price'] }}</small>
                                @endif
                            </span>

                            @if (!empty($card['chips']))
                                <span class="shopw-featured-showcase-tags">
                                    @foreach ($card['chips'] as $chip)
                                        <em>{{ $chip }}</em>
                                    @endforeach
                                </span>
                            @endif

                            <span class="shopw-featured-showcase-note">{{ $card['note'] }}</span>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
