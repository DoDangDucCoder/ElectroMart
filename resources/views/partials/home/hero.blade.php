@php
    $heroSlides = collect([
        [
            'src' => asset('assets/home/hero/hero-tech-main.svg'),
            'alt' => 'Công nghệ chính hãng cho mọi nhu cầu',
            'kicker' => 'TECH STORE',
            'title' => 'Công nghệ chính hãng cho mọi nhu cầu',
            'summary' => 'Laptop, PC, màn hình, thiết bị mạng và phụ kiện được chọn lọc cho học tập, làm việc và giải trí.',
            'primary_label' => 'Khám phá sản phẩm',
            'primary_url' => url('/san-pham'),
            'secondary_label' => 'Xem khuyến mãi',
            'secondary_url' => url('/khuyen-mai'),
        ],
        [
            'src' => asset('assets/home/hero/hero-gaming-setup.svg'),
            'alt' => 'Nâng cấp góc gaming của bạn',
            'kicker' => 'GAMING SETUP',
            'title' => 'Nâng cấp góc gaming của bạn',
            'summary' => 'PC Gaming, màn hình, bàn phím, chuột và phụ kiện hiệu năng cao.',
            'primary_label' => 'Xem thiết bị gaming',
            'primary_url' => url('/tim-kiem?keywords_submit=' . urlencode('pc gaming')),
            'secondary_label' => 'Xem màn hình',
            'secondary_url' => url('/tim-kiem?keywords_submit=' . urlencode('màn hình')),
        ],
        [
            'src' => asset('assets/home/hero/hero-office-network.svg'),
            'alt' => 'Thiết bị văn phòng và mạng ổn định',
            'kicker' => 'OFFICE & NETWORK',
            'title' => 'Thiết bị văn phòng & mạng ổn định',
            'summary' => 'Router WiFi, máy in, thiết bị văn phòng cho không gian làm việc hiện đại.',
            'primary_label' => 'Xem giải pháp văn phòng',
            'primary_url' => url('/tim-kiem?keywords_submit=' . urlencode('máy in')),
            'secondary_label' => 'Thiết bị mạng',
            'secondary_url' => url('/tim-kiem?keywords_submit=' . urlencode('router')),
        ],
    ]);
@endphp

<section id="slider" class="tech-home-hero-slider">
    <div class="container">
        <div class="shopw-home-hero">
            <div class="shopw-home-hero__main">
                <div id="slider-carousel"
                    class="carousel slide"
                    data-ride="carousel"
                    data-interval="7000"
                    data-pause="hover"
                    data-hero-slide-count="{{ $heroSlides->count() }}">

                    <div class="carousel-inner">
                        @foreach ($heroSlides as $slide)
                            <div class="item {{ $loop->first ? 'active' : '' }}">
                                <div class="shopw-home-hero__image-wrapper">
                                    <img src="{{ $slide['src'] }}" alt="{{ $slide['alt'] }}" class="shopw-home-hero__image">
                                    <div class="shopw-home-hero__caption">
                                        <span class="shopw-home-hero__pill">{{ $slide['kicker'] }}</span>
                                        <h1 class="shopw-home-hero__title">{{ $slide['title'] }}</h1>
                                        <p class="shopw-home-hero__desc">{{ $slide['summary'] }}</p>
                                        <div class="shopw-home-hero__actions">
                                            <a href="{{ $slide['primary_url'] }}" class="btn-shopw btn-shopw--primary">
                                                {{ $slide['primary_label'] }}
                                            </a>
                                            <a href="{{ $slide['secondary_url'] }}" class="btn-shopw btn-shopw--ghost">
                                                {{ $slide['secondary_label'] }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($heroSlides->count() > 1)
                        <ol class="carousel-indicators shopw-home-hero__indicators">
                            @foreach ($heroSlides as $key => $slide)
                                <li data-target="#slider-carousel"
                                    data-slide-to="{{ $key }}"
                                    class="{{ $key === 0 ? 'active' : '' }}"></li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </div>

            <div class="shopw-home-hero__aside">
                <div class="shopw-hero-util-card">
                    <div class="shopw-hero-util-icon">
                        <i class="fa fa-credit-card"></i>
                    </div>
                    <div class="shopw-hero-util-body">
                        <strong>Trả góp 0%</strong>
                        <span>Duyệt nhanh online</span>
                    </div>
                </div>
                <div class="shopw-hero-util-card">
                    <div class="shopw-hero-util-icon">
                        <i class="fa fa-shield"></i>
                    </div>
                    <div class="shopw-hero-util-body">
                        <strong>Bảo hành chính hãng</strong>
                        <span>An tâm sử dụng</span>
                    </div>
                </div>
                <div class="shopw-hero-util-card">
                    <div class="shopw-hero-util-icon">
                        <i class="fa fa-refresh"></i>
                    </div>
                    <div class="shopw-hero-util-body">
                        <strong>1 đổi 1 trong 30 ngày</strong>
                        <span>Lỗi kỹ thuật</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
