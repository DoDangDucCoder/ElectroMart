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
                $storefrontLink = \App\Support\StorefrontLink::class;
                $headerLogo = optional($contact_footer->first())->info_logo ?: 'laptop-symbol-azy3.png';
                $headerLogoPath = 'uploads/contact/' . $headerLogo;
                if (!file_exists(public_path($headerLogoPath))) {
                    $headerLogoPath = 'uploads/contact/laptop-symbol-azy3.png';
                }
                $headerSlogan = optional($contact_footer->first())->slogan_logo ?: 'Điểm mua công nghệ gọn hơn mỗi ngày';
                $headerSloganCompact = \Illuminate\Support\Str::limit(\Illuminate\Support\Str::squish(strip_tags($headerSlogan)), 24, '...');
                $customer_id = Session::get('customer_id');
                $shipping_id = Session::get('shipping_id');
                $brandConfig = config('services.storefront_brand', []);
                $supportPhoneDisplay = $supportConfig['contact_phone_display'] ?? '1900.0000';
                $supportPhoneHref = $supportConfig['phone_href'] ?? ('tel:' . ($supportConfig['contact_phone_href'] ?? '19000000'));
                $storefrontLabel = \App\Support\StorefrontLabel::class;
                $storefrontCustomerAuth = \App\Support\StorefrontCustomerAuth::class;
                $currentPath = request()->path();
                $isHomeRoute = $currentPath === '/' || request()->is('trang-chu');
                $isCategorySection = request()->is('danh-muc/*');
                $isBlogSection = request()->is('danh-muc-bai-viet/*') || request()->is('bai-viet/*') || request()->is('video') || request()->is('video-shop') || request()->is('xem-video/*');
                $isCartSection = request()->is('gio-hang');
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
                $isStorefrontAuthRoute = request()->is('dang-nhap') ||
                    request()->is('dang-ky') ||
                    request()->is('login-customer') ||
                    request()->is('add-customer') ||
                    request()->is('dang-xuat-khach-hang') ||
                    request()->is('logout-checkout') ||
                    request()->is('quen-mat-khau') ||
                    request()->is('update-new-pass') ||
                    request()->is('login-facebook-customer') ||
                    request()->is('customer/facebook/callback') ||
                    request()->is('login-customer-google') ||
                    request()->is('customer/google/callback');
                $currentPageRedirectTo = $isStorefrontAuthRoute ? null : request()->fullUrl();
                $buildLoginUrl = function (string $redirectKey = 'history', ?string $redirectTo = null) {
                    $query = ['redirect' => $redirectKey];

                    if ($redirectTo) {
                        $query['redirect_to'] = $redirectTo;
                    }

                    return url('/dang-nhap') . '?' . http_build_query($query);
                };
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
                $searchTrendingKeywords = collect(config('storefront.search_suggest.trending_keywords', []))
                    ->filter(fn($keyword) => filled($keyword))
                    ->take(6)
                    ->values();
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
                $headerSearchHintLinks = $storefrontLink::normalize($searchHintLinks, 'href')
                    ->take(2)
                    ->values();
                $desktopCategoryIconMap = [
                    'laptop' => 'fa-laptop',
                    'laptop-gaming' => 'fa-gamepad',
                    'pc' => 'fa-desktop',
                    'pc-gaming' => 'fa-desktop',
                    'linh-kien' => 'fa-microchip',
                    'phu-kien' => 'fa-plug',
                    'dien-thoai' => 'fa-mobile',
                    'man-hinh' => 'fa-television',
                    'chuot' => 'fa-mouse-pointer',
                    'ban-phim' => 'fa-keyboard-o',
                    'tai-nghe' => 'fa-headphones',
                    'loa' => 'fa-volume-up',
                    'camera' => 'fa-camera',
                    'ghe-gaming' => 'fa-suitcase',
                    'ban-ghe' => 'fa-suitcase',
                ];
                $desktopCategoryUtilityLinks = collect([
                    [
                        'url' => url('/so-sanh'),
                        'icon' => 'fa-random',
                        'label' => 'So sánh sản phẩm',
                        'note' => 'Đặt cạnh các lựa chọn để chốt nhanh hơn',
                    ],
                    [
                        'url' => url('/goi-y-san-pham'),
                        'icon' => 'fa-compass',
                        'label' => config('storefront.labels.recommend_for_you', 'Gợi ý cho bạn'),
                        'note' => 'Tìm cấu hình phù hợp với nhu cầu thực tế',
                    ],
                    [
                        'url' => url('/video-shop'),
                        'icon' => 'fa-play-circle',
                        'label' => 'Video công nghệ',
                        'note' => 'Xem review và điểm nhanh đáng chú ý',
                    ],
                    [
                        'url' => url('/lien-he'),
                        'icon' => 'fa-headphones',
                        'label' => 'Hỗ trợ mua nhanh',
                        'note' => 'Nhận tư vấn trực tiếp từ đội ngũ bán hàng',
                    ],
                ]);
                $desktopCategoryNavQuicklinks = $desktopCategoryUtilityLinks->take(2)->values();
                $desktopCategoryProductTable = (new \App\Infrastructure\Persistence\Eloquent\Catalog\Product())->getTable();
                $desktopCategoryBrandTable = (new \App\Infrastructure\Persistence\Eloquent\Catalog\Brand())->getTable();
                $desktopCategoryHasProductTable = \Illuminate\Support\Facades\Schema::hasTable($desktopCategoryProductTable);
                $desktopCategoryHasBrandTable = \Illuminate\Support\Facades\Schema::hasTable($desktopCategoryBrandTable);
                $desktopCategoryProductColumns = [
                    'is_active' => $desktopCategoryHasProductTable && \Illuminate\Support\Facades\Schema::hasColumn($desktopCategoryProductTable, 'is_active'),
                    'primary_category_id' => $desktopCategoryHasProductTable && \Illuminate\Support\Facades\Schema::hasColumn($desktopCategoryProductTable, 'primary_category_id'),
                    'brand_id' => $desktopCategoryHasProductTable && \Illuminate\Support\Facades\Schema::hasColumn($desktopCategoryProductTable, 'brand_id'),
                    'price' => $desktopCategoryHasProductTable && \Illuminate\Support\Facades\Schema::hasColumn($desktopCategoryProductTable, 'price'),
                    'sale_price' => $desktopCategoryHasProductTable && \Illuminate\Support\Facades\Schema::hasColumn($desktopCategoryProductTable, 'sale_price'),
                    'is_featured' => $desktopCategoryHasProductTable && \Illuminate\Support\Facades\Schema::hasColumn($desktopCategoryProductTable, 'is_featured'),
                    'views_count' => $desktopCategoryHasProductTable && \Illuminate\Support\Facades\Schema::hasColumn($desktopCategoryProductTable, 'views_count'),
                ];
                $desktopCategoryCatalogReady = $desktopCategoryProductColumns['is_active'] && $desktopCategoryProductColumns['primary_category_id'];
                $desktopCategoryCanSummarizeBrands = $desktopCategoryCatalogReady && $desktopCategoryHasBrandTable && $desktopCategoryProductColumns['brand_id'];
                $desktopCategoryCanQueryPriceRanges = $desktopCategoryCatalogReady && \App\Support\ProductPriceSql::canQueryEffectivePrice($desktopCategoryProductTable);
                $desktopCategoryCanLoadFeaturedProducts = $desktopCategoryCatalogReady;
                $desktopCategoryDescendantIds = function (int $rootId) use ($categoryItems) {
                    $pending = collect([$rootId]);
                    $seen = collect();

                    while ($pending->isNotEmpty()) {
                        $currentId = (int) $pending->shift();

                        if ($currentId <= 0 || $seen->contains($currentId)) {
                            continue;
                        }

                        $seen->push($currentId);

                        $childIds = $categoryItems
                            ->filter(fn($entry) => (int) ($entry->category_parent ?? 0) === $currentId)
                            ->pluck('category_id')
                            ->map(fn($id) => (int) $id)
                            ->filter(fn($id) => $id > 0)
                            ->values();

                        foreach ($childIds as $childId) {
                            if (! $seen->contains($childId)) {
                                $pending->push($childId);
                            }
                        }
                    }

                    return $seen->values();
                };
                $formatDesktopCategoryPrice = function (float $amount): string {
                    $amountInMillions = round($amount / 1000000, 1);
                    $formatted = number_format($amountInMillions, 1, ',', '.');
                    $formatted = rtrim(rtrim($formatted, '0'), ',');

                    return $formatted . ' triệu';
                };
                $desktopCategoryEffectivePriceExpression = \App\Support\ProductPriceSql::effectivePriceExpression($desktopCategoryProductTable);
                $desktopCategoryPriceLinks = function (float $minPrice, float $maxPrice, string $slug) use ($formatDesktopCategoryPrice) {
                    if ($minPrice <= 0 || $maxPrice <= 0 || blank($slug)) {
                        return collect();
                    }

                    if ($minPrice > $maxPrice) {
                        [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
                    }

                    if (abs($maxPrice - $minPrice) < 1000000) {
                        return collect([[
                            'label' => 'Giá tốt từ ' . $formatDesktopCategoryPrice($minPrice),
                            'url' => url('/danh-muc/' . $slug) . '?' . http_build_query([
                                'start_price' => max(0, (int) floor($minPrice)),
                            ]),
                            'icon' => 'fa-money',
                            'note' => 'Lọc nhanh theo tầm giá đang bán',
                        ]]);
                    }

                    $start = (int) floor($minPrice / 1000000) * 1000000;
                    $end = (int) ceil($maxPrice / 1000000) * 1000000;
                    $span = max(1000000, (int) ceil((($end - $start) / 3) / 1000000) * 1000000);

                    $ranges = collect();

                    for ($index = 0; $index < 3; $index++) {
                        $rangeStart = $start + ($span * $index);

                        if ($rangeStart >= $end) {
                            break;
                        }

                        $rangeEnd = min($end, $rangeStart + $span);
                        $isLastRange = $index === 2 || $rangeEnd >= $end;

                        $ranges->push([
                            'label' => $isLastRange
                                ? 'Từ ' . $formatDesktopCategoryPrice($rangeStart)
                                : $formatDesktopCategoryPrice($rangeStart) . ' - ' . $formatDesktopCategoryPrice(max($rangeStart, $rangeEnd - 1)),
                            'url' => url('/danh-muc/' . $slug) . '?' . http_build_query(array_filter([
                                'start_price' => max(0, (int) floor($rangeStart)),
                                'end_price' => $isLastRange ? null : max((int) floor($rangeStart), (int) floor($rangeEnd - 1)),
                            ])),
                            'icon' => $isLastRange ? 'fa-diamond' : 'fa-money',
                            'note' => 'Lọc nhanh theo khoảng giá phổ biến',
                        ]);
                    }

                    return $ranges->unique('label')->values();
                };
                $desktopCategoryThemeForSlug = function (string $slug): string {
                    $slug = \Illuminate\Support\Str::slug($slug, '-');

                    return match (true) {
                        \Illuminate\Support\Str::contains($slug, ['dien-thoai', 'tablet', 'ipad', 'iphone']) => 'mobile',
                        \Illuminate\Support\Str::contains($slug, ['laptop', 'macbook']) => 'laptop',
                        \Illuminate\Support\Str::contains($slug, ['man-hinh', 'monitor']) => 'display',
                        \Illuminate\Support\Str::contains($slug, ['linh-kien', 'pc', 'mainboard', 'vga']) => 'hardware',
                        \Illuminate\Support\Str::contains($slug, ['phu-kien', 'chuot', 'ban-phim', 'tai-nghe', 'loa']) => 'gear',
                        default => 'core',
                    };
                };
                $currentCategorySlug = request()->is('danh-muc/*') ? request()->segment(2) : null;
                $desktopCategoryGroups = $rootCategoryItems
                    ->take(9)
                    ->map(function ($item) use ($categoryItems, $rootCategoryItems, $storefrontLabel, $desktopCategoryIconMap, $desktopCategoryUtilityLinks, $desktopCategoryCatalogReady, $desktopCategoryCanSummarizeBrands, $desktopCategoryCanQueryPriceRanges, $desktopCategoryCanLoadFeaturedProducts, $desktopCategoryProductColumns, $desktopCategoryDescendantIds, $desktopCategoryPriceLinks, $desktopCategoryEffectivePriceExpression, $desktopCategoryThemeForSlug, $currentCategorySlug, $featuredCategoryLabel, $featuredBlogLabel) {
                        $children = $categoryItems
                            ->filter(
                                fn($child) => (int) ($child->category_parent ?? 0) === (int) ($item->category_id ?? 0) &&
                                    (int) ($child->category_status ?? 0) === 0 &&
                                    ! empty($child->slug_category_product),
                            )
                            ->sortBy(
                                fn($child) => sprintf(
                                    '%010d-%010d',
                                    (int) ($child->category_order ?? 0),
                                    (int) ($child->category_id ?? 0),
                                ),
                            )
                            ->values();

                        $slug = (string) ($item->slug_category_product ?? '');
                        $normalizedSlug = \Illuminate\Support\Str::slug($slug, '-');
                        $label = $storefrontLabel::category($item->category_name, $slug);
                        $isActive = $currentCategorySlug === $slug || $children->contains(
                            fn($child) => ($child->slug_category_product ?? null) === $currentCategorySlug,
                        );
                        $childLinks = $children
                            ->take(12)
                            ->map(fn($child) => [
                                'label' => $storefrontLabel::category($child->category_name, $child->slug_category_product),
                                'url' => url('/danh-muc/' . $child->slug_category_product),
                                'icon' => $desktopCategoryIconMap[\Illuminate\Support\Str::slug((string) $child->slug_category_product, '-')] ?? ($desktopCategoryIconMap[$normalizedSlug] ?? 'fa-angle-right'),
                                'note' => 'Mở nhanh danh mục con',
                            ])
                            ->values();
                        $relatedRoots = $rootCategoryItems
                            ->filter(
                                fn($root) => (int) ($root->category_status ?? 0) === 0 &&
                                    ! empty($root->slug_category_product) &&
                                    $root->slug_category_product !== $slug,
                            )
                            ->take(6)
                            ->map(fn($root) => [
                                'label' => $storefrontLabel::category($root->category_name, $root->slug_category_product),
                                'url' => url('/danh-muc/' . $root->slug_category_product),
                                'icon' => $desktopCategoryIconMap[\Illuminate\Support\Str::slug((string) $root->slug_category_product, '-')] ?? 'fa-th-large',
                                'note' => 'Khám phá nhóm liên quan',
                            ])
                            ->values();
                        $descendantIds = $desktopCategoryDescendantIds((int) ($item->category_id ?? 0));
                        $brandLinks = collect();
                        $priceLinks = collect();
                        $featuredProducts = collect();

                        if ($desktopCategoryCatalogReady && $descendantIds->isNotEmpty()) {
                            $productsQuery = \App\Infrastructure\Persistence\Eloquent\Catalog\Product::query()
                                ->where('is_active', true)
                                ->whereIn('primary_category_id', $descendantIds->all());

                            if ($desktopCategoryCanSummarizeBrands) {
                                $brandSummary = (clone $productsQuery)
                                    ->selectRaw('brand_id, COUNT(*) as product_count')
                                    ->whereNotNull('brand_id')
                                    ->groupBy('brand_id')
                                    ->orderByDesc('product_count')
                                    ->limit(6)
                                    ->get();

                                if ($brandSummary->isNotEmpty()) {
                                    $brandMap = \App\Infrastructure\Persistence\Eloquent\Catalog\Brand::query()
                                        ->where('is_active', true)
                                        ->whereIn(
                                            'id',
                                            $brandSummary->pluck('brand_id')->map(fn($id) => (int) $id)->all(),
                                        )
                                        ->get()
                                        ->keyBy('id');

                                    $brandLinks = $brandSummary
                                        ->map(function ($summary) use ($brandMap) {
                                            $brand = $brandMap->get((int) $summary->brand_id);

                                            if (! $brand || blank($brand->brand_slug)) {
                                                return null;
                                            }

                                            return [
                                                'label' => $brand->brand_name,
                                                'url' => url('/thuong-hieu/' . $brand->brand_slug),
                                                'icon' => 'fa-certificate',
                                                'note' => (int) ($summary->product_count ?? 0) . ' sản phẩm đang mở bán',
                                            ];
                                        })
                                        ->filter()
                                        ->values();

                                    if ($brandLinks->isEmpty()) {
                                        $brandLinks = $brandMap
                                            ->take(6)
                                            ->map(fn($brand) => [
                                                'label' => $brand->brand_name,
                                                'url' => blank($brand->brand_slug) ? url('/danh-muc/' . $slug) : url('/thuong-hieu/' . $brand->brand_slug),
                                                'icon' => 'fa-certificate',
                                                'note' => 'Thương hiệu đang có trong nhóm này',
                                            ])
                                            ->values();
                                    }
                                }
                            }

                            if ($desktopCategoryCanQueryPriceRanges) {
                                $priceStats = (clone $productsQuery)
                                    ->selectRaw(
                                        'MIN(' . $desktopCategoryEffectivePriceExpression . ') as min_price, MAX(' . $desktopCategoryEffectivePriceExpression . ') as max_price'
                                    )
                                    ->first();

                                $priceLinks = $desktopCategoryPriceLinks(
                                    (float) ($priceStats->min_price ?? 0),
                                    (float) ($priceStats->max_price ?? 0),
                                    $slug,
                                );
                            }

                            if ($desktopCategoryCanLoadFeaturedProducts) {
                                $featuredProductsQuery = clone $productsQuery;

                                if ($desktopCategoryCanSummarizeBrands) {
                                    $featuredProductsQuery->with('brand');
                                }

                                if ($desktopCategoryProductColumns['is_featured']) {
                                    $featuredProductsQuery->orderByDesc('is_featured');
                                }

                                if ($desktopCategoryProductColumns['views_count']) {
                                    $featuredProductsQuery->orderByDesc('views_count');
                                }

                                $featuredProducts = $featuredProductsQuery
                                    ->limit(3)
                                    ->get()
                                    ->map(function ($product) use ($desktopCategoryCanSummarizeBrands) {
                                        return [
                                            'label' => $product->product_name,
                                            'url' => url('/chi-tiet/' . $product->product_slug),
                                            'image' => $product->product_image ? asset($product->product_image) : asset('uploads/product/no-image.svg'),
                                            'brand' => $desktopCategoryCanSummarizeBrands && $product->relationLoaded('brand')
                                                ? $product->brand?->brand_name
                                                : null,
                                            'price' => (float) $product->final_price,
                                            'original_price' => (float) ($product->price ?? $product->final_price),
                                        ];
                                    })
                                    ->values();
                            }
                        }

                        $contextLabel = \Illuminate\Support\Str::lower($label);
                        $contextUtilityLinks = $desktopCategoryUtilityLinks
                            ->map(function ($link) use ($contextLabel) {
                                $contextNote = match ($link['label']) {
                                    'So sánh sản phẩm' => 'Đặt cạnh các mẫu ' . $contextLabel . ' để chốt nhanh hơn',
                                    'Gợi ý cho bạn' => 'Tìm cấu hình ' . $contextLabel . ' phù hợp nhu cầu thực tế',
                                    'Video công nghệ' => 'Xem video mở hộp và đánh giá nhanh cho nhóm ' . $contextLabel,
                                    'Hỗ trợ mua nhanh' => 'Nhận tư vấn trực tiếp cho nhóm ' . $contextLabel,
                                    default => $link['note'] ?? 'Lối vào hữu ích cho nhóm ' . $contextLabel,
                                };

                                return array_merge($link, [
                                    'note' => $contextNote,
                                ]);
                            })
                            ->values();

                        $sections = collect();

                        if ($childLinks->isNotEmpty()) {
                            $sections->push([
                                'title' => 'Danh mục con nổi bật',
                                'links' => $childLinks->take(6)->values(),
                            ]);
                        }

                        if ($brandLinks->isNotEmpty()) {
                            $sections->push([
                                'title' => 'Thương hiệu nổi bật',
                                'links' => $brandLinks->take(4)->values(),
                            ]);
                        }

                        if ($priceLinks->isNotEmpty()) {
                            $sections->push([
                                'title' => 'Theo tầm giá',
                                'links' => $priceLinks->take(4)->values(),
                            ]);
                        }

                        if ($sections->count() < 4 && $relatedRoots->isNotEmpty()) {
                            $sections->push([
                                'title' => 'Khám phá thêm',
                                'links' => $relatedRoots->take(4)->values(),
                            ]);
                        }

                        if ($sections->count() < 4) {
                            $sections->push([
                                'title' => 'Mua nhanh & hỗ trợ',
                                'links' => $contextUtilityLinks->take(4)->values(),
                            ]);
                        }

                        $sections = $sections
                            ->unique('title')
                            ->take(4)
                            ->values();

                        $railTitle = 'Lối vào nhanh';
                        $supportLinks = $priceLinks
                            ->take(2)
                            ->merge($brandLinks->take(1))
                            ->merge($childLinks->take(1))
                            ->merge($contextUtilityLinks)
                            ->unique('label')
                            ->take(4)
                            ->values();

                        if ($priceLinks->isNotEmpty()) {
                            $railTitle = 'Mốc chọn nhanh';
                        } elseif ($brandLinks->isNotEmpty()) {
                            $railTitle = 'Thương hiệu nên xem';
                        } elseif ($childLinks->isNotEmpty()) {
                            $railTitle = 'Nhóm nổi bật';
                        }

                        $promoTags = $childLinks
                            ->take(2)
                            ->pluck('label')
                            ->merge($brandLinks->take(2)->pluck('label'))
                            ->values();

                        if ($promoTags->isEmpty()) {
                            $promoTags = collect([$label, $featuredCategoryLabel, $featuredBlogLabel])
                                ->filter()
                                ->unique()
                                ->take(3)
                                ->values();
                        }

                        $highlightCards = collect([
                            $childLinks->isNotEmpty() ? [
                                'icon' => 'fa-sitemap',
                                'value' => min($childLinks->count(), 12),
                                'label' => 'Nhóm con',
                                'note' => 'Mở nhanh các nhánh đang bán',
                            ] : null,
                            $brandLinks->isNotEmpty() ? [
                                'icon' => 'fa-certificate',
                                'value' => $brandLinks->count(),
                                'label' => 'Thương hiệu',
                                'note' => 'Các hãng đang có trong nhóm này',
                            ] : null,
                            $priceLinks->isNotEmpty() ? [
                                'icon' => 'fa-money',
                                'value' => $priceLinks->count(),
                                'label' => 'Mốc giá',
                                'note' => 'Lọc nhanh theo ngân sách thực tế',
                            ] : [
                                'icon' => 'fa-bolt',
                                'value' => $supportLinks->count(),
                                'label' => 'Lối vào',
                                'note' => 'Các đường tắt hữu ích để mua nhanh',
                            ],
                        ])->filter()->take(3)->values();

                        $promoStats = collect([
                            $childLinks->isNotEmpty() ? [
                                'value' => min($childLinks->count(), 12),
                                'label' => 'nhóm con',
                            ] : null,
                            $brandLinks->isNotEmpty() ? [
                                'value' => $brandLinks->count(),
                                'label' => 'thương hiệu',
                            ] : null,
                            $priceLinks->isNotEmpty() ? [
                                'value' => $priceLinks->count(),
                                'label' => 'mốc giá',
                            ] : null,
                        ])->filter()->take(3)->values();

                        $theme = $desktopCategoryThemeForSlug($slug);

                        return [
                            'id' => 'shopw-desktop-category-' . ($item->category_id ?? $normalizedSlug ?: uniqid()),
                            'label' => $label,
                            'url' => url('/danh-muc/' . $slug),
                            'icon' => $desktopCategoryIconMap[$normalizedSlug] ?? 'fa-th-large',
                            'theme' => $theme,
                            'summary' => $children->isNotEmpty()
                                ? 'Mở nhanh ' . min($children->count(), 12) . ' nhóm con'
                                : ($brandLinks->isNotEmpty()
                                    ? 'Tìm theo thương hiệu và nhóm phổ biến'
                                    : ($priceLinks->isNotEmpty()
                                        ? 'Chọn nhanh theo tầm giá phù hợp'
                                        : 'Mở lối vào mua sắm nhanh')),
                            'sections' => $sections->values(),
                            'highlights' => $highlightCards,
                            'rail_title' => $railTitle,
                            'support_links' => $supportLinks,
                            'featured_products' => $featuredProducts,
                            'promo_title' => 'Chọn nhanh ' . $label,
                            'promo_note' => 'Đi thẳng vào nhóm sản phẩm đang được quan tâm nhiều và rút ngắn thời gian tìm cấu hình phù hợp.',
                            'promo_tags' => $promoTags,
                            'promo_stats' => $promoStats,
                            'active' => $isActive,
                        ];
                    })
                    ->values();

                if ($desktopCategoryGroups->where('active', true)->isEmpty() && $desktopCategoryGroups->isNotEmpty()) {
                    $desktopCategoryGroups = $desktopCategoryGroups->map(function ($group, $index) {
                        $group['active'] = $index === 0;

                        return $group;
                    });
                }
                $checkoutLoginUrl = $buildLoginUrl('checkout', url('/checkout'));
                $accountLoginUrl = $buildLoginUrl('history', $currentPageRedirectTo);
                $checkoutUrl = match (true) {
                    $isAuthenticatedCustomer && $shipping_id == null => url('/checkout'),
                    $isAuthenticatedCustomer && $shipping_id != null => url('/payment'),
                    default => $checkoutLoginUrl,
                };
                $accountUtilityUrl = $isAuthenticatedCustomer ? url('/history') : $accountLoginUrl;
                $accountUtilityLabel = $isAuthenticatedCustomer ? 'Đơn hàng của tôi' : 'Đăng nhập';
                $hasCartInSession = ! empty(Session::get('cart', []));
                $headerCustomerSummaryTitle = collect([$headerCustomerName, $headerCustomerEmail, $headerCustomerPhone])
                    ->filter()
                    ->implode(' • ');
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
                ];
                if ($isAuthenticatedCustomer) {
                    $mobileQuicklinks[] = [
                        'href' => url('/history'),
                        'icon' => 'fa-user-circle-o',
                        'label' => 'Đơn hàng của tôi',
                        'note' => 'Xem lịch sử mua sắm và trạng thái hiện tại',
                        'badge' => 'Tài khoản',
                        'class' => '',
                    ];
                    $mobileQuicklinks[] = [
                        'href' => url('/dang-xuat-khach-hang?redirect=login'),
                        'icon' => 'fa-sign-out',
                        'label' => 'Đăng xuất',
                        'note' => 'Thoát khỏi tài khoản hiện tại',
                        'badge' => 'Thoát',
                        'class' => '',
                    ];
                } else {
                    $mobileQuicklinks[] = [
                        'href' => $accountLoginUrl,
                        'icon' => 'fa-user-circle-o',
                        'label' => 'Đăng nhập',
                        'note' => 'Đăng nhập để theo dõi đơn và thanh toán nhanh hơn',
                        'badge' => 'Tài khoản',
                        'class' => '',
                    ];
                }
                $mobileQuicklinks = $storefrontLink::normalize($mobileQuicklinks, 'href');
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
                <div class="shopw-header-center shopw-header-center--category-hidden">
                    <div class="shopw-header-category-shell shopw-header-category-shell--review-hidden" aria-hidden="true">
                        <button class="shopw-cat-btn shopw-mobile-nav-toggle" type="button" data-nav-intent="categories"
                            data-desktop-panel="#shopw-desktop-category-panel" aria-label="Mở danh mục"
                            aria-haspopup="menu" aria-expanded="false">
                            <span class="shopw-cat-btn-icon"><i class="fa fa-bars"></i></span>
                            <span class="shopw-cat-btn-copy">
                                <strong>Danh mục</strong>
                            </span>
                        </button>

                        <div class="shopw-desktop-category-panel" id="shopw-desktop-category-panel" hidden>
                            <div class="shopw-desktop-category-nav" role="tablist" aria-label="Danh mục nhanh">
                                @foreach ($desktopCategoryGroups as $group)
                                    <button type="button"
                                        class="shopw-desktop-category-trigger shopw-desktop-category-trigger--{{ $group['theme'] ?? 'core' }}{{ $group['active'] ? ' is-active' : '' }}"
                                        data-category-pane="#{{ $group['id'] }}"
                                        aria-selected="{{ $group['active'] ? 'true' : 'false' }}">
                                        <span class="shopw-desktop-category-trigger-icon">
                                            <i class="fa {{ $group['icon'] }}"></i>
                                        </span>
                                        <span class="shopw-desktop-category-trigger-copy">
                                            <strong>{{ $group['label'] }}</strong>
                                            <small>{{ $group['summary'] }}</small>
                                        </span>
                                        <i class="fa fa-angle-right"></i>
                                    </button>
                                @endforeach

                                @if ($desktopCategoryNavQuicklinks->isNotEmpty())
                                    <div class="shopw-desktop-category-nav-foot">
                                        <span class="shopw-desktop-category-nav-foot-title">Đi nhanh</span>
                                        <div class="shopw-desktop-category-nav-foot-links">
                                            @foreach ($desktopCategoryNavQuicklinks as $quicklink)
                                                <a href="{{ $quicklink['url'] ?? $quicklink['href'] }}" class="shopw-desktop-category-nav-foot-link">
                                                    <span class="shopw-desktop-category-nav-foot-icon">
                                                        <i class="fa {{ $quicklink['icon'] }}"></i>
                                                    </span>
                                                    <span class="shopw-desktop-category-nav-foot-copy">
                                                        <strong>{{ $quicklink['label'] }}</strong>
                                                        <small>{{ $quicklink['note'] }}</small>
                                                    </span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="shopw-desktop-category-content">
                                @foreach ($desktopCategoryGroups as $group)
                                    <section class="shopw-desktop-category-pane{{ $group['active'] ? ' is-active' : '' }}"
                                        id="{{ $group['id'] }}" @if (! $group['active']) hidden @endif>
                                        <div class="shopw-desktop-category-pane-head">
                                            <div class="shopw-desktop-category-pane-copy">
                                                <span>Khám phá theo nhóm</span>
                                                <strong>{{ $group['label'] }}</strong>
                                                <p>Chọn nhanh danh mục con và các lối vào phù hợp với nhu cầu mua sắm hiện tại.</p>
                                            </div>
                                            <a href="{{ $group['url'] ?? $group['href'] }}" class="shopw-desktop-category-pane-cta">
                                                <span>Xem tất cả</span>
                                                <i class="fa fa-arrow-right"></i>
                                            </a>
                                        </div>

                                        @if (! empty($group['highlights']))
                                            <div class="shopw-desktop-category-highlights">
                                                @foreach ($group['highlights'] as $highlight)
                                                    <div class="shopw-desktop-category-highlight">
                                                        <span class="shopw-desktop-category-highlight-icon">
                                                            <i class="fa {{ $highlight['icon'] }}"></i>
                                                        </span>
                                                        <span class="shopw-desktop-category-highlight-copy">
                                                            <strong>{{ $highlight['value'] }}</strong>
                                                            <span>{{ $highlight['label'] }}</span>
                                                            <small>{{ $highlight['note'] }}</small>
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="shopw-desktop-category-pane-grid">
                                            <div class="shopw-desktop-category-sections">
                                                @foreach ($group['sections'] as $section)
                                                    <section class="shopw-desktop-category-section">
                                                        <span class="shopw-desktop-category-section-title">{{ $section['title'] }}</span>
                                                        <div class="shopw-desktop-category-section-list">
                                                            @foreach ($section['links'] as $link)
                                                                <a href="{{ $link['url'] ?? $link['href'] }}" class="shopw-desktop-category-section-link">
                                                                    <span class="shopw-desktop-category-section-link-icon">
                                                                        <i class="fa {{ $link['icon'] ?? 'fa-angle-right' }}"></i>
                                                                    </span>
                                                                    <span class="shopw-desktop-category-section-link-copy">
                                                                        <strong>{{ $link['label'] }}</strong>
                                                                        @if (! empty($link['note']))
                                                                            <small>{{ $link['note'] }}</small>
                                                                        @endif
                                                                    </span>
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </section>
                                                @endforeach
                                            </div>

                                            <aside class="shopw-desktop-category-rail">
                                                <div class="shopw-desktop-category-pane-block">
                                                    <span class="shopw-desktop-category-pane-title">{{ $group['rail_title'] ?? 'Lối vào nhanh' }}</span>
                                                    <div class="shopw-desktop-category-links">
                                                        @foreach ($group['support_links'] as $link)
                                                            <a href="{{ $link['url'] ?? $link['href'] }}" class="shopw-desktop-category-link">
                                                                <span class="shopw-desktop-category-link-icon">
                                                                    <i class="fa {{ $link['icon'] }}"></i>
                                                                </span>
                                                                <span class="shopw-desktop-category-link-copy">
                                                                    <strong>{{ $link['label'] }}</strong>
                                                                    <small>{{ $link['note'] }}</small>
                                                                </span>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                @if (! empty($group['featured_products']))
                                                    <div class="shopw-desktop-category-pane-block">
                                                        <span class="shopw-desktop-category-pane-title">Sản phẩm nổi bật</span>
                                                        <div class="shopw-desktop-category-products">
                                                            @foreach ($group['featured_products'] as $featuredProduct)
                                                                <a href="{{ $featuredProduct['url'] ?? $featuredProduct['href'] }}" class="shopw-desktop-category-product-card">
                                                                    <span class="shopw-desktop-category-product-thumb">
                                                                        <img src="{{ $featuredProduct['image'] }}" alt="{{ $group['label'] }} nổi bật">
                                                                    </span>
                                                                    <span class="shopw-desktop-category-product-copy">
                                                                        @if (! empty($featuredProduct['brand']))
                                                                            <small>{{ $featuredProduct['brand'] }}</small>
                                                                        @endif
                                                                        <strong>{{ $group['label'] }} đang được quan tâm</strong>
                                                                        <span class="shopw-desktop-category-product-price">
                                                                            {{ number_format((float) $featuredProduct['price'], 0, ',', '.') }}đ
                                                                        </span>
                                                                    </span>
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                <a href="{{ $group['url'] ?? $group['href'] }}" class="shopw-desktop-category-promo shopw-desktop-category-promo--{{ $group['theme'] ?? 'core' }}">
                                                    <span class="shopw-desktop-category-promo-visual" aria-hidden="true">
                                                        <span class="shopw-desktop-category-promo-orb"></span>
                                                        <span class="shopw-desktop-category-promo-icon">
                                                            <i class="fa {{ $group['icon'] }}"></i>
                                                        </span>
                                                    </span>
                                                    <span class="shopw-desktop-category-promo-kicker">Đề xuất hôm nay</span>
                                                    <strong>{{ $group['promo_title'] }}</strong>
                                                    <small>{{ $group['promo_note'] }}</small>
                                                    @if (! empty($group['promo_stats']))
                                                        <span class="shopw-desktop-category-promo-stats">
                                                            @foreach ($group['promo_stats'] as $stat)
                                                                <span class="shopw-desktop-category-promo-stat">
                                                                    <strong>{{ $stat['value'] }}</strong>
                                                                    <small>{{ $stat['label'] }}</small>
                                                                </span>
                                                            @endforeach
                                                        </span>
                                                    @endif
                                                    <span class="shopw-desktop-category-promo-tags">
                                                        @foreach ($group['promo_tags'] as $tag)
                                                            <em>{{ $tag }}</em>
                                                        @endforeach
                                                    </span>
                                                </a>
                                            </aside>
                                        </div>
                                    </section>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="shopw-search-shell">
                        <span data-search-rotator-marker hidden></span>
                        <form action="{{ URL::to('/tim-kiem') }}" autocomplete="off" method="POST" class="shopw-search">
                            {{ csrf_field() }}
                            <label class="shopw-search-entry" for="keywords">
                                <span class="shopw-search-prefix">Bạn cần gì?</span>
                                <span class="shopw-search-input-stage">
                                    <input type="text" name="keywords_submit" id="keywords"
                                        value="{{ old('keywords_submit') }}"
                                        placeholder=""
                                        data-placeholder-base=""
                                        data-search-trends='@json($searchTrendingKeywords->all())'>
                                    <span class="shopw-search-rotator is-visible" data-search-rotator aria-hidden="true">
                                        <span class="shopw-search-rotator-word" data-search-rotator-word>{{ $searchTrendingKeywords->first() ?: 'Laptop gaming' }}</span>
                                    </span>
                                </span>
                            </label>
                            <button type="submit" class="shopw-search-btn" aria-label="Tìm kiếm">
                                <i class="fa fa-search"></i>
                            </button>
                            <div id="search_ajax" class="shopw-search-suggest"></div>
                        </form>
                        @if ($errors->has('keywords_submit'))
                            <div class="shopw-search-error">
                                {{ $errors->first('keywords_submit') }}
                            </div>
                        @endif
                        <div class="shopw-search-hints shopw-search-hints--compact">
                            @foreach ($headerSearchHintLinks as $hint)
                                <a href="{{ $hint['href'] ?? $hint['url'] }}">
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
                            <a href="{{ $supportPhoneHref }}" title="Hotline {{ $supportPhoneDisplay }}">
                                <i class="fa fa-headphones"></i>
                                <span class="shopw-util-label">{{ $supportPhoneDisplay }}</span>
                            </a>
                        </li>
                        <li class="shopw-util shopw-showroom">
                            <a href="{{ URL::to('/lien-he') }}" title="{{ config('storefront.labels.utility_store', 'Cửa hàng') }}"
                                @class(['is-current' => $isContactSection]) @if ($isContactSection) aria-current="page" @endif>
                                <i class="fa fa-map-marker"></i>
                                <span class="shopw-util-label">{{ config('storefront.labels.utility_store', 'Cửa hàng') }}</span>
                            </a>
                        </li>
                        <li class="shopw-util cart-hover shopw-cart">
                            <a href="{{ url('gio-hang') }}" title="Giỏ hàng" @class(['is-current' => $isCartSection])
                                @if ($isCartSection) aria-current="page" @endif>
                                <i class="fa fa-shopping-cart"></i>
                                <span class="shopw-util-label">Giỏ hàng</span>
                                <span class="show-cart"></span>
                                <span class="giohang-hover"></span>
                            </a>
                        </li>
                        @if ($isAuthenticatedCustomer)
                            <li class="shopw-util shopw-auth shopw-auth-menu">
                                <details class="shopw-account-menu">
                                    <summary @class([
                                        'shopw-account-summary',
                                        'shopw-account-summary--avatar-only',
                                        'is-current' => $isAccountSection,
                                    ]) title="{{ $headerCustomerSummaryTitle }}" aria-haspopup="menu" aria-expanded="false">
                                        <span class="shopw-account-avatar">
                                            <img src="{{ $headerCustomerAvatar }}" alt="{{ $headerCustomerName ?: 'Tài khoản' }}">
                                        </span>
                                    </summary>

                                    <div class="shopw-account-dropdown" role="menu">
                                        <div class="shopw-account-dropdown-head">
                                            <span class="shopw-account-dropdown-kicker">Tài khoản</span>
                                            <strong>{{ $headerCustomerName ?: 'Tài khoản của bạn' }}</strong>
                                            @if ($headerCustomerEmail)
                                                <small>{{ $headerCustomerEmail }}</small>
                                            @endif
                                            @if ($headerCustomerPhone)
                                                <small>{{ $headerCustomerPhone }}</small>
                                            @endif
                                        </div>

                                        <div class="shopw-account-dropdown-links">
                                            @if ($hasCartInSession)
                                                <a href="{{ $checkoutUrl }}" class="shopw-account-dropdown-link" role="menuitem">
                                                    <i class="fa fa-credit-card"></i>
                                                    <span>Tiếp tục thanh toán</span>
                                                </a>
                                            @endif
                                            <a href="{{ url('/history') }}" class="shopw-account-dropdown-link" role="menuitem">
                                                <i class="fa fa-history"></i>
                                                <span>Đơn hàng của tôi</span>
                                            </a>
                                            <a href="{{ url('/dang-xuat-khach-hang?redirect=login') }}"
                                                class="shopw-account-dropdown-link is-danger" role="menuitem">
                                                <i class="fa fa-sign-out"></i>
                                                <span>Đăng xuất</span>
                                            </a>
                                        </div>
                                    </div>
                                </details>
                            </li>
                        @else
                            <li class="shopw-util shopw-auth">
                                <a href="{{ $accountUtilityUrl }}" title="{{ $accountUtilityLabel }}"
                                    @class(['is-current' => $isAccountSection]) @if ($isAccountSection) aria-current="page" @endif>
                                    <i class="fa fa-user"></i>
                                    <span class="shopw-util-label">{{ $accountUtilityLabel }}</span>
                                </a>
                            </li>
                        @endif
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
                                <li class="dropdown shopw-nav-group {{ $isCategorySection ? 'is-current' : '' }}" id="shopw-nav-group-categories">
                                    <div class="shopw-nav-row">
                                        <a href="{{ $featuredCategoryUrl }}"
                                            data-storefront-nav-parent="categories"
                                            @class(['is-current' => $isCategorySection])
                                            @if ($isCategorySection) aria-current="page" @endif>
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

                                <li><a href="{{ URL::to('/gio-hang') }}"
                                            data-storefront-nav-key="cart"
                                            @class(['active' => $isCartSection, 'is-current' => $isCartSection])
                                            @if ($isCartSection) aria-current="page" @endif>
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
