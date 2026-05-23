@extends('layouts.home-tech')

@php
    $meta_desc = $meta_desc ?? 'Chăm sóc khách hàng DangDuc - hỗ trợ đơn hàng, bảo hành, đổi trả và tư vấn sản phẩm công nghệ';
    $meta_keywords = $meta_keywords ?? 'chăm sóc khách hàng, hỗ trợ DangDuc, bảo hành, đổi trả, hỗ trợ đơn hàng';
    $meta_title = $meta_title ?? 'Chăm sóc khách hàng - DangDuc';
    $url_canonical = $url_canonical ?? url('/cham-soc-khach-hang');
@endphp

@push('styles')
<link href="{{ asset('frontend/css/support-page.css') }}?v=2.1" rel="stylesheet">
@endpush

@section('content')
    @php
        $brandName = config('services.storefront_brand.name', 'DangDuc');
        $supportConfig = config('services.storefront_support', []);
        $supportPhoneDisplay = $supportConfig['phone_display'] ?? '1900 1234';
        $supportPhoneHref = $supportConfig['phone_href'] ?? 'tel:19001234';
        $supportEmail = $supportConfig['email'] ?? 'support@dangduc.vn';

        $quickActions = [
            [
                'icon' => 'fa-comments-o',
                'title' => 'Chat trực tuyến',
                'desc' => 'Trò chuyện ngay với nhân viên hỗ trợ',
                'href' => '#contact-now',
            ],
            [
                'icon' => 'fa-phone',
                'title' => 'Gọi hotline',
                'desc' => $supportPhoneDisplay,
                'meta' => '8:00 - 22:00 hằng ngày',
                'href' => $supportPhoneHref,
            ],
            [
                'icon' => 'fa-envelope-o',
                'title' => 'Gửi email',
                'desc' => $supportEmail,
                'meta' => 'Phản hồi trong 1-2 giờ',
                'href' => 'mailto:' . $supportEmail,
            ],
            [
                'icon' => 'fa-search',
                'title' => 'Kiểm tra đơn hàng',
                'desc' => 'Tra cứu trạng thái đơn hàng nhanh chóng',
                'href' => url('/kiem-tra-don-hang'),
            ],
            [
                'icon' => 'fa-shield',
                'title' => 'Yêu cầu bảo hành',
                'desc' => 'Tạo yêu cầu bảo hành sản phẩm',
                'href' => '#request-form',
            ],
            [
                'icon' => 'fa-refresh',
                'title' => 'Đổi trả / hoàn tiền',
                'desc' => 'Hướng dẫn đổi trả và hoàn tiền dễ dàng',
                'href' => '#topics',
            ],
        ];

        $topics = [
            ['icon' => 'fa-credit-card', 'title' => 'Đặt hàng & thanh toán', 'desc' => 'Hướng dẫn đặt hàng, thanh toán và xuất hóa đơn'],
            ['icon' => 'fa-truck', 'title' => 'Giao hàng', 'desc' => 'Thời gian giao hàng, phí và chính sách vận chuyển'],
            ['icon' => 'fa-shield', 'title' => 'Bảo hành', 'desc' => 'Chính sách & quy trình bảo hành sản phẩm'],
            ['icon' => 'fa-refresh', 'title' => 'Đổi trả', 'desc' => 'Điều kiện, quy trình đổi trả và hoàn tiền'],
            ['icon' => 'fa-user', 'title' => 'Tài khoản', 'desc' => 'Quản lý tài khoản, đăng nhập và bảo mật'],
            ['icon' => 'fa-tag', 'title' => 'Khuyến mãi', 'desc' => 'Mã giảm giá, chương trình khuyến mãi và ưu đãi'],
            ['icon' => 'fa-shopping-cart', 'title' => 'Sản phẩm', 'desc' => 'Thông tin, hướng dẫn sử dụng và tương thích'],
            ['icon' => 'fa-file-text-o', 'title' => 'Bài viết & hướng dẫn', 'desc' => 'Hướng dẫn sử dụng, mẹo và tin tức công nghệ'],
        ];

        $faqs = [
            ['q' => 'Làm thế nào để theo dõi trạng thái đơn hàng?', 'a' => 'Bạn có thể đăng nhập tài khoản và truy cập mục "Lịch sử đơn hàng" để xem trạng thái hoặc dùng tính năng tra cứu đơn hàng theo mã.'],
            ['q' => 'Thời gian giao hàng dự kiến là bao lâu?', 'a' => 'Nội thành Hà Nội giao trong 2 - 4 giờ, các tỉnh khác từ 1 - 3 ngày làm việc tùy khu vực.'],
            ['q' => 'Sản phẩm được bảo hành trong bao lâu?', 'a' => 'Tất cả sản phẩm đều được bảo hành chính hãng từ 12 - 36 tháng tùy nhà sản xuất.'],
            ['q' => 'Điều kiện để đổi/trả sản phẩm là gì?', 'a' => 'Đổi trả trong 7 ngày nếu sản phẩm còn nguyên tem, không có dấu hiệu sử dụng và đầy đủ phụ kiện.'],
            ['q' => 'Phương thức thanh toán nào được chấp nhận?', 'a' => 'Chấp nhận VISA, Mastercard, Momo, ZaloPay, VNPay, chuyển khoản ngân hàng và thanh toán khi nhận hàng (COD).'],
            ['q' => 'Tôi có thể xuất hóa đơn VAT như thế nào?', 'a' => 'Khi đặt hàng vui lòng tích chọn "Yêu cầu xuất hóa đơn VAT" và nhập đầy đủ thông tin doanh nghiệp tại trang thanh toán.'],
        ];

        $orderSupports = [
            ['icon' => 'fa-laptop',     'title' => 'Theo dõi đơn hàng', 'desc' => 'Kiểm tra trạng thái đơn hàng của bạn',          'cta' => 'Theo dõi ngay'],
            ['icon' => 'fa-clipboard',  'title' => 'Chi tiết đơn hàng', 'desc' => 'Xem thông tin chi tiết và lịch sử đơn hàng',     'cta' => 'Xem chi tiết'],
            ['icon' => 'fa-qrcode',     'title' => 'Thanh toán QR thất bại', 'desc' => 'Hướng dẫn xử lý khi thanh toán QR không thành công', 'cta' => 'Xem hướng dẫn'],
            ['icon' => 'fa-file-text-o','title' => 'Yêu cầu xuất hóa đơn', 'desc' => 'Đăng ký để yêu cầu hóa đơn điện tử',           'cta' => 'Xem hướng dẫn'],
        ];

        $highlightArticles = [
            [
                'date' => '15/05/2025',
                'tag' => 'Đặt hàng & thanh toán',
                'title' => 'Hướng dẫn theo dõi đơn hàng trên DangDuc.vn',
                'desc' => 'Các bước theo dõi đơn hàng nhanh chóng và chính xác nhất.',
                'image' => asset('uploads/contact/blog-three41.jpg'),
            ],
            [
                'date' => '10/05/2025',
                'tag' => 'Đổi trả',
                'title' => 'Chính sách đổi trả và hoàn tiền mới nhất 2025',
                'desc' => 'Cập nhật điều kiện đổi trả và hoàn tiền áp dụng tại DangDuc.',
                'image' => asset('uploads/contact/laptop-symbol-azy3.png'),
            ],
            [
                'date' => '05/05/2025',
                'tag' => 'Thanh toán',
                'title' => 'Các phương thức thanh toán được chấp nhận tại DangDuc',
                'desc' => 'Hướng dẫn các hình thức thanh toán an toàn và tiện lợi.',
                'image' => asset('frontend/images/banners/hero-student-promo.png'),
            ],
        ];

        $features = [
            ['icon' => 'fa-truck',      'title' => 'Miễn phí giao hàng', 'desc' => 'Áp dụng cho đơn hàng công nghệ đủ điều kiện'],
            ['icon' => 'fa-refresh',    'title' => 'Đổi trả dễ dàng',    'desc' => 'Đổi trả trong 7 ngày nếu sản phẩm bị lỗi'],
            ['icon' => 'fa-shield',     'title' => 'Bảo hành chính hãng','desc' => 'Cam kết bảo hành chính hãng từ 12 - 36 tháng'],
            ['icon' => 'fa-headphones', 'title' => 'Hỗ trợ tận tâm 24/7','desc' => 'Đội ngũ hỗ trợ luôn sẵn sàng giải đáp mọi thắc mắc'],
        ];
    @endphp

    <main class="dd-support">
        <div class="container">
            @include('partials.storefront.breadcrumb', [
                'class' => 'dd-support__breadcrumb',
                'separator' => '›',
                'items' => [
                    [
                        'label' => 'Chăm sóc khách hàng',
                        'current' => true,
                    ],
                ],
            ])

            {{-- Hero --}}
            <section class="dd-support__hero">
                <div class="dd-support__hero-copy">
                    <h1 class="dd-support__hero-title">Chăm sóc khách hàng</h1>
                    <p class="dd-support__hero-desc">
                        Chúng tôi luôn sẵn sàng hỗ trợ bạn nhanh chóng và tận tâm nhất.
                    </p>
                    <form class="dd-support__search" role="search" onsubmit="return false;">
                        <i class="fa fa-search"></i>
                        <input type="text" placeholder="Tìm câu hỏi, đơn hàng hoặc chủ đề hỗ trợ..." aria-label="Tìm hỗ trợ">
                    </form>
                </div>
                <div class="dd-support__hero-art" aria-hidden="true">
                    <svg viewBox="0 0 460 320" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet">
                        <defs>
                            <linearGradient id="dds-blob" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0" stop-color="#cffafe"/>
                                <stop offset="1" stop-color="#e0f2fe" stop-opacity="0.45"/>
                            </linearGradient>
                            <linearGradient id="dds-bubble" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0" stop-color="#22d3ee"/>
                                <stop offset="1" stop-color="#0891b2"/>
                            </linearGradient>
                            <linearGradient id="dds-skin" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0" stop-color="#fde7d4"/>
                                <stop offset="1" stop-color="#f3c8a3"/>
                            </linearGradient>
                            <linearGradient id="dds-shirt" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0" stop-color="#ffffff"/>
                                <stop offset="1" stop-color="#e2e8f0"/>
                            </linearGradient>
                            <linearGradient id="dds-hair" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0" stop-color="#3f2a1d"/>
                                <stop offset="1" stop-color="#1f1208"/>
                            </linearGradient>
                            <radialGradient id="dds-leaf" cx="0.3" cy="0.3" r="0.8">
                                <stop offset="0" stop-color="#86efac"/>
                                <stop offset="1" stop-color="#15803d"/>
                            </radialGradient>
                        </defs>

                        <path d="M30 250 C 0 170 40 70 150 50 C 270 28 360 70 400 130 C 440 200 360 280 270 285 C 180 290 80 320 30 250 Z" fill="url(#dds-blob)"/>

                        {{-- Plant --}}
                        <g transform="translate(40 195)">
                            <rect x="0" y="40" width="60" height="56" rx="6" fill="#a3a3a3"/>
                            <rect x="2" y="40" width="56" height="6" fill="#737373"/>
                            <path d="M30 40 q-30 -30 -10 -60 q26 18 12 60" fill="url(#dds-leaf)"/>
                            <path d="M30 40 q22 -34 50 -34 q-6 32 -34 38" fill="url(#dds-leaf)" opacity="0.9"/>
                            <path d="M30 40 q-4 -36 18 -54 q14 26 -4 56" fill="url(#dds-leaf)" opacity="0.8"/>
                        </g>

                        {{-- Desk --}}
                        <rect x="0" y="280" width="460" height="40" fill="#cbd5e1" opacity="0.55"/>

                        {{-- Person --}}
                        <g transform="translate(150 70)">
                            {{-- Body --}}
                            <path d="M-30 200 Q 60 120 150 200 L150 240 L-30 240 Z" fill="url(#dds-shirt)"/>
                            {{-- Neck --}}
                            <rect x="48" y="120" width="24" height="28" fill="url(#dds-skin)"/>
                            {{-- Head --}}
                            <ellipse cx="60" cy="92" rx="42" ry="48" fill="url(#dds-skin)"/>
                            {{-- Hair --}}
                            <path d="M18 92 Q 18 30 60 30 Q 110 30 104 96 Q 90 60 60 60 Q 30 60 18 92 Z" fill="url(#dds-hair)"/>
                            <path d="M22 96 Q 18 140 32 152 L 28 110 Z" fill="url(#dds-hair)"/>
                            {{-- Headset --}}
                            <path d="M14 90 Q 60 24 108 90" stroke="#0f172a" stroke-width="6" fill="none" stroke-linecap="round"/>
                            <rect x="6" y="86" width="14" height="22" rx="6" fill="#0f172a"/>
                            <rect x="100" y="86" width="14" height="22" rx="6" fill="#0f172a"/>
                            {{-- Mic --}}
                            <path d="M14 102 Q 6 130 36 138" stroke="#0f172a" stroke-width="4" fill="none" stroke-linecap="round"/>
                            <circle cx="38" cy="138" r="5" fill="#0f172a"/>
                            {{-- Eyes & smile --}}
                            <circle cx="46" cy="92" r="3" fill="#0f172a"/>
                            <circle cx="76" cy="92" r="3" fill="#0f172a"/>
                            <path d="M46 110 q14 12 30 0" stroke="#0f172a" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                        </g>

                        {{-- Chat bubbles --}}
                        <g transform="translate(310 50)">
                            <path d="M0 0 h70 a14 14 0 0 1 14 14 v36 a14 14 0 0 1 -14 14 h-46 l-16 18 v-18 h-8 a14 14 0 0 1 -14 -14 v-36 a14 14 0 0 1 14 -14 z" fill="url(#dds-bubble)"/>
                            <circle cx="22" cy="32" r="3.5" fill="#fff"/>
                            <circle cx="42" cy="32" r="3.5" fill="#fff"/>
                            <circle cx="62" cy="32" r="3.5" fill="#fff"/>
                        </g>
                        <g transform="translate(360 150)">
                            <circle cx="34" cy="34" r="34" fill="#22d3ee"/>
                            <path d="M34 14 a16 18 0 1 0 0.01 0 z M22 50 q0 -10 12 -10 q12 0 12 10" stroke="#fff" stroke-width="3" fill="none"/>
                        </g>
                    </svg>
                </div>
            </section>

            {{-- Quick action cards --}}
            <section class="dd-support__quick">
                @foreach ($quickActions as $action)
                    <a class="dd-quick" href="{{ $action['href'] }}">
                        <span class="dd-quick__icon"><i class="fa {{ $action['icon'] }}"></i></span>
                        <div class="dd-quick__body">
                            <strong>{{ $action['title'] }}</strong>
                            <p>{{ $action['desc'] }}</p>
                            @if (!empty($action['meta']))
                                <small>{{ $action['meta'] }}</small>
                            @endif
                        </div>
                    </a>
                @endforeach
            </section>

            {{-- Topic categories --}}
            <section id="topics" class="dd-support__topics">
                <h2 class="dd-support__heading">Danh mục hỗ trợ</h2>
                <div class="dd-support__topics-grid">
                    @foreach ($topics as $topic)
                        <a class="dd-topic" href="#">
                            <span class="dd-topic__icon"><i class="fa {{ $topic['icon'] }}"></i></span>
                            <div class="dd-topic__body">
                                <strong>{{ $topic['title'] }}</strong>
                                <p>{{ $topic['desc'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>

            {{-- FAQ + Contact + Form --}}
            <section class="dd-support__split">
                {{-- FAQ --}}
                <div class="dd-card dd-faq-card">
                    <h3 class="dd-card__title">Câu hỏi thường gặp</h3>
                    <div class="dd-faq-list">
                        @foreach ($faqs as $faq)
                            <details class="dd-faq-item">
                                <summary>
                                    <span>{{ $faq['q'] }}</span>
                                    <i class="fa fa-chevron-down"></i>
                                </summary>
                                <p>{{ $faq['a'] }}</p>
                            </details>
                        @endforeach
                    </div>
                    <a class="dd-faq-card__more" href="{{ url('/cham-soc-khach-hang/faq') }}">Xem tất cả câu hỏi thường gặp <i class="fa fa-long-arrow-right"></i></a>
                </div>

                {{-- Need help now --}}
                <div class="dd-card dd-help-card" id="contact-now">
                    <h3 class="dd-card__title">Cần hỗ trợ ngay?</h3>
                    <ul class="dd-help-list">
                        <li>
                            <span class="dd-help-list__icon"><i class="fa fa-phone"></i></span>
                            <div>
                                <strong>Hotline</strong>
                                <p><a href="{{ $supportPhoneHref }}">{{ $supportPhoneDisplay }}</a></p>
                            </div>
                        </li>
                        <li>
                            <span class="dd-help-list__icon"><i class="fa fa-envelope-o"></i></span>
                            <div>
                                <strong>Email</strong>
                                <p><a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></p>
                            </div>
                        </li>
                        <li>
                            <span class="dd-help-list__icon"><i class="fa fa-clock-o"></i></span>
                            <div>
                                <strong>Thời gian làm việc</strong>
                                <p>8:00 - 22:00 (Tất cả các ngày)</p>
                            </div>
                        </li>
                    </ul>
                    <div class="dd-help-card__actions">
                        <button type="button" class="dd-btn dd-btn--primary"><i class="fa fa-comments-o"></i> Bắt đầu chat</button>
                        <a class="dd-btn dd-btn--ghost" href="{{ url('/lien-he') }}"><i class="fa fa-envelope"></i> Liên hệ ngay</a>
                    </div>
                </div>

                {{-- Form --}}
                <div class="dd-card dd-request-card" id="request-form">
                    <h3 class="dd-card__title">Gửi yêu cầu hỗ trợ</h3>
                    <form class="dd-request-form" onsubmit="return false;">
                        <div class="dd-form-row">
                            <label>Chủ đề</label>
                            <div class="dd-form-select">
                                <select>
                                    <option>Chọn chủ đề</option>
                                    <option>Đặt hàng &amp; thanh toán</option>
                                    <option>Giao hàng</option>
                                    <option>Bảo hành</option>
                                    <option>Đổi trả</option>
                                    <option>Tài khoản</option>
                                </select>
                                <i class="fa fa-angle-down"></i>
                            </div>
                        </div>
                        <div class="dd-form-row">
                            <label>Email của bạn</label>
                            <input type="email" placeholder="email@example.com">
                        </div>
                        <div class="dd-form-row">
                            <label>Mã đơn hàng (nếu có)</label>
                            <input type="text" placeholder="VD: DD123456789">
                        </div>
                        <div class="dd-form-row">
                            <label>Nội dung chi tiết</label>
                            <textarea rows="4" placeholder="Nhập nội dung yêu cầu hỗ trợ..."></textarea>
                        </div>
                        <button type="submit" class="dd-btn dd-btn--primary dd-btn--block">Gửi yêu cầu</button>
                    </form>
                </div>
            </section>

            {{-- Order support --}}
            <section class="dd-support__orders">
                <h2 class="dd-support__heading">Hỗ trợ đơn hàng</h2>
                <div class="dd-support__orders-grid">
                    @foreach ($orderSupports as $item)
                        <a class="dd-order" href="#">
                            <span class="dd-order__icon"><i class="fa {{ $item['icon'] }}"></i></span>
                            <div class="dd-order__body">
                                <strong>{{ $item['title'] }}</strong>
                                <p>{{ $item['desc'] }}</p>
                                <span class="dd-order__cta">{{ $item['cta'] }} <i class="fa fa-long-arrow-right"></i></span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>

            {{-- Highlight articles --}}
            <section class="dd-support__articles">
                <header class="dd-support__articles-head">
                    <h2 class="dd-support__heading dd-support__heading--inline">Bài viết hỗ trợ nổi bật</h2>
                    <a class="dd-support__articles-more" href="{{ url('/bai-viet') }}">Xem tất cả bài viết <i class="fa fa-long-arrow-right"></i></a>
                </header>
                <div class="dd-support__articles-grid">
                    @foreach ($highlightArticles as $article)
                        <article class="dd-article">
                            <a class="dd-article__media" href="#">
                                <span class="dd-article__date">{{ $article['date'] }}</span>
                                <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" loading="lazy" onerror="this.style.display='none'">
                            </a>
                            <div class="dd-article__body">
                                <h3 class="dd-article__title"><a href="#">{{ $article['title'] }}</a></h3>
                                <p>{{ $article['desc'] }}</p>
                                <span class="dd-article__tag">{{ $article['tag'] }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            {{-- Bottom features --}}
            <section class="dd-support__features">
                @foreach ($features as $f)
                    <div class="dd-feature">
                        <span class="dd-feature__icon"><i class="fa {{ $f['icon'] }}"></i></span>
                        <div>
                            <strong>{{ $f['title'] }}</strong>
                            <p>{{ $f['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </section>
        </div>
    </main>
@endsection
