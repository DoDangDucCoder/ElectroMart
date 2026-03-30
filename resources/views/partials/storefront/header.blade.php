<header id="header"><!--header-->
    <div class="shopw-topbar shopw-topbar--compact">
        <div class="container">
            <div class="shopw-topbar-inner">
                <div class="shopw-topbar-copy">
                    <span class="shopw-topbar-badge">{{ config('services.storefront_brand.name', 'ShopW') }}</span>
                    <strong class="shopw-topbar-headline">Ưu đãi chọn lọc mỗi ngày</strong>
                </div>
                <div class="shopw-topbar-highlights">
                    <span class="shopw-topbar-pill"><i class="fa fa-truck"></i> Giao nhanh nội thành</span>
                    <span class="shopw-topbar-pill"><i class="fa fa-shield"></i> Bảo hành minh bạch</span>
                    <a class="shopw-topbar-cta" href="{{ url('/lien-he') }}">{{ config('storefront.labels.topbar_contact_fast', 'Liên hệ nhanh') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="header-middle"><!--header-middle-->
        <div class="container">
            @php
                $supportConfig = config('services.storefront_support', []);
                $headerLogo = optional($contact_footer->first())->info_logo ?: 'laptop-symbol-azy3.png';
                $headerLogoPath = 'uploads/contact/' . $headerLogo;
                if (!file_exists(public_path($headerLogoPath))) {
                    $headerLogoPath = 'uploads/contact/laptop-symbol-azy3.png';
                }
                $headerSlogan = optional($contact_footer->first())->slogan_logo ?: 'Điểm mua công nghệ gọn hơn mỗi ngày';
                $headerSloganCompact = \Illuminate\Support\Str::limit(\Illuminate\Support\Str::squish(strip_tags($headerSlogan)), 34, '...');
                $customer_id = Session::get('customer_id');
                $shipping_id = Session::get('shipping_id');
                $brandConfig = config('services.storefront_brand', []);
                $supportPhoneDisplay = $supportConfig['contact_phone_display'] ?? '1900.0000';
                $supportPhoneHref = $supportConfig['contact_phone_href'] ?? '19000000';
                $storefrontLabel = \App\Support\StorefrontLabel::class;
                $categoryItems = collect($category ?? [])->filter(fn($item) => ! empty($item->slug_category_product));
                $rootCategoryItems = $categoryItems
                    ->filter(
                        fn($item) => (int) ($item->category_parent ?? 0) === 0 &&
                            (int) ($item->category_status ?? 0) === 0,
                    )
                    ->sortBy(
                        fn($item) => sprintf(
                            '%010d-%010d',
                            (int) ($item->category_order ?? 0),
                            (int) ($item->category_id ?? 0),
                        ),
                    )
                    ->values();

                if ($rootCategoryItems->isEmpty()) {
                    $rootCategoryItems = $categoryItems
                        ->sortBy(
                            fn($item) => sprintf(
                                '%010d-%010d',
                                (int) ($item->category_order ?? 0),
                                (int) ($item->category_id ?? 0),
                            ),
                        )
                        ->values();
                }

                $featuredCategoryLink = $rootCategoryItems->firstWhere('slug_category_product', 'laptop-gaming')
                    ?? $rootCategoryItems->firstWhere('slug_category_product', 'laptop')
                    ?? $rootCategoryItems->first();

                $secondaryCategoryLink = $rootCategoryItems->firstWhere('slug_category_product', 'laptop');

                if (
                    ! $secondaryCategoryLink ||
                    (
                        $featuredCategoryLink &&
                        $secondaryCategoryLink->slug_category_product === $featuredCategoryLink->slug_category_product
                    )
                ) {
                    $secondaryCategoryLink = $rootCategoryItems->first(
                        fn($item) => ! $featuredCategoryLink ||
                            $item->slug_category_product !== $featuredCategoryLink->slug_category_product,
                    ) ?? $featuredCategoryLink;
                }

                $blogCategoryItems = collect($category_post ?? [])->filter(fn($item) => ! empty($item->cate_post_slug))->values();
                $featuredBlogCategory = $blogCategoryItems->firstWhere('cate_post_slug', 'tin-tuc-cong-nghe')
                    ?? $blogCategoryItems->firstWhere('cate_post_slug', 'tin-tuc')
                    ?? $blogCategoryItems->first();

                $featuredCategoryUrl = $featuredCategoryLink
                    ? url('/danh-muc/' . $featuredCategoryLink->slug_category_product)
                    : url('/trang-chu');
                $featuredCategoryLabel = $storefrontLabel::category(
                    $featuredCategoryLink?->category_name,
                    $featuredCategoryLink?->slug_category_product,
                    'Ưu đãi nổi bật',
                );
                $secondaryCategoryUrl = $secondaryCategoryLink
                    ? url('/danh-muc/' . $secondaryCategoryLink->slug_category_product)
                    : $featuredCategoryUrl;
                $secondaryCategoryLabel = $storefrontLabel::category(
                    $secondaryCategoryLink?->category_name,
                    $secondaryCategoryLink?->slug_category_product,
                    'Danh mục nổi bật',
                );
                $featuredBlogUrl = $featuredBlogCategory
                    ? url('/danh-muc-bai-viet/' . $featuredBlogCategory->cate_post_slug)
                    : url('/video-shop');
                $featuredBlogLabel = $storefrontLabel::blogCategory(
                    $featuredBlogCategory?->cate_post_name,
                    $featuredBlogCategory?->cate_post_slug,
                    'Video công nghệ',
                );
                $searchHintLinks = [
                    [
                        'href' => $featuredCategoryUrl,
                        'icon' => 'fa-bolt',
                        'label' => $featuredCategoryLabel,
                    ],
                    [
                        'href' => $featuredBlogUrl,
                        'icon' => 'fa-newspaper-o',
                        'label' => $featuredBlogLabel,
                    ],
                    [
                        'href' => url('/goi-y-san-pham'),
                        'icon' => 'fa-compass',
                        'label' => config('storefront.labels.recommend_for_you', 'Gợi ý cho bạn'),
                    ],
                ];
                $headerSearchHintLinks = collect($searchHintLinks)->take(2)->values();
                $checkoutUrl = match (true) {
                    $customer_id != null && $shipping_id == null => url('/checkout'),
                    $customer_id != null && $shipping_id != null => url('/payment'),
                    default => url('/dang-nhap'),
                };
                $accountUtilityUrl = $customer_id != null ? url('/history') : url('/dang-nhap');
                $accountUtilityLabel = $customer_id != null ? 'Đơn hàng' : 'Tài khoản';
                $mobileQuicklinks = [
                    [
                        'href' => $featuredCategoryUrl,
                        'icon' => 'fa-bolt',
                        'label' => $featuredCategoryLabel,
                        'note' => 'Khuyến mãi nổi bật',
                        'badge' => 'Nổi bật',
                        'class' => 'shopw-quick-hot',
                    ],
                    [
                        'href' => $featuredBlogUrl,
                        'icon' => 'fa-newspaper-o',
                        'label' => $featuredBlogLabel,
                        'note' => 'Cập nhật mới trong ngày',
                        'badge' => 'Mới',
                        'class' => '',
                    ],
                    [
                        'href' => url('/lien-he'),
                        'icon' => 'fa-wrench',
                        'label' => 'Dịch vụ kỹ thuật',
                        'note' => 'Hỗ trợ tại nhà',
                        'badge' => '24/7',
                        'class' => '',
                    ],
                    [
                        'href' => url('/history'),
                        'icon' => 'fa-shield',
                        'label' => 'Bảo hành',
                        'note' => 'Tra cứu đơn và hậu mãi',
                        'badge' => 'Hậu mãi',
                        'class' => '',
                    ],
                ];
            @endphp
            <div class="shopw-header">
                <div class="shopw-header-brand">
                    <div class="shopw-logo">
                        <a class="shopw-logo-link" href="{{ url('/') }}">
                            <img src="{{ asset($headerLogoPath) }}" alt="Logo">
                        </a>
                        <div class="shopw-logo-copy">
                            <strong>{{ $headerSloganCompact }}</strong>
                        </div>
                    </div>
                </div>
                <div class="shopw-header-center">
                    <button class="shopw-cat-btn shopw-mobile-nav-toggle" type="button" data-nav-intent="categories"
                        data-desktop-target="#shopw-nav-group-categories" aria-label="Mở danh mục"
                        aria-haspopup="menu" aria-expanded="false">
                        <span class="shopw-cat-btn-icon"><i class="fa fa-bars"></i></span>
                        <span class="shopw-cat-btn-copy">
                            <small>Điều hướng</small>
                            <strong>Danh mục</strong>
                        </span>
                    </button>
                    <div class="shopw-search-shell">
                        <form action="{{ URL::to('/tim-kiem') }}" autocomplete="off" method="POST" class="shopw-search">
                            {{ csrf_field() }}
                            <input type="text" name="keywords_submit" id="keywords"
                                value="{{ old('keywords_submit') }}" placeholder="Bạn cần tìm gì?">
                            <button type="submit" class="shopw-search-btn" aria-label="Tìm kiếm">
                                <i class="fa fa-search"></i>
                            </button>
                            <div id="search_ajax" class="shopw-search-suggest"></div>
                            @if ($errors->has('keywords_submit'))
                                <div class="text-danger" style="margin-top: 8px; font-size: 12px;">
                                    {{ $errors->first('keywords_submit') }}
                                </div>
                            @endif
                        </form>
                        <div class="shopw-search-hints shopw-search-hints--compact">
                            @foreach ($headerSearchHintLinks as $hint)
                                <a href="{{ $hint['href'] }}">
                                    <i class="fa {{ $hint['icon'] }}"></i>
                                    <span>{{ $hint['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="shopw-header-actions">
                    <ul class="shopw-utilities shopw-utilities--desktop shopw-utilities--compact">
                        <li class="shopw-util shopw-hotline">
                            <a href="tel:{{ $supportPhoneHref }}" title="Hotline {{ $supportPhoneDisplay }}">
                                <i class="fa fa-headphones"></i>
                                <span class="shopw-util-label">{{ $supportPhoneDisplay }}</span>
                            </a>
                        </li>
                        <li class="shopw-util shopw-showroom">
                            <a href="{{ URL::to('/lien-he') }}" title="{{ config('storefront.labels.utility_store', 'Cửa hàng') }}">
                                <i class="fa fa-map-marker"></i>
                                <span class="shopw-util-label">{{ config('storefront.labels.utility_store', 'Cửa hàng') }}</span>
                            </a>
                        </li>
                        <li class="shopw-util cart-hover shopw-cart">
                            <a href="{{ url('gio-hang') }}" title="Giỏ hàng">
                                <i class="fa fa-shopping-cart"></i>
                                <span class="shopw-util-label">Giỏ hàng</span>
                                <span class="show-cart"></span>
                                <span class="giohang-hover"></span>
                            </a>
                        </li>
                        <li class="shopw-util shopw-auth">
                            <a href="{{ $accountUtilityUrl }}" title="{{ $accountUtilityLabel }}">
                                <i class="fa fa-user"></i>
                                <span class="shopw-util-label">{{ $accountUtilityLabel }}</span>
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

                                <li><a href="{{ URL::to('/trang-chu') }}" class="active">Trang chủ</a></li>
                                <li class="dropdown shopw-nav-group" id="shopw-nav-group-categories">
                                    <div class="shopw-nav-row">
                                        <a href="#">
                                            <span class="shopw-nav-label">
                                                <span class="shopw-nav-icon"><i class="fa fa-th-large"></i></span>
                                                <span class="shopw-nav-text">
                                                    <strong>Danh mục sản phẩm</strong>
                                                    <small>Laptop, PC, linh kiện và phụ kiện</small>
                                                </span>
                                            </span>
                                            <span class="shopw-nav-badge">Nổi bật</span>
                                            <span class="shopw-nav-desktop-arrow"><i class="fa fa-angle-down"></i></span>
                                        </a>
                                        <button type="button" class="shopw-submenu-toggle" aria-expanded="false"
                                            aria-label="Mở danh mục sản phẩm">
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

                                <li class="dropdown shopw-nav-group" id="shopw-nav-group-blogs">
                                    <div class="shopw-nav-row">
                                        <a href="#">
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

                                <li><a href="{{ URL::to('/gio-hang') }}">
                                            <span class="shopw-nav-label">
                                            <span class="shopw-nav-icon"><i class="fa fa-shopping-cart"></i></span>
                                            <span class="shopw-nav-text">
                                                <strong>Giỏ hàng</strong>
                                                <small>Xem sản phẩm đã chọn</small>
                                            </span>
                                        </span>
                                        <span class="show-cart shopw-nav-inline-status"></span>
                                    </a>

                                </li>

                                <li><a href="{{ URL::to('/lien-he') }}">
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
                                        <a class="{{ $quicklink['class'] }}" href="{{ $quicklink['href'] }}">
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

