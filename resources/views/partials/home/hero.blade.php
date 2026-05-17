@php
    $heroSlides = collect([
        [
            'src' => asset('assets/home/hero/hero-11.png'),
            'webp' => asset('assets/home/hero/hero-11.webp'),
            'alt' => 'Ưu đãi công nghệ nổi bật',
        ],
        [
            'src' => asset('assets/home/hero/hero-10.png'),
            'webp' => asset('assets/home/hero/hero-10.webp'),
            'alt' => 'Banner khuyến mãi thiết bị công nghệ',
        ],
        [
            'src' => asset('assets/home/hero/hero-9.png'),
            'webp' => asset('assets/home/hero/hero-9.webp'),
            'alt' => 'Banner sản phẩm công nghệ local',
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
                                    <picture>
                                        <source srcset="{{ $slide['webp'] }}" type="image/webp">
                                        <img
                                            src="{{ $slide['src'] }}"
                                            alt="{{ $slide['alt'] }}"
                                            class="shopw-home-hero__image"
                                            decoding="async"
                                            fetchpriority="{{ $loop->first ? 'high' : 'low' }}"
                                            loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                                    </picture>
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
