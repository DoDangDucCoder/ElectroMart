@php
    $supportConfig = config('services.storefront_support', []);
    $storefrontLink = \App\Support\StorefrontLink::class;
    $headerLogoPath = 'uploads/brand/dangduc-logo-horizontal-width-600.svg';
    $customer_id = Session::get('customer_id');
    $shipping_id = Session::get('shipping_id');
    $brandConfig = config('services.storefront_brand', []);
    $supportPhoneDisplay = $supportConfig['contact_phone_display'] ?? '1900.0000';
    $supportPhoneHref = $supportConfig['phone_href'] ?? ('tel:' . ($supportConfig['contact_phone_href'] ?? '19000000'));
    $storefrontLabel = \App\Support\StorefrontLabel::class;
    $storefrontCustomerAuth = \App\Support\StorefrontCustomerAuth::class;
    $currentPath = request()->path();
    $isHomeRoute = $currentPath === '/' || request()->is('trang-chu');
    $isCategorySection = request()->is('san-pham')
        || request()->is('san-pham-ban-chay')
        || request()->is('danh-muc/*')
        || request()->is('chi-tiet/*')
        || request()->is('tim-kiem')
        || request()->is('tag/*')
        || request()->is('thuong-hieu/*')
        || request()->is('goi-y-san-pham');
    $isBlogSection = request()->is('bai-viet')
        || request()->is('bai-viet/*')
        || request()->is('video')
        || request()->is('xem-video/*');
    $isCartSection = request()->is('gio-hang')
        || request()->is('checkout')
        || request()->is('payment')
        || request()->is('dat-hang-thanh-cong/*')
        || request()->is('dat-hang-that-bai/*')
        || request()->is('payment/bank-transfer/*');
    $isPromoSection = request()->is('khuyen-mai');
    $isContactSection = request()->is('lien-he');
    $isHistorySection = request()->is('history')
        || request()->is('view-history-order/*')
        || request()->is('account')
        || request()->is('account/*')
        || request()->is('kiem-tra-don-hang')
        || request()->is('xac-thuc-otp')
        || request()->is('dat-lai-mat-khau');
    $isLoginSection = request()->is('dang-nhap')
        || request()->is('dang-ky')
        || request()->is('quen-mat-khau')
        || request()->is('update-new-pass');
    $defaultStorefrontAvatar = asset('frontend/images/storefront-account-avatar.svg');
    $headerCustomer = $customer_id ? $storefrontCustomerAuth::headerCustomer((int) $customer_id) : null;
    $headerCustomerName = $headerCustomer?->name ?: Session::get($storefrontCustomerAuth::SESSION_NAME_KEY);
    $headerCustomerEmail = $headerCustomer?->email ?: Session::get($storefrontCustomerAuth::SESSION_EMAIL_KEY);
    $headerCustomerPhone = $headerCustomer?->phone;
    $headerCustomerPicture = $headerCustomer?->avatar_path
        ? asset($headerCustomer->avatar_path)
        : Session::get($storefrontCustomerAuth::SESSION_PICTURE_KEY);
    $headerCustomerAvatar = $headerCustomerPicture ?: $defaultStorefrontAvatar;
    $isAuthenticatedCustomer = ! empty($headerCustomerName) || ! empty($headerCustomerEmail);
    $isAccountSection = $isAuthenticatedCustomer ? $isHistorySection : $isLoginSection;
    $headerSearchSuggestions = [
        'Laptop gaming',
        'MacBook Air',
        'Màn hình 27 inch',
        'Router WiFi 6',
        'Máy in văn phòng',
        'Chuột gaming',
        'Tai nghe Bluetooth',
        'Bàn phím cơ',
        'PC văn phòng',
    ];

    $categoryItems = collect($category ?? [])->filter(fn($item) => ! empty($item->slug_category_product));
    $blogCategoryItems = collect($category_post ?? [])->filter(fn($item) => ! empty($item->cate_post_slug));
    $headerCartCount = collect(Session::get('cart', []))
        ->filter(fn ($item) => is_array($item))
        ->sum(fn ($item) => (int) ($item['product_qty'] ?? 0));

    $featuredBlogCategory = $blogCategoryItems->first();
    $featuredBlogUrl = url('/bai-viet');

    $mobileQuicklinks = [
        [
            'href' => url('/trang-chu'),
            'icon' => 'fa-home',
            'label' => 'Trang chủ',
            'note' => 'Về màn hình chính',
            'badge' => 'Home',
            'class' => '',
        ],
        [
            'href' => $featuredBlogUrl,
            'icon' => 'fa-file-text-o',
            'label' => 'Bài viết mới',
            'note' => 'Tin tức công nghệ',
            'badge' => 'Mới',
            'class' => '',
        ],
    ];
@endphp
<header id="header"><!--header-->
    <div class="shopw-topbar shopw-topbar--compact">
        <div class="container">
            <div class="shopw-topbar-inner">
                <div class="shopw-topbar-highlights">
                    <span class="shopw-topbar-pill"><i class="fa fa-gift"></i> Ưu đãi chọn lọc mỗi ngày</span>
                    <span class="shopw-topbar-pill"><i class="fa fa-truck"></i> Giao hàng nhanh nội thành</span>
                    <span class="shopw-topbar-pill"><i class="fa fa-shield"></i> Bảo hành minh bạch</span>
                    <span class="shopw-topbar-pill"><i class="fa fa-headphones"></i> Hỗ trợ 24/7</span>
                </div>
            </div>
        </div>
    </div>

    <div class="header-middle"><!--header-middle-->
        <div class="container">
            <div class="shopw-header">
                <div class="shopw-header-brand">
                    <button type="button"
                        class="shopw-header-menu-toggle shopw-mobile-nav-toggle"
                        data-nav-intent="categories"
                        aria-label="Mở menu cửa hàng"
                        aria-expanded="false">
                        <i class="fa fa-bars"></i>
                    </button>
                    <div class="shopw-logo">
                        <a class="shopw-logo-link" href="{{ url('/') }}">
                            <img src="{{ asset($headerLogoPath) }}" alt="Logo">
                        </a>
                    </div>
                </div>

                <div class="shopw-header-center">
                    <div class="shopw-search-shell">
                        <form action="{{ url('/san-pham') }}" method="GET" class="shopw-search">
                            <label class="shopw-search-entry" for="keywords">
                                <span class="shopw-search-prefix">Bạn cần gì?</span>
                                <span class="shopw-search-input-stage">
                                    <input type="text" name="q" id="keywords"
                                        placeholder=""
                                        data-placeholder-base=""
                                        data-search-trends='@json($headerSearchSuggestions)'
                                        autocomplete="off">
                                    <span class="shopw-search-rotator" data-search-rotator aria-hidden="true">
                                        <span class="shopw-search-rotator-word" data-search-rotator-word>Laptop gaming</span>
                                    </span>
                                </span>
                            </label>
                            <button type="submit" class="shopw-search-btn">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>
                        <div class="shopw-search-hints {{ $isAccountSection ? 'shopw-search-hints--compact' : '' }}" style="display:none;"></div>
                    </div>
                </div>

                <div class="shopw-header-actions">
                    <ul class="shopw-utilities shopw-utilities--desktop">
                        <li class="shopw-util">
                            <details class="shopw-location-menu">
                                <summary class="shopw-util-link" aria-label="Chọn khu vực giao hàng">
                                    <i class="fa fa-map-marker"></i>
                                    <span class="shopw-util-text">
                                        <span class="shopw-util-label">Giao hàng</span>
                                        <span class="shopw-util-sub">Hà Nội <i class="fa fa-angle-down"></i></span>
                                    </span>
                                </summary>
                                <div class="shopw-location-dropdown">
                                    <strong>Giao hàng tại Hà Nội</strong>
                                    <small>Phí vận chuyển sẽ được cập nhật chính xác ở bước thanh toán.</small>
                                    <a class="shopw-location-link" href="{{ url('/lien-he') }}">Xem thông tin cửa hàng</a>
                                </div>
                            </details>
                        </li>
                        <li class="shopw-util">
                            @if ($isAuthenticatedCustomer)
                                <details class="shopw-account-menu">
                                    <summary
                                        @class(['shopw-account-summary shopw-util-link', 'is-current' => $isAccountSection])
                                        aria-expanded="false"
                                        @if ($isAccountSection) aria-current="page" @endif>
                                        <i class="fa fa-user"></i>
                                        <span class="shopw-util-text">
                                            <span class="shopw-util-label">{{ \Illuminate\Support\Str::limit($headerCustomerName, 12) }}</span>
                                            <span class="shopw-util-sub">Tài khoản <i class="fa fa-angle-down"></i></span>
                                        </span>
                                    </summary>
                                    <div class="shopw-account-dropdown">
                                        <div class="shopw-account-dropdown-head">
                                            <span class="shopw-account-dropdown-kicker">Tài khoản</span>
                                            <strong>{{ $headerCustomerName }}</strong>
                                            @if ($headerCustomerEmail)
                                                <small>{{ $headerCustomerEmail }}</small>
                                            @endif
                                        </div>
                                        <div class="shopw-account-dropdown-links">
                                            <a class="shopw-account-dropdown-link" href="{{ route('customer.account.profile') }}"><i class="fa fa-user"></i> Trang tài khoản</a>
                                            <a class="shopw-account-dropdown-link" href="{{ url('/account/orders') }}"><i class="fa fa-list-alt"></i> Đơn hàng của tôi</a>
                                            <form method="POST" action="{{ url('/dang-xuat-khach-hang') }}">
                                                @csrf
                                                <input type="hidden" name="redirect" value="login">
                                                <button type="submit" class="shopw-account-dropdown-link is-danger"><i class="fa fa-sign-out"></i> Đăng xuất</button>
                                            </form>
                                        </div>
                                    </div>
                                </details>
                            @else
                                <a href="{{ url('/dang-nhap') }}" @class(['is-current' => $isAccountSection]) @if ($isAccountSection) aria-current="page" @endif>
                                    <i class="fa fa-user"></i>
                                    <span class="shopw-util-text">
                                        <span class="shopw-util-label">Đăng nhập</span>
                                        <span class="shopw-util-sub">Tài khoản <i class="fa fa-angle-down"></i></span>
                                    </span>
                                </a>
                            @endif
                        </li>
                        <li class="shopw-util">
                            <a href="{{ url('/gio-hang') }}" @class(['is-current' => $isCartSection]) @if ($isCartSection) aria-current="page" @endif>
                                <i class="fa fa-shopping-cart"></i>
                                <span class="shopw-util-label">Giỏ hàng</span>
                                <span class="shopw-cart-count">{{ $headerCartCount }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div><!--/header-middle-->

    <div class="header-bottom" id="navbar"><!--header-bottom-->
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="shopw-mobile-drawer" id="shopw-mobile-drawer" aria-hidden="true">
                        <div class="shopw-mobile-drawer-head">
                            <div class="shopw-mobile-drawer-copy">
                                <span>Menu cửa hàng</span>
                                <strong>Điều hướng nhanh</strong>
                            </div>
                            <button type="button" class="shopw-mobile-drawer-close" aria-label="Đóng menu">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                        <div class="mainmenu pull-left">
                            <ul class="nav navbar-nav shopw-links">
                                <li><a href="{{ URL::to('/trang-chu') }}"
                                        data-storefront-nav-key="home"
                                        @class(['active' => $isHomeRoute, 'is-current' => $isHomeRoute])
                                        @if ($isHomeRoute) aria-current="page" @endif>Trang chủ</a></li>
                                <li><a href="{{ URL::to('/san-pham') }}"
                                        data-storefront-nav-key="products"
                                        @class(['active' => $isCategorySection, 'is-current' => $isCategorySection])
                                        @if ($isCategorySection) aria-current="page" @endif>Sản phẩm</a></li>

                                <li class="dropdown shopw-nav-group {{ $isBlogSection ? 'is-current' : '' }}" id="shopw-nav-group-blogs">
                                    <div class="shopw-nav-row">
                                        <a href="{{ url('/bai-viet') }}"
                                            data-storefront-nav-parent="blogs"
                                            @class(['active' => $isBlogSection, 'is-current' => $isBlogSection])
                                            @if ($isBlogSection) aria-current="page" @endif>Bài viết</a>
                                        <button type="button" class="shopw-submenu-toggle" aria-label="Mở danh mục bài viết" aria-expanded="false">
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                    </div>
                                    <ul role="menu" class="sub-menu">
                                        @foreach ($blogCategoryItems as $key => $danhmucbaiviet)
                                            <li><a
                                                    href="{{ URL::to('/bai-viet?' . http_build_query(['category' => $danhmucbaiviet->cate_post_slug])) }}">{{ $storefrontLabel::blogCategory($danhmucbaiviet->cate_post_name, $danhmucbaiviet->cate_post_slug) }}</a>
                                            </li>
                                        @endforeach

                                    </ul>
                                </li>

                                <li><a href="{{ URL::to('/khuyen-mai') }}"
                                        data-storefront-nav-key="promo"
                                        @class(['active' => $isPromoSection, 'is-current' => $isPromoSection])
                                        @if ($isPromoSection) aria-current="page" @endif>Bộ sưu tập / Khuyến mãi</a></li>

                                <li><a href="{{ URL::to('/lien-he') }}"
                                            data-storefront-nav-key="contact"
                                            @class(['active' => $isContactSection, 'is-current' => $isContactSection])
                                            @if ($isContactSection) aria-current="page" @endif>Liên hệ</a></li>
                            </ul>
                        </div>
                        <div class="shopw-mobile-drawer-links">
                            <span class="shopw-mobile-drawer-section-title">Lối tắt nhanh</span>
                            <ul class="shopw-mobile-quicklinks">
                                @foreach ($mobileQuicklinks as $quicklink)
                                    <li>
                                        <a class="{{ $quicklink['class'] }}" href="{{ $quicklink['href'] ?? $quicklink['url'] }}">
                                            <span class="shopw-mobile-quicklink-main">
                                                <span class="shopw-mobile-quicklink-icon">
                                                    <i class="fa {{ $quicklink['icon'] }}"></i>
                                                </span>
                                                <span class="shopw-mobile-quicklink-copy">
                                                    <strong>{{ $quicklink['label'] }}</strong>
                                                    <small>{{ $quicklink['note'] }}</small>
                                                </span>
                                                <span class="shopw-mobile-quicklink-arrow" aria-hidden="true">
                                                    <i class="fa fa-angle-right"></i>
                                                </span>
                                            </span>
                                            <span class="shopw-nav-badge shopw-nav-badge-soft">{{ $quicklink['badge'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div><!--/header-bottom-->
    <button type="button" class="shopw-mobile-nav-overlay" aria-label="Đóng menu"></button>

</header><!--/header-->
