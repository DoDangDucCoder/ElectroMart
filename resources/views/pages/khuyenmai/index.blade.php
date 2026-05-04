@extends('layouts.home-tech')

@section('body_class', 'g-home tech-home promo-page')

@push('styles')
<link href="{{ asset('frontend/css/promo-page.css') }}" rel="stylesheet">
@endpush

@section('content')
<main class="promo-main">
    {{-- Breadcrumb --}}
    <nav class="promo-breadcrumb" aria-label="Breadcrumb">
        <div class="container">
            <ol>
                <li><a href="{{ url('/trang-chu') }}">Trang chủ</a></li>
                <li class="active">Khuyến mãi</li>
            </ol>
        </div>
    </nav>

    {{-- Hero Banner --}}
    <section class="promo-hero" id="promo-hero">
        <div class="promo-hero-bg"></div>
        <div class="container">
            <div class="promo-hero-inner">
                <div class="promo-hero-content">
                    <h1 class="promo-hero-title">
                        <span>BỘ SƯU TẬP &</span>
                        <span>KHUYẾN MÃI HOT</span>
                    </h1>
                    <p class="promo-hero-desc">Laptop, PC & Phụ kiện chính hãng<br>giá tốt – ưu đãi mỗi ngày!</p>
                    <div class="promo-hero-countdown" data-tech-countdown aria-label="Đếm ngược">
                        <span class="promo-hero-countdown-label">Kết thúc sau:</span>
                        <div class="promo-hero-countdown-boxes">
                            <div class="promo-countdown-box">
                                <strong data-countdown-unit="days">06</strong>
                                <small>NGÀY</small>
                            </div>
                            <span class="promo-countdown-sep">:</span>
                            <div class="promo-countdown-box">
                                <strong data-countdown-unit="hours">23</strong>
                                <small>GIỜ</small>
                            </div>
                            <span class="promo-countdown-sep">:</span>
                            <div class="promo-countdown-box">
                                <strong data-countdown-unit="minutes">41</strong>
                                <small>PHÚT</small>
                            </div>
                            <span class="promo-countdown-sep">:</span>
                            <div class="promo-countdown-box">
                                <strong data-countdown-unit="seconds">58</strong>
                                <small>GIÂY</small>
                            </div>
                        </div>
                    </div>
                    <div class="promo-hero-actions">
                        <a href="{{ url('/san-pham') }}" class="promo-btn promo-btn--primary">Mua ngay</a>
                        <a href="#promo-collections" class="promo-btn promo-btn--outline">Xem bộ sưu tập</a>
                    </div>
                </div>
                <div class="promo-hero-visual">
                    <img src="{{ asset('uploads/slider/banner342.jpg') }}" alt="Khuyến mãi HOT" class="promo-hero-img">
                </div>
            </div>
        </div>
    </section>

    {{-- Category Quick Tabs --}}
    <section class="promo-cats" id="promo-cats">
        <div class="container">
            <div class="promo-cats-grid">
                @php
                    $promoCategories = [
                        ['name' => 'Laptop sinh viên', 'icon' => 'fa-graduation-cap', 'query' => 'laptop sinh viên', 'img' => 'uploads/post/smartphone12.jpg'],
                        ['name' => 'Gaming setup', 'icon' => 'fa-gamepad', 'query' => 'gaming', 'img' => 'uploads/post/smartphone24.jpg'],
                        ['name' => 'Phụ kiện hot', 'icon' => 'fa-headphones', 'query' => 'phụ kiện', 'img' => 'uploads/post/smartphone31.jpg'],
                        ['name' => 'Văn phòng tối ưu', 'icon' => 'fa-briefcase', 'query' => 'laptop văn phòng', 'img' => 'uploads/post/smartphone12.jpg'],
                        ['name' => 'Deal cuối tuần', 'icon' => 'fa-percent', 'query' => 'deal', 'img' => null],
                        ['name' => 'Flash Sale', 'icon' => 'fa-bolt', 'query' => 'flash sale', 'img' => null],
                    ];
                @endphp
                @foreach ($promoCategories as $cat)
                    <a href="{{ url('/tim-kiem?keywords_submit=' . urlencode($cat['query'])) }}" class="promo-cat-item">
                        <span class="promo-cat-icon-wrap">
                            @if (!empty($cat['img']))
                                <img src="{{ asset($cat['img']) }}" alt="{{ $cat['name'] }}">
                            @else
                                <i class="fa {{ $cat['icon'] }}"></i>
                            @endif
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
                <a href="{{ url('/tim-kiem?keywords_submit=laptop+sinh+viên') }}" class="promo-banner-card promo-banner--blue">
                    <div class="promo-banner-content">
                        <span class="promo-banner-tag">Back to School</span>
                        <h3>LÊN LỚP<br>THẬT CHẤT</h3>
                        <div class="promo-banner-discount">Giảm đến<br><strong>35%</strong></div>
                        <span class="promo-banner-cta">Xem ngay <i class="fa fa-arrow-right"></i></span>
                    </div>
                </a>
                <a href="{{ url('/tim-kiem?keywords_submit=gaming') }}" class="promo-banner-card promo-banner--dark">
                    <div class="promo-banner-content">
                        <span class="promo-banner-tag">GAMING COLLECTION</span>
                        <h3>SỨC MẠNH<br>DẪN ĐẦU</h3>
                        <div class="promo-banner-discount">Giảm đến<br><strong>40%</strong></div>
                        <span class="promo-banner-cta">Xem ngay <i class="fa fa-arrow-right"></i></span>
                    </div>
                </a>
                <a href="{{ url('/tim-kiem?keywords_submit=làm+việc+tại+nhà') }}" class="promo-banner-card promo-banner--teal">
                    <div class="promo-banner-content">
                        <span class="promo-banner-tag">LÀM VIỆC TẠI NHÀ</span>
                        <h3>HIỆU QUẢ<br>MỖI NGÀY</h3>
                        <div class="promo-banner-discount">Giảm đến<br><strong>30%</strong></div>
                        <span class="promo-banner-cta">Xem ngay <i class="fa fa-arrow-right"></i></span>
                    </div>
                </a>
                <a href="{{ url('/tim-kiem?keywords_submit=phụ+kiện') }}" class="promo-banner-card promo-banner--purple">
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
                    <div class="promo-flash-timer" data-tech-countdown>
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
                                'compact' => true,
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
                        ['name' => 'Laptop học tập', 'desc' => 'Gọn nhẹ, bền bỉ, hiệu năng ổn định.', 'icon' => 'fa-book', 'query' => 'laptop học tập'],
                        ['name' => 'Laptop gaming', 'desc' => 'Hiệu năng mạnh mẽ, chiến game đỉnh cao.', 'icon' => 'fa-gamepad', 'query' => 'laptop gaming'],
                        ['name' => 'PC streaming', 'desc' => 'Cấu hình tối ưu, stream mượt mọi game.', 'icon' => 'fa-video-camera', 'query' => 'pc streaming'],
                        ['name' => 'Thiết bị làm việc tại nhà', 'desc' => 'Tăng năng suất, làm việc thoải mái.', 'icon' => 'fa-home', 'query' => 'thiết bị văn phòng'],
                    ];
                @endphp
                @foreach ($collections as $col)
                    <a href="{{ url('/tim-kiem?keywords_submit=' . urlencode($col['query'])) }}" class="promo-collection-card">
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
            <div class="promo-coupon-grid">
                @php
                    $coupons = [
                        ['code' => 'STUDY10', 'title' => 'Giảm 10% tối đa 300K', 'desc' => 'Đơn từ 2.000.000đ', 'exp' => 'HSD: 25/05/2025', 'color' => 'blue'],
                        ['code' => 'GAMER 300K', 'title' => 'Giảm 300K cho đơn từ 10 triệu', 'desc' => '', 'exp' => 'HSD: 20/05/2025', 'color' => 'dark'],
                        ['code' => 'FREESHIP', 'title' => 'Miễn phí vận chuyển', 'desc' => 'Đơn từ 500.000đ', 'exp' => 'HSD: 31/05/2025', 'color' => 'teal'],
                        ['code' => 'ACCESS5', 'title' => 'Giảm 5% cho phụ kiện', 'desc' => 'Đơn từ 250.000đ', 'exp' => 'HSD: 31/05/2025', 'color' => 'orange'],
                    ];
                @endphp
                @foreach ($coupons as $coupon)
                    <div class="promo-coupon-card promo-coupon--{{ $coupon['color'] }}">
                        <div class="promo-coupon-left">
                            <strong class="promo-coupon-code">{{ $coupon['code'] }}</strong>
                            <p class="promo-coupon-title">{{ $coupon['title'] }}</p>
                            @if ($coupon['desc'])
                                <span class="promo-coupon-desc">{{ $coupon['desc'] }}</span>
                            @endif
                        </div>
                        <div class="promo-coupon-right">
                            <button type="button" class="promo-coupon-copy" data-coupon="{{ $coupon['code'] }}">Sao chép</button>
                            <span class="promo-coupon-exp">{{ $coupon['exp'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Sản phẩm trong khuyến mãi --}}
    <section class="promo-section promo-products" id="promo-products">
        <div class="container">
            <div class="promo-section-head">
                <h2>Sản phẩm trong khuyến mãi</h2>
                <a href="{{ url('/san-pham') }}" class="promo-see-all">Xem tất cả <i class="fa fa-arrow-right"></i></a>
            </div>
            <div class="promo-product-grid">
                @foreach ($promoProducts as $product)
                    <div class="promo-product-col">
                        @include('partials.home.product-card', [
                            'product' => $product,
                            'compact' => true,
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
