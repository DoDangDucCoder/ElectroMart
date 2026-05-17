@extends('layouts.home-tech')

@section('body_class', 'g-home tech-home promo-page')

@push('styles')
<link href="{{ asset('frontend/css/promo-page.css') }}?v=2.1" rel="stylesheet">
@endpush


@section('content')
<main class="promo-main">
    <div class="container">
        @include('partials.storefront.breadcrumb', [
            'class' => 'promo-breadcrumb',
            'items' => [
                [
                    'label' => 'Khuyến mãi',
                    'current' => true,
                ],
            ],
        ])
    </div>

    {{-- Hero Banner --}}
    <section class="promo-banner-hero" id="promo-hero">
        <div class="container">
            <a href="{{ url('/san-pham') }}" class="promo-banner-hero-link" aria-label="Xem sản phẩm khuyến mãi">
                <img
                    src="{{ asset('frontend/images/banners/hero-student-promo.png') }}"
                    alt="Bộ sưu tập khuyến mãi"
                    class="promo-banner-hero-img">
            </a>
        </div>
    </section>

    {{-- Category Quick Tabs --}}
    <section class="promo-cats" id="promo-cats">
        <div class="container">
            <div class="promo-cats-grid">
                @php
                    $promoCategories = [
                        ['name' => 'Laptop sinh viên', 'icon' => 'fa-laptop', 'url' => url('/danh-muc/laptop')],
                        ['name' => 'Gaming setup', 'icon' => 'fa-gamepad', 'url' => url('/danh-muc/gaming-gear')],
                        ['name' => 'Phụ kiện hot', 'icon' => 'fa-headphones', 'url' => url('/danh-muc/phu-kien')],
                        ['name' => 'Văn phòng tối ưu', 'icon' => 'fa-briefcase', 'url' => url('/danh-muc/laptop-van-phong')],
                        ['name' => 'Deal cuối tuần', 'icon' => 'fa-tags', 'url' => url('/san-pham?' . http_build_query(['sort' => 'sale_desc']))],
                        ['name' => 'Flash Sale', 'icon' => 'fa-bolt', 'url' => url('/san-pham?' . http_build_query(['sort' => 'sale_desc']))],
                    ];
                @endphp
                @foreach ($promoCategories as $cat)
                    <a href="{{ $cat['url'] }}" class="promo-cat-item">
                        <span class="promo-cat-icon-wrap">
                            <i class="fa {{ $cat['icon'] }}"></i>
                        </span>
                        <span class="promo-cat-name">{{ $cat['name'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Ưu đãi nổi bật --}}
    <section class="promo-section promo-featured" id="promo-featured">
        <div class="container">
            <div class="promo-section-head">
                <h2>Ưu đãi nổi bật</h2>
                <a href="{{ url('/san-pham') }}" class="promo-see-all">Xem tất cả ưu đãi <i class="fa fa-arrow-right"></i></a>
            </div>
            <div class="promo-banner-grid">
                <a href="{{ url('/danh-muc/laptop') }}" class="promo-banner-card promo-banner--blue">
                    <div class="promo-banner-content">
                        <span class="promo-banner-tag">Back to School</span>
                        <h3>LÊN LỚP<br>THẬT CHẤT</h3>
                        <div class="promo-banner-discount">Giảm đến<br><strong>35%</strong></div>
                        <span class="promo-banner-cta">Xem ngay <i class="fa fa-arrow-right"></i></span>
                    </div>
                </a>
                <a href="{{ url('/danh-muc/pc') }}" class="promo-banner-card promo-banner--dark">
                    <div class="promo-banner-content">
                        <span class="promo-banner-tag">GAMING COLLECTION</span>
                        <h3>SỨC MẠNH<br>DẪN ĐẦU</h3>
                        <div class="promo-banner-discount">Giảm đến<br><strong>40%</strong></div>
                        <span class="promo-banner-cta">Xem ngay <i class="fa fa-arrow-right"></i></span>
                    </div>
                </a>
                <a href="{{ url('/danh-muc/laptop-van-phong') }}" class="promo-banner-card promo-banner--teal">
                    <div class="promo-banner-content">
                        <span class="promo-banner-tag">LÀM VIỆC TẠI NHÀ</span>
                        <h3>HIỆU QUẢ<br>MỖI NGÀY</h3>
                        <div class="promo-banner-discount">Giảm đến<br><strong>30%</strong></div>
                        <span class="promo-banner-cta">Xem ngay <i class="fa fa-arrow-right"></i></span>
                    </div>
                </a>
                <a href="{{ url('/danh-muc/phu-kien') }}" class="promo-banner-card promo-banner--purple">
                    <div class="promo-banner-content">
                        <span class="promo-banner-tag">COMBO PHỤ KIỆN</span>
                        <h3>MUA NHIỀU<br>GIẢM SÂU</h3>
                        <div class="promo-banner-discount">Giảm đến<br><strong>25%</strong></div>
                        <span class="promo-banner-cta">Xem ngay <i class="fa fa-arrow-right"></i></span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- Flash Sale --}}
    <section class="promo-section promo-flash" id="promo-flash">
        <div class="container">
            <div class="promo-section-head">
                <div class="promo-flash-header">
                    <h2><i class="fa fa-bolt"></i> Flash Sale</h2>
                    <div class="promo-flash-timer"
                        data-tech-countdown
                        data-countdown-type="daily"
                        data-countdown-cycle-hours="24"
                        data-countdown-storage-key="promo-flash-sale">
                        <span class="promo-flash-timer-label"><i class="fa fa-clock-o"></i> Kết thúc sau:</span>
                        <strong data-countdown-unit="hours">02</strong>
                        <span>:</span>
                        <strong data-countdown-unit="minutes">23</strong>
                        <span>:</span>
                        <strong data-countdown-unit="seconds">41</strong>
                    </div>
                </div>
                <a href="{{ url('/san-pham') }}" class="promo-see-all">Xem tất cả <i class="fa fa-arrow-right"></i></a>
            </div>

            <div class="tech-sale-viewport" data-tech-sale-viewport>
                <button type="button" class="tech-sale-nav tech-sale-prev" data-tech-sale-prev aria-label="Quay lại">
                    <i class="fa fa-angle-left"></i>
                </button>
                <div class="tech-sale-track" data-tech-sale-track>
                    @foreach ($flashSaleProducts as $product)
                        <div class="tech-sale-item">
                            @include('partials.home.product-card', [
                                'product' => $product,
                            ])
                        </div>
                    @endforeach
                </div>
                <button type="button" class="tech-sale-nav tech-sale-next" data-tech-sale-next aria-label="Xem thêm">
                    <i class="fa fa-angle-right"></i>
                </button>
            </div>
        </div>
    </section>

    {{-- Bộ sưu tập theo nhu cầu --}}
    <section class="promo-section promo-collections" id="promo-collections">
        <div class="container">
            <div class="promo-section-head">
                <h2>Bộ sưu tập theo nhu cầu</h2>
                <a href="{{ url('/san-pham') }}" class="promo-see-all">Xem tất cả <i class="fa fa-arrow-right"></i></a>
            </div>
            <div class="promo-collection-grid">
                @php
                    $collections = [
                        ['name' => 'Laptop học tập', 'desc' => 'Gọn nhẹ, bền bỉ, hiệu năng ổn định.', 'icon' => 'fa-book', 'url' => url('/danh-muc/laptop')],
                        ['name' => 'Laptop gaming', 'desc' => 'Hiệu năng mạnh mẽ, chiến game đỉnh cao.', 'icon' => 'fa-gamepad', 'url' => url('/danh-muc/laptop-gaming')],
                        ['name' => 'PC streaming', 'desc' => 'Cấu hình tối ưu, stream mượt mọi game.', 'icon' => 'fa-video-camera', 'url' => url('/danh-muc/pc')],
                        ['name' => 'Thiết bị làm việc tại nhà', 'desc' => 'Tăng năng suất, làm việc thoải mái.', 'icon' => 'fa-home', 'url' => url('/danh-muc/laptop-van-phong')],
                    ];
                @endphp
                @foreach ($collections as $col)
                    <a href="{{ $col['url'] }}" class="promo-collection-card">
                        <span class="promo-collection-icon"><i class="fa {{ $col['icon'] }}"></i></span>
                        <div class="promo-collection-body">
                            <h3>{{ $col['name'] }}</h3>
                            <p>{{ $col['desc'] }}</p>
                            <span class="promo-collection-cta">Xem ngay <i class="fa fa-arrow-right"></i></span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Mã giảm giá --}}
    <section class="promo-section promo-coupons" id="promo-coupons">
        <div class="container">
            <div class="promo-section-head">
                <h2>Mã giảm giá dành cho bạn</h2>
                <a href="{{ url('/san-pham') }}" class="promo-see-all">Xem tất cả <i class="fa fa-arrow-right"></i></a>
            </div>
            @php
                $coupons = collect($availableCoupons ?? []);
                $couponColors = ['blue', 'dark', 'teal', 'orange'];
                $formatCouponValue = function ($coupon) {
                    return match ($coupon->type) {
                        'percent' => 'Giảm ' . rtrim(rtrim(number_format((float) $coupon->value, 2, '.', ''), '0'), '.') . '%',
                        'shipping' => 'Miễn phí vận chuyển',
                        default => 'Giảm ' . number_format((float) $coupon->value, 0, ',', '.') . 'đ',
                    };
                };
            @endphp
            @if ($coupons->isNotEmpty())
                <div class="promo-coupon-grid">
                    @foreach ($coupons as $coupon)
                        @php
                            $couponDesc = $coupon->min_order_total
                                ? 'Đơn từ ' . number_format((float) $coupon->min_order_total, 0, ',', '.') . 'đ'
                                : 'Áp dụng theo điều kiện mã';
                            $couponExp = $coupon->expires_at
                                ? 'HSD: ' . $coupon->expires_at->format('d/m/Y')
                                : 'Không giới hạn thời hạn';
                        @endphp
                        <div class="promo-coupon-card promo-coupon--{{ $couponColors[$loop->index % count($couponColors)] }}">
                            <div class="promo-coupon-left">
                                <strong class="promo-coupon-code">{{ $coupon->code }}</strong>
                                <p class="promo-coupon-title">{{ $coupon->name ?: $formatCouponValue($coupon) }}</p>
                                <span class="promo-coupon-desc">{{ $couponDesc }}</span>
                            </div>
                            <div class="promo-coupon-right">
                                <button type="button" class="promo-coupon-copy" data-coupon="{{ $coupon->code }}">Sao chép</button>
                                <span class="promo-coupon-exp">{{ $couponExp }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                @include('partials.storefront.empty-state', [
                    'class' => 'promo-coupon-empty',
                    'icon' => 'fa fa-ticket',
                    'title' => 'Hiện chưa có mã khuyến mãi khả dụng',
                    'message' => 'Các ưu đãi sản phẩm vẫn được cập nhật trong danh sách bên dưới.',
                    'actionText' => 'Xem sản phẩm',
                    'actionUrl' => url('/san-pham'),
                ])
            @endif
        </div>
    </section>

    {{-- Sản phẩm trong khuyến mãi --}}
    <section class="promo-section promo-products" id="promo-products">
        <div class="container">
            <div class="promo-section-head">
                <h2>Sản phẩm trong khuyến mãi</h2>
                <a href="{{ url('/san-pham') }}" class="promo-see-all">Xem tất cả <i class="fa fa-arrow-right"></i></a>
            </div>
            @php
                $visiblePromoProducts = collect($promoProducts ?? []);
                $visiblePromoProducts = $visiblePromoProducts
                    ->take($visiblePromoProducts->count() >= 8 ? 8 : min(4, $visiblePromoProducts->count()));
            @endphp
            <div class="promo-product-grid">
                @foreach ($visiblePromoProducts as $product)
                    <div class="promo-product-col">
                        @include('partials.home.product-card', [
                            'product' => $product,
                        ])
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Policy Strip --}}
    <section class="promo-section promo-policy-strip">
        <div class="container">
            <div class="promo-policy-row">
                @foreach ($policyItems as $policy)
                    <div class="promo-policy-item">
                        <span class="promo-policy-icon"><i class="fa {{ $policy['icon'] }}"></i></span>
                        <div class="promo-policy-text">
                            <strong>{{ $policy['title'] }}</strong>
                            <span>{{ $policy['description'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</main>

<script>
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.promo-coupon-copy');
        if (!btn) return;
        var code = btn.getAttribute('data-coupon');
        if (!code) return;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(code).then(function() {
                btn.textContent = 'Đã sao chép!';
                btn.classList.add('is-copied');
                setTimeout(function() {
                    btn.textContent = 'Sao chép';
                    btn.classList.remove('is-copied');
                }, 2000);
            });
        } else {
            var textarea = document.createElement('textarea');
            textarea.value = code;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            btn.textContent = 'Đã sao chép!';
            btn.classList.add('is-copied');
            setTimeout(function() {
                btn.textContent = 'Sao chép';
                btn.classList.remove('is-copied');
            }, 2000);
        }
    });
</script>
@endsection
