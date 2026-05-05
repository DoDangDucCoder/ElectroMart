@php
    $promoBanners = [
        [
            'class' => 'tech-promo-card--gaming',
            'query' => 'pc gaming',
            'eyebrow' => 'PC GAMING',
            'title' => 'Hiệu năng đỉnh cao',
            'description' => 'Tối ưu cho game thủ',
            'image' => 'assets/home/banners/pc-gaming.svg',
        ],
        [
            'class' => 'tech-promo-card--monitor',
            'query' => 'màn hình',
            'eyebrow' => 'MONITOR',
            'title' => 'Hình ảnh sắc nét',
            'description' => 'Mượt mà từng khung hình',
            'image' => 'assets/home/banners/monitor.svg',
        ],
        [
            'class' => 'tech-promo-card--laptop',
            'query' => 'laptop',
            'eyebrow' => 'LAPTOP',
            'title' => 'Linh hoạt học tập',
            'description' => 'Gọn nhẹ cho công việc',
            'image' => 'assets/home/banners/laptop.svg',
        ],
        [
            'class' => 'tech-promo-card--phone',
            'query' => 'điện thoại',
            'eyebrow' => 'PHONE',
            'title' => 'Kết nối mọi lúc',
            'description' => 'Smartphone chính hãng',
            'image' => 'assets/home/banners/phone.svg',
        ],
        [
            'class' => 'tech-promo-card--network',
            'query' => 'router',
            'eyebrow' => 'NETWORK',
            'title' => 'Mạng ổn định',
            'description' => 'Router và Wi-Fi mesh',
            'image' => 'assets/home/banners/network.svg',
        ],
        [
            'class' => 'tech-promo-card--office',
            'query' => 'máy in',
            'eyebrow' => 'OFFICE',
            'title' => 'Thiết bị văn phòng',
            'description' => 'In ấn và làm việc hiệu quả',
            'image' => 'assets/home/banners/office.svg',
        ],
        [
            'class' => 'tech-promo-card--accessory',
            'query' => 'phụ kiện gaming',
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
                    <a href="{{ url('/tim-kiem?keywords_submit=' . urlencode($banner['query'])) }}" class="tech-promo-card {{ $banner['class'] }}">
                        <div class="tech-promo-content">
                            <span class="tech-promo-eyebrow">{{ $banner['eyebrow'] }}</span>
                            <h3>{{ $banner['title'] }}</h3>
                            <p>{{ $banner['description'] }}</p>
                            <span class="tech-promo-btn">Xem ngay <i class="fa fa-arrow-right"></i></span>
                        </div>
                        <img src="{{ asset($banner['image']) }}" alt="{{ $banner['eyebrow'] }}">
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
