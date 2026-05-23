@extends('layouts.home-tech')

@push('styles')
<link href="{{ asset('frontend/css/contact-page.css') }}?v=2.1" rel="stylesheet">
@endpush

@section('content')
    @php
        $info = $contact->first() ?? null;
        $brandName = config('services.storefront_brand.name', 'DangDuc');
        $supportConfig = config('services.storefront_support', []);
        $supportPhoneDisplay = $supportConfig['phone_display'] ?? '1900 1234';
        $supportPhoneHref = $supportConfig['phone_href'] ?? 'tel:19001234';
        $supportEmail = $supportConfig['email'] ?? 'support@dangduc.vn';
        $storeAddress = $supportConfig['address'] ?? '123 Nguyễn Văn Cừ, Long Biên, Hà Nội, Việt Nam';
        $storeAddressShort = $supportConfig['address_short'] ?? '123 Nguyễn Văn Cừ, Long Biên, Hà Nội';
        $storeName = $supportConfig['store_name'] ?? ($brandName . ' Store');
        $mapEmbed = $info?->info_map;

        $subjects = [
            'product' => 'Tư vấn sản phẩm',
            'order' => 'Hỗ trợ đơn hàng',
            'warranty' => 'Bảo hành / Đổi trả',
            'partnership' => 'Hợp tác kinh doanh',
            'other' => 'Vấn đề khác',
        ];

        $socialLinks = [
            ['icon' => 'fa-facebook', 'url' => $supportConfig['facebook'] ?? '#', 'label' => 'Facebook'],
            ['icon' => 'fa-youtube-play', 'url' => $supportConfig['youtube'] ?? '#', 'label' => 'YouTube'],
            ['icon' => 'fa-music', 'url' => $supportConfig['tiktok'] ?? '#', 'label' => 'TikTok'],
            ['icon' => 'fa-instagram', 'url' => $supportConfig['instagram'] ?? '#', 'label' => 'Instagram'],
        ];

        $workingHours = [
            ['label' => 'Thứ 2 - Thứ 6:', 'value' => '8:00 - 21:00'],
            ['label' => 'Thứ 7 - Chủ nhật:', 'value' => '8:00 - 21:00'],
            ['label' => 'Ngày lễ:', 'value' => '8:00 - 17:00'],
        ];

        $faqs = [
            ['q' => 'Làm thế nào để đặt hàng?', 'a' => 'Bạn có thể đặt hàng trực tiếp trên website bằng cách thêm sản phẩm vào giỏ hàng và làm theo các bước thanh toán, hoặc gọi hotline ' . $supportPhoneDisplay . ' để được hỗ trợ.'],
            ['q' => 'Phương thức thanh toán nào được chấp nhận?', 'a' => 'Chúng tôi chấp nhận thanh toán qua VISA, Mastercard, Momo, ZaloPay, chuyển khoản ngân hàng và thanh toán khi nhận hàng (COD).'],
            ['q' => 'Thời gian giao hàng là bao lâu?', 'a' => 'Nội thành Hà Nội giao trong 2-4 giờ, các tỉnh khác từ 1-3 ngày làm việc tùy khu vực.'],
            ['q' => 'Sản phẩm có được bảo hành không?', 'a' => 'Tất cả sản phẩm đều được bảo hành chính hãng theo quy định của nhà sản xuất, từ 12-36 tháng tùy sản phẩm.'],
            ['q' => 'Chính sách đổi trả như thế nào?', 'a' => 'Bạn có thể đổi trả trong vòng 7 ngày nếu sản phẩm còn nguyên tem, không có dấu hiệu sử dụng và đầy đủ phụ kiện.'],
            ['q' => 'Làm sao để theo dõi đơn hàng?', 'a' => 'Đăng nhập tài khoản và truy cập mục "Lịch sử đơn hàng" để xem trạng thái đơn hàng của bạn theo thời gian thực.'],
        ];
    @endphp

    <main class="dd-contact">
        <div class="container">
            @include('partials.storefront.breadcrumb', [
                'class' => 'dd-contact__breadcrumb',
                'separator' => '›',
                'items' => [
                    [
                        'label' => 'Liên hệ',
                        'current' => true,
                    ],
                ],
            ])

            <section class="dd-contact__hero">
                <div class="dd-contact__hero-copy">
                    <h1 class="dd-contact__hero-title">
                        LIÊN HỆ VỚI <span>{{ strtoupper($brandName) }}</span>
                    </h1>
                    <p class="dd-contact__hero-subtitle">Chúng tôi luôn sẵn sàng hỗ trợ bạn!</p>
                    <p class="dd-contact__hero-desc">
                        Mọi thắc mắc, góp ý hoặc cần hỗ trợ, đừng ngần ngại liên hệ
                        với đội ngũ của chúng tôi.
                    </p>
                </div>
                <div class="dd-contact__hero-art" aria-hidden="true">
                    <svg viewBox="0 0 360 280" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="dd-c-blob" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0" stop-color="#cffafe"/>
                                <stop offset="1" stop-color="#e0f2fe" stop-opacity="0.4"/>
                            </linearGradient>
                            <linearGradient id="dd-c-env" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0" stop-color="#22d3ee"/>
                                <stop offset="1" stop-color="#0891b2"/>
                            </linearGradient>
                            <linearGradient id="dd-c-plane" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0" stop-color="#67e8f9"/>
                                <stop offset="1" stop-color="#0e7490"/>
                            </linearGradient>
                        </defs>
                        <path d="M40 220 C 0 150 30 60 130 40 C 230 20 320 60 340 130 C 360 200 280 260 200 260 C 120 260 80 280 40 220 Z" fill="url(#dd-c-blob)"/>
                        <circle cx="80" cy="80" r="22" fill="#fff" opacity="0.9"/>
                        <circle cx="80" cy="80" r="4" fill="#06b6d4"/>
                        <circle cx="68" cy="80" r="3" fill="#06b6d4"/>
                        <circle cx="92" cy="80" r="3" fill="#06b6d4"/>
                        <path d="M70 95 q10 12 20 0" stroke="#06b6d4" stroke-width="2" fill="none" stroke-linecap="round"/>
                        <g transform="translate(120 110)">
                            <rect x="0" y="0" width="180" height="120" rx="10" fill="url(#dd-c-env)"/>
                            <path d="M0 10 L90 78 L180 10" stroke="#fff" stroke-width="3" fill="#ecfeff"/>
                            <path d="M0 10 L90 78 L180 10 L180 0 L0 0 Z" fill="#fff"/>
                            <rect x="20" y="30" width="140" height="6" rx="3" fill="#cffafe"/>
                            <rect x="20" y="46" width="100" height="6" rx="3" fill="#cffafe"/>
                            <rect x="20" y="62" width="120" height="6" rx="3" fill="#cffafe"/>
                        </g>
                        <g transform="translate(220 30) rotate(-15)">
                            <path d="M0 30 L90 0 L70 60 L48 42 L52 60 Z" fill="url(#dd-c-plane)"/>
                            <path d="M48 42 L70 60" stroke="#0e7490" stroke-width="1.5" fill="none"/>
                        </g>
                        <path d="M210 22 q14 -6 30 4" stroke="#67e8f9" stroke-width="2" fill="none" stroke-linecap="round" stroke-dasharray="3 5"/>
                        <path d="M198 38 q22 -8 42 4" stroke="#67e8f9" stroke-width="2" fill="none" stroke-linecap="round" stroke-dasharray="3 5" opacity="0.7"/>
                    </svg>
                </div>
            </section>

            <section class="dd-contact__main">
                <aside class="dd-card dd-contact__info">
                    <h2 class="dd-card__title">THÔNG TIN LIÊN HỆ</h2>

                    <ul class="dd-info-list">
                        <li>
                            <span class="dd-info-list__icon"><i class="fa fa-map-marker"></i></span>
                            <div class="dd-info-list__body">
                                <strong>Địa chỉ</strong>
                                <p>{{ $storeAddress }}</p>
                            </div>
                        </li>
                        <li>
                            <span class="dd-info-list__icon"><i class="fa fa-phone"></i></span>
                            <div class="dd-info-list__body">
                                <strong>Hotline</strong>
                                <p><a href="{{ $supportPhoneHref }}">{{ $supportPhoneDisplay }}</a></p>
                                <small>(8:00 - 21:00, Tất cả các ngày)</small>
                            </div>
                        </li>
                        <li>
                            <span class="dd-info-list__icon"><i class="fa fa-envelope-o"></i></span>
                            <div class="dd-info-list__body">
                                <strong>Email</strong>
                                <p><a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></p>
                                <small>Phản hồi trong vòng 24h</small>
                            </div>
                        </li>
                        <li>
                            <span class="dd-info-list__icon"><i class="fa fa-gift"></i></span>
                            <div class="dd-info-list__body">
                                <strong>Cửa hàng</strong>
                                <p>Hệ thống cửa hàng</p>
                                <a class="dd-info-list__link" href="{{ url('/lien-he') }}">Xem danh sách cửa hàng</a>
                            </div>
                        </li>
                        <li>
                            <span class="dd-info-list__icon"><i class="fa fa-clock-o"></i></span>
                            <div class="dd-info-list__body">
                                <strong>Kết nối với chúng tôi</strong>
                                <div class="dd-info-list__socials">
                                    @foreach ($socialLinks as $social)
                                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener" aria-label="{{ $social['label'] }}">
                                            <i class="fa {{ $social['icon'] }}"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                    </ul>
                </aside>

                <section class="dd-card dd-contact__form">
                    <h2 class="dd-card__title">GỬI LIÊN HỆ CHO CHÚNG TÔI</h2>

                    @if (session('message'))
                        <div class="alert alert-success">{{ session('message') }}</div>
                    @elseif (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form class="dd-form" action="{{ url('/lien-he') }}" method="POST" novalidate>
                        @csrf
                        <div class="dd-form__row">
                            <div class="dd-form__field">
                                <label for="dd-name">Họ và tên <span>*</span></label>
                                <input type="text" id="dd-name" name="full_name" value="{{ old('full_name') }}" placeholder="Nhập họ và tên của bạn" required>
                            </div>
                            <div class="dd-form__field">
                                <label for="dd-phone">Số điện thoại</label>
                                <input type="tel" id="dd-phone" name="phone" value="{{ old('phone') }}" placeholder="Nhập số điện thoại">
                            </div>
                        </div>

                        <div class="dd-form__field">
                            <label for="dd-email">Email <span>*</span></label>
                            <input type="email" id="dd-email" name="email" value="{{ old('email') }}" placeholder="Nhập email của bạn" required>
                        </div>

                        <div class="dd-form__field dd-form__field--select">
                            <label for="dd-subject">Chủ đề</label>
                            <select id="dd-subject" name="subject">
                                <option value="">Chọn chủ đề</option>
                                @foreach ($subjects as $key => $label)
                                    <option value="{{ $key }}" @selected(old('subject') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <i class="fa fa-angle-down dd-form__select-caret"></i>
                        </div>

                        <div class="dd-form__field">
                            <label for="dd-message">Nội dung <span>*</span></label>
                            <textarea id="dd-message" name="message" rows="5" placeholder="Nhập nội dung bạn muốn gửi" required>{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="dd-btn dd-btn--primary">
                            <i class="fa fa-paper-plane"></i>
                            <span>Gửi liên hệ</span>
                        </button>

                        <p class="dd-form__policy">
                            Bằng việc gửi thông tin, bạn đã đồng ý với
                            <a href="{{ url('/cham-soc-khach-hang/bao-mat') }}">chính sách bảo mật</a>
                            của chúng tôi.
                        </p>
                    </form>
                </section>
            </section>

            <section class="dd-contact__lower">
                <div class="dd-contact__lower-left">
                    <div class="dd-card dd-hours">
                        <div class="dd-hours__head">
                            <span class="dd-info-list__icon"><i class="fa fa-clock-o"></i></span>
                            <h2 class="dd-card__title dd-card__title--inline">THỜI GIAN LÀM VIỆC</h2>
                        </div>
                        <ul class="dd-hours__list">
                            @foreach ($workingHours as $row)
                                <li>
                                    <span>{{ $row['label'] }}</span>
                                    <strong>{{ $row['value'] }}</strong>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="dd-card dd-support-247">
                        <span class="dd-info-list__icon"><i class="fa fa-headphones"></i></span>
                        <div>
                            <strong>Tiếp nhận yêu cầu 24/7</strong>
                            <p>Chúng tôi tiếp nhận tin nhắn mọi lúc và phản hồi trong khung giờ làm việc.</p>
                        </div>
                    </div>
                </div>

                <div class="dd-card dd-map">
                    @if ($mapEmbed)
                        <div class="dd-map__frame">{!! $mapEmbed !!}</div>
                    @else
                        <iframe class="dd-map__frame"
                            src="https://www.google.com/maps?q={{ urlencode($storeAddress) }}&hl=vi&z=15&output=embed"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen></iframe>
                    @endif
                    <div class="dd-map__pin" aria-hidden="true">
                        <span class="dd-map__pin-dot"></span>
                    </div>
                    <div class="dd-map__store">
                        <span class="dd-map__store-icon"><i class="fa fa-map-marker"></i></span>
                        <div>
                            <strong>{{ $storeName }}</strong>
                            <small>{{ $storeAddressShort }}</small>
                        </div>
                    </div>
                </div>
            </section>

            <section class="dd-faq">
                <header class="dd-faq__head">
                    <h2>CÂU HỎI THƯỜNG GẶP</h2>
                    <span class="dd-faq__divider"></span>
                </header>
                <div class="dd-faq__grid">
                    @foreach ($faqs as $i => $faq)
                        <details class="dd-faq__item">
                            <summary>
                                <span>{{ $faq['q'] }}</span>
                                <span class="dd-faq__toggle" aria-hidden="true">
                                    <i class="fa fa-plus"></i>
                                </span>
                            </summary>
                            <p>{{ $faq['a'] }}</p>
                        </details>
                    @endforeach
                </div>
            </section>
        </div>
    </main>
@endsection
