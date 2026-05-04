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
    $isCategorySection = request()->is('san-pham') || request()->is('danh-muc/*');
    $isBlogSection = request()->is('danh-muc-bai-viet/*') || request()->is('bai-viet/*') || request()->is('video') || request()->is('video-shop') || request()->is('xem-video/*');
    $isCartSection = request()->is('gio-hang');
    $isPromoSection = request()->is('khuyen-mai');
    $isContactSection = request()->is('lien-he');
    $isHistorySection = request()->is('history') || request()->is('view-history-order/*');
    $isLoginSection = request()->is('dang-nhap') || request()->is('dang-ky') || request()->is('quen-mat-khau') || request()->is('update-new-pass');
    $headerCustomer = null;
    $defaultStorefrontAvatar = asset('frontend/images/storefront-account-avatar.svg');
    if ($customer_id && \Illuminate\Support\Facades\Schema::hasTable('customers')) {
        $headerCustomer = \App\Infrastructure\Persistence\Eloquent\Customer\Customer::query()
            ->select(['id', 'name', 'email', 'phone', 'avatar_path', 'is_active'])
            ->whereKey((int) $customer_id)
            ->where('is_active', true)
            ->first();
    }
    $headerCustomerName = $headerCustomer?->name ?: Session::get($storefrontCustomerAuth::SESSION_NAME_KEY);
    $headerCustomerEmail = $headerCustomer?->email ?: Session::get($storefrontCustomerAuth::SESSION_EMAIL_KEY);
    $headerCustomerPhone = $headerCustomer?->phone;
    $headerCustomerPicture = $headerCustomer?->avatar_path
        ? asset($headerCustomer->avatar_path)
        : Session::get($storefrontCustomerAuth::SESSION_PICTURE_KEY);
    $headerCustomerAvatar = $headerCustomerPicture ?: $defaultStorefrontAvatar;
    $isAuthenticatedCustomer = ! empty($headerCustomerName) || ! empty($headerCustomerEmail);
    $isAccountSection = $isAuthenticatedCustomer ? $isHistorySection : $isLoginSection;

    $categoryItems = collect($category ?? [])->filter(fn($item) => ! empty($item->slug_category_product));
    $blogCategoryItems = collect($category_post ?? [])->filter(fn($item) => ! empty($item->cate_post_slug));

    $featuredBlogCategory = $blogCategoryItems->first();
    $featuredBlogUrl = $featuredBlogCategory ? url('/danh-muc-bai-viet/' . $featuredBlogCategory->cate_post_slug) : url('/video-shop');

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
            'icon' => 'fa-newspaper-o',
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
                        data-desktop-panel="#shopw-desktop-category-panel"
                        aria-label="Mở danh mục sản phẩm"
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
                    <div class="shopw-search-shell" data-search-trends="[]">
                        <form action="{{ url('/tim-kiem') }}" method="GET" class="shopw-search">
                            <label class="shopw-search-entry" for="keywords">
                                <span class="shopw-search-prefix">Bạn cần gì hôm nay?</span>
                                <span class="shopw-search-input-stage">
                                    <input type="text" name="keywords_submit" id="keywords"
                                        placeholder="Laptop gaming, iPhone 15..."
                                        autocomplete="off">
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
                    <ul class="shopw-utilities shopw-utilities--desktop {{ $isAccountSection ? 'shopw-utilities--compact' : '' }}">
                        <li class="shopw-util">
                            <a href="{{ url('/lien-he') }}">
                                <i class="fa fa-map-marker"></i>
                                <span class="shopw-util-text">
                                    <span class="shopw-util-label">Giao hàng</span>
                                    <span class="shopw-util-sub">Hà Nội <i class="fa fa-angle-down"></i></span>
                                </span>
                            </a>
                        </li>
                        <li class="shopw-util">
                            @if ($isAuthenticatedCustomer)
                                <a href="{{ url('/history') }}" class="shopw-account-summary--avatar-only {{ $isAccountSection ? 'is-current' : '' }}">
                                    <img src="{{ $headerCustomerAvatar }}" alt="Avatar" style="display:none;">
                                    <i class="fa fa-user"></i>
                                    <span class="shopw-util-text">
                                        <span class="shopw-util-label">{{ \Illuminate\Support\Str::limit($headerCustomerName, 12) }}</span>
                                        <span class="shopw-util-sub">Tài khoản <i class="fa fa-angle-down"></i></span>
                                    </span>
                                </a>
                                <div class="shopw-account-menu" style="display:none;">
                                    <span class="shopw-account-name">Header Customer</span>
                                    <a href="{{ url('/history') }}">Đơn hàng của tôi</a>
                                    <form method="POST" action="{{ url('/dang-xuat-khach-hang') }}">
                                        @csrf
                                        <input type="hidden" name="redirect" value="login">
                                        <button type="submit">Đăng xuất</button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ url('/dang-nhap') }}">
                                    <i class="fa fa-user"></i>
                                    <span class="shopw-util-text">
                                        <span class="shopw-util-label">Đăng nhập</span>
                                        <span class="shopw-util-sub">Tài khoản <i class="fa fa-angle-down"></i></span>
                                    </span>
                                </a>
                            @endif
                        </li>
                        <li class="shopw-util">
                            <a href="{{ url('/gio-hang') }}">
                                <i class="fa fa-shopping-cart"></i>
                                <span class="shopw-util-label">Giỏ hàng</span>
                                <span class="shopw-cart-count">{{ Session::has('cart') ? count(Session::get('cart')) : 0 }}</span>
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
                                <li class="dropdown shopw-nav-group {{ $isCategorySection ? 'is-current' : '' }}" id="shopw-nav-group-categories">
                                    <div class="shopw-nav-row">
                                        <a href="{{ url('/san-pham') }}"
                                            data-storefront-nav-parent="categories"
                                            @class(['is-current' => $isCategorySection])
                                            @if ($isCategorySection) aria-current="page" @endif>
                                            <span class="shopw-nav-label">
                                                <span class="shopw-nav-icon"><i class="fa fa-bars"></i></span>
                                                <span class="shopw-nav-text">
                                                    <strong>Danh mục sản phẩm</strong>
                                                    <small class="shopw-cat-btn-copy">Laptop, PC, linh kiện và phụ kiện</small>
                                                </span>
                                            </span>
                                            <span class="shopw-nav-desktop-arrow" data-desktop-panel="#shopw-desktop-category-panel"><i class="fa fa-angle-down"></i></span>
                                        </a>
                                        <div class="shopw-header-category-shell" style="display:none;">
                                            <div id="shopw-desktop-category-panel">
                                                <div class="shopw-desktop-category-nav">
                                                    <div class="shopw-desktop-category-trigger"></div>
                                                    <div class="shopw-desktop-category-panel">
                                                        <div class="shopw-desktop-category-pane">
                                                            <div class="shopw-desktop-category-section">
                                                                <div class="shopw-desktop-category-rail">
                                                                    <div class="shopw-desktop-category-section-link-icon"></div>
                                                                    <div class="shopw-desktop-category-promo-icon"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="shopw-submenu-toggle" aria-expanded="false"
                                            aria-label="Mở sản phẩm">
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                    </div>
                                    <ul role="menu" class="sub-menu">
                                        @foreach ($categoryItems as $key => $cate)
                                            @if ($cate->category_parent == 0)
                                                @php
                                                    $categoryChildren = $categoryItems->filter(
                                                        fn($item) => (int) $item->category_parent === (int) $cate->category_id &&
                                                            (int) $item->category_status === 0,
                                                    );
                                                @endphp
                                                <li class="menu-item {{ $categoryChildren->isNotEmpty() ? 'has-children' : '' }}">
                                                    @if ($categoryChildren->isNotEmpty())
                                                        <div class="shopw-submenu-row">
                                                            <a href="{{ URL::to('/danh-muc/' . $cate->slug_category_product) }}">
                                                                {{ $storefrontLabel::category($cate->category_name, $cate->slug_category_product) }}
                                                            </a>
                                                            <button type="button" class="shopw-childmenu-toggle"
                                                                aria-expanded="false"
                                                                aria-label="Mở danh mục {{ $storefrontLabel::category($cate->category_name, $cate->slug_category_product) }}">
                                                                <i class="fa fa-angle-down"></i>
                                                            </button>
                                                        </div>
                                                    @else
                                                        <a href="{{ URL::to('/danh-muc/' . $cate->slug_category_product) }}">
                                                            {{ $storefrontLabel::category($cate->category_name, $cate->slug_category_product) }}
                                                        </a>
                                                    @endif
                                                    @if ($categoryChildren->isNotEmpty())
                                                        <ul class="cate_sub">
                                                            @foreach ($categoryChildren as $cate_sub)
                                                                <li class="submenu-item">
                                                                    <a href="{{ URL::to('/danh-muc/' . $cate_sub->slug_category_product) }}">
                                                                        {{ $storefrontLabel::category($cate_sub->category_name, $cate_sub->slug_category_product) }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </li>

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
                                        <a href="{{ $featuredBlogUrl }}"
                                            data-storefront-nav-parent="blogs"
                                            @class(['is-current' => $isBlogSection])
                                            @if ($isBlogSection) aria-current="page" @endif>
                                            <span class="shopw-nav-label">
                                                <span class="shopw-nav-icon"><i class="fa fa-newspaper-o"></i></span>
                                                <span class="shopw-nav-text">
                                                    <strong>Bài viết</strong>
                                                    <small>Tin tức, review và hướng dẫn nhanh</small>
                                                </span>
                                            </span>
                                            <span class="shopw-nav-badge shopw-nav-badge-cool">Mới</span>
                                            <span class="shopw-nav-desktop-arrow"><i class="fa fa-angle-down"></i></span>
                                        </a>
                                        <button type="button" class="shopw-submenu-toggle" aria-expanded="false"
                                            aria-label="Mở danh mục bài viết">
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                    </div>
                                    <ul role="menu" class="sub-menu">
                                        @foreach ($blogCategoryItems as $key => $danhmucbaiviet)
                                            <li><a
                                                    href="{{ URL::to('/danh-muc-bai-viet/' . $danhmucbaiviet->cate_post_slug) }}">{{ $storefrontLabel::blogCategory($danhmucbaiviet->cate_post_name, $danhmucbaiviet->cate_post_slug) }}</a>
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
                                            @if ($isContactSection) aria-current="page" @endif>
                                            <span class="shopw-nav-label">
                                            <span class="shopw-nav-icon"><i class="fa fa-headphones"></i></span>
                                            <span class="shopw-nav-text">
                                                <strong>Liên hệ</strong>
                                                <small>CSKH, kỹ thuật và bảo hành</small>
                                            </span>
                                        </span>
                                        <span class="shopw-nav-badge shopw-nav-badge-soft">24/7</span>
                                    </a></li>
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
