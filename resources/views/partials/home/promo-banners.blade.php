@php
    $promoBanners = [
        [
            'class' => 'tech-promo-card--gaming',
            'url' => url('/danh-muc/pc'),
            'eyebrow' => 'PC GAMING',
            'title' => 'Hiệu năng đỉnh cao',
            'description' => 'Tối ưu cho game thủ',
            'image' => 'assets/home/banners/pc-gaming.svg',
        ],
        [
            'class' => 'tech-promo-card--monitor',
            'url' => url('/danh-muc/man-hinh'),
            'eyebrow' => 'MONITOR',
            'title' => 'Hình ảnh sắc nét',
            'description' => 'Mượt mà từng khung hình',
            'image' => 'assets/home/banners/monitor.svg',
        ],
        [
            'class' => 'tech-promo-card--laptop',
            'url' => url('/danh-muc/laptop'),
            'eyebrow' => 'LAPTOP',
            'title' => 'Linh hoạt học tập',
            'description' => 'Gọn nhẹ cho công việc',
            'image' => 'assets/home/banners/laptop.svg',
        ],
        [
            'class' => 'tech-promo-card--phone',
            'url' => url('/danh-muc/dien-thoai'),
            'eyebrow' => 'PHONE',
            'title' => 'Kết nối mọi lúc',
            'description' => 'Smartphone chính hãng',
            'image' => 'assets/home/banners/phone.svg',
        ],
        [
            'class' => 'tech-promo-card--network',
            'url' => url('/danh-muc/network'),
            'eyebrow' => 'NETWORK',
            'title' => 'Mạng ổn định',
            'description' => 'Router và Wi-Fi mesh',
            'image' => 'assets/home/banners/network.svg',
        ],
        [
            'class' => 'tech-promo-card--office',
            'url' => url('/danh-muc/thiet-bi-van-phong'),
            'eyebrow' => 'OFFICE',
            'title' => 'Thiết bị văn phòng',
            'description' => 'In ấn và làm việc hiệu quả',
            'image' => 'assets/home/banners/office.svg',
        ],
        [
            'class' => 'tech-promo-card--accessory',
            'url' => url('/danh-muc/gaming-gear'),
            'eyebrow' => 'PHỤ KIỆN GAMING',
            'title' => 'Đa dạng lựa chọn',
            'description' => 'Chuột, phím, tai nghe',
            'image' => 'assets/home/banners/gaming-accessories.svg',
        ],
    ];
@endphp

<section class="tech-section tech-promo-banners-section tech-promo-section">
    <div class="container">
        <div class="row tech-promo-grid">
            @foreach ($promoBanners as $banner)
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <a href="{{ $banner['url'] }}" class="tech-promo-card {{ $banner['class'] }}">
                        <div class="tech-promo-content">
                            <span class="tech-promo-eyebrow">{{ $banner['eyebrow'] }}</span>
                            <h3>{{ $banner['title'] }}</h3>
                            <p>{{ $banner['description'] }}</p>
                            <span class="tech-promo-btn">Xem ngay <i class="fa fa-arrow-right"></i></span>
                        </div>
                        <img src="{{ asset($banner['image']) }}" alt="{{ $banner['eyebrow'] }}" loading="lazy" decoding="async">
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
