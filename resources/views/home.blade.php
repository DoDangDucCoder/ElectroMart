@extends('layout')

@section('body_class', 'g-home')

@section('slider')
    @include('pages.include.slider')
@endsection

@section('content')
    <div class="shopw-homepage">
        @php
            $storefrontLabel = \App\Support\StorefrontLabel::class;
            $stringHelper = \Illuminate\Support\Str::class;
            $categoryIcons = [
                'laptop' => 'fa-laptop',
                'laptop-gaming' => 'fa-gamepad',
                'laptop-van-phong' => 'fa-briefcase',
                'dien-thoai' => 'fa-mobile',
                'iphone' => 'fa-apple',
                'samsung-galaxy' => 'fa-android',
                'android-khac' => 'fa-android',
                'phu-kien' => 'fa-headphones',
                'sac-cap' => 'fa-plug',
                'op-lung-kinh-cuong-luc' => 'fa-shield',
            ];
            $resolvePublicAsset = static function (?string $path): ?string {
                if (blank($path)) {
                    return null;
                }

                return file_exists(public_path($path)) ? asset($path) : null;
            };
            $allCategoryItems = collect($category ?? [])->values();
            $rootCategories = collect($cate_pro_tabs ?? [])->values();
            if ($rootCategories->isEmpty()) {
                $rootCategories = $allCategoryItems
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
            }

            if ($rootCategories->isEmpty()) {
                $rootCategories = $allCategoryItems
                    ->filter(fn($item) => (int) ($item->category_status ?? 0) === 0)
                    ->sortBy(
                        fn($item) => sprintf(
                            '%010d-%010d',
                            (int) ($item->category_order ?? 0),
                            (int) ($item->category_id ?? 0),
                        ),
                    )
                    ->values();
            }
            $tabCategories = collect($cate_pro_tabs ?? [])->values();
            if ($tabCategories->isEmpty()) {
                $tabCategories = $rootCategories->take(6)->values();
            }
            $sliderItems = collect($slider ?? [])->values();
            $blogCategoryItems = collect($category_post ?? [])->values();
            $featuredBlogCategory = $blogCategoryItems->firstWhere('cate_post_slug', 'tin-tuc-cong-nghe')
                ?? $blogCategoryItems->firstWhere('cate_post_slug', 'tin-tuc')
                ?? $blogCategoryItems->first();
            $featuredBlogUrl = $featuredBlogCategory
                ? url('/danh-muc-bai-viet/' . $featuredBlogCategory->cate_post_slug)
                : url('/video-shop');
            $brandItems = collect($brand ?? [])->values();

            $discoveryCards = $rootCategories->take(6)->map(function ($category) use (
                $categoryIcons,
                $mega_menu_data,
                $resolvePublicAsset,
                $storefrontLabel
            ) {
                $slug = $category->slug_category_product;
                $mega = $mega_menu_data[$slug] ?? [];
                $usage = collect($mega['usage'] ?? [])->pluck('name')->filter()->take(3)->values();
                $brands = collect($mega['brands'] ?? [])->pluck('name')->filter()->take(2)->values();
                $description = $usage->isNotEmpty()
                    ? 'Nhóm nổi bật: ' . $usage->take(2)->implode(' • ')
                    : ($brands->isNotEmpty()
                        ? 'Ưu tiên các hãng như ' . $brands->implode(' • ')
                        : 'Đi thẳng vào nhóm sản phẩm đang được người dùng tìm nhiều.');

                return [
                    'title' => $storefrontLabel::category($category->category_name, $slug),
                    'url' => url('/danh-muc/' . $slug),
                    'description' => $description,
                    'tags' => $usage->isNotEmpty() ? $usage->all() : $brands->all(),
                    'image' => $resolvePublicAsset($category->thumbnail_path ?? null),
                    'icon' => $categoryIcons[$slug] ?? 'fa-th-large',
                ];
            })->all();

            $campaignFallbackCopy = [
                'mobile' => 'Tập trung vào smartphone và các nhóm máy đang có nhịp mua cao trong ngày.',
                'gaming' => 'Đẩy các cấu hình và combo thiên về hiệu năng lên đúng điểm nhìn đầu tiên.',
                'lifestyle' => 'Ưu tiên những deal dễ tiếp cận để người dùng có thêm lý do bấm vào danh mục.',
                'accessory' => 'Những món bổ trợ nhỏ nhưng chốt đơn nhanh thường được gom ở khu vực này.',
            ];

            $campaignCards = $rootCategories->take(3)->values()->map(function ($category, $index) use (
                $sliderItems,
                $mega_menu_data,
                $categoryIcons,
                $resolvePublicAsset,
                $storefrontLabel,
                $campaignFallbackCopy
            ) {
                $slide = $sliderItems->get($index);
                $slug = $category->slug_category_product;
                $slideImage = $slide && !empty($slide->slider_image)
                    ? $resolvePublicAsset('uploads/slider/' . $slide->slider_image)
                    : null;
                $image = $slideImage ?: $resolvePublicAsset($category->thumbnail_path ?? null);
                $tone = $slide && in_array($slide->slider_tone ?? null, ['mobile', 'gaming', 'lifestyle', 'accessory'], true)
                    ? $slide->slider_tone
                    : (str_contains($slug, 'laptop') ? 'gaming' : ($slug === 'phu-kien' ? 'accessory' : 'mobile'));
                $mega = $mega_menu_data[$slug] ?? [];
                $tags = collect($mega['usage'] ?? [])->pluck('name')->filter()->take(3)->values();
                if ($tags->isEmpty()) {
                    $tags = collect($mega['brands'] ?? [])->pluck('name')->filter()->take(3)->values();
                }

                return [
                    'tone' => $tone,
                    'kicker' => match ($tone) {
                        'gaming' => 'Điểm rơi hiệu năng',
                        'accessory' => 'Điểm rơi phụ kiện',
                        'lifestyle' => 'Deal chọn lọc',
                        default => 'Điểm rơi danh mục',
                    },
                    'title' => $slide?->slider_alt ?: $storefrontLabel::category($category->category_name, $slug),
                    'description' => $slide?->slider_desc ?: ($campaignFallbackCopy[$tone] ?? 'Ưu tiên những điểm vào danh mục đang có cơ hội chuyển đổi tốt nhất.'),
                    'image' => $image,
                    'url' => url('/danh-muc/' . $slug),
                    'tags' => $tags->all(),
                    'icon' => $categoryIcons[$slug] ?? 'fa-bolt',
                    'cta' => 'Khám phá danh mục',
                ];
            })->all();

            $brandHighlights = $brandItems->filter(fn ($item) => (bool) ($item->is_active ?? true))
                ->sortBy('display_order')
                ->take(10)
                ->map(function ($brandItem) use ($resolvePublicAsset, $stringHelper) {
                    $name = $brandItem->brand_name ?? $brandItem->name;

                    return [
                        'name' => $name,
                        'url' => url('/thuong-hieu/' . ($brandItem->brand_slug ?? $brandItem->slug)),
                        'image' => $resolvePublicAsset($brandItem->logo_path ?? null),
                        'initials' => $stringHelper::upper($stringHelper::substr($name, 0, 2)),
                    ];
                })->all();

            $serviceHighlights = [
                [
                    'icon' => 'fa-comments-o',
                    'title' => 'Chat mua hàng',
                    'description' => 'Hỏi nhanh về sản phẩm và ưu đãi.',
                    'type' => 'button',
                ],
                [
                    'icon' => 'fa-truck',
                    'title' => 'Tra cứu đơn hàng',
                    'description' => 'Theo dõi trạng thái đơn đã đặt.',
                    'url' => url('/history'),
                    'type' => 'link',
                ],
                [
                    'icon' => 'fa-refresh',
                    'title' => config('storefront.labels.compare_products', 'So sánh sản phẩm'),
                    'description' => 'Đặt các mẫu cạnh nhau để chọn nhanh.',
                    'url' => url('/so-sanh'),
                    'type' => 'link',
                ],
                [
                    'icon' => 'fa-headphones',
                    'title' => 'Liên hệ & bảo hành',
                    'description' => 'Đi thẳng tới hỗ trợ kỹ thuật và hậu mãi.',
                    'url' => url('/lien-he'),
                    'type' => 'link',
                ],
            ];

            $contentHighlights = $blogCategoryItems->take(3)->map(function ($item) use ($storefrontLabel) {
                return [
                    'eyebrow' => 'Bài viết',
                    'title' => $storefrontLabel::blogCategory($item->cate_post_name, $item->cate_post_slug),
                    'description' => 'Đi tới chuyên mục để đọc review, hướng dẫn và những cập nhật liên quan tới nhóm sản phẩm này.',
                    'url' => url('/danh-muc-bai-viet/' . $item->cate_post_slug),
                ];
            })->values();

            $contentHighlights->push([
                'eyebrow' => 'Video',
                'title' => 'Xem nhanh nội dung video và demo',
                'description' => 'Một lối vào gọn cho các video giới thiệu, hướng dẫn và nội dung công nghệ ngắn.',
                'url' => url('/video-shop'),
            ]);
            $recommendedProducts = collect($recommended_products ?? [])->values();
            $recommendationGridClass = match (true) {
                $recommendedProducts->count() <= 1 => 'shopw-home-recommendation-grid--solo',
                $recommendedProducts->count() === 2 => 'shopw-home-recommendation-grid--duo',
                default => 'shopw-home-recommendation-grid--full',
            };
            $recommendationCardColumnClass = $recommendedProducts->count() === 1
                ? 'col-xs-12 shopw-home-featured-card'
                : ($recommendedProducts->count() === 2
                    ? 'col-xs-12 col-sm-6'
                    : 'col-xs-12 col-sm-6 col-md-3');
            $recommendationFallbackLinks = [
                [
                    'title' => 'Tìm sản phẩm phù hợp',
                    'description' => 'Mở trang tìm kiếm để lọc nhanh theo tên, mã, danh mục hoặc khoảng giá.',
                    'url' => url('/tim-kiem'),
                    'icon' => 'fa-search',
                ],
                [
                    'title' => config('storefront.labels.compare_products', 'So sánh sản phẩm'),
                    'description' => 'Đặt vài mẫu lên cùng một bàn so sánh trước khi quyết định.',
                    'url' => url('/so-sanh'),
                    'icon' => 'fa-refresh',
                ],
                [
                    'title' => 'Xem video và bài viết',
                    'description' => 'Đi tiếp bằng các nội dung review, hướng dẫn và demo ngắn.',
                    'url' => $featuredBlogUrl,
                    'icon' => 'fa-play-circle-o',
                ],
            ];
        @endphp

        @if (!empty($flash_sale))
            @include('pages.home.partials.flash_sale', [
                'flashSale' => $flash_sale,
            ])
        @endif

        @include('pages.home.partials.discovery_hub', [
            'discoveryCards' => $discoveryCards,
            'cate_pro_tabs' => $tabCategories,
            'storefrontLabel' => $storefrontLabel,
        ])

        @include('pages.home.partials.campaign_board', [
            'campaignCards' => $campaignCards,
        ])

        <section class="features_items shopw-home-section shopw-home-latest-section">
            <div class="shopw-section-head shopw-section-head-split">
                <div>
                    <span class="shopw-section-kicker">Hàng mới lên kệ</span>
                    <h2 class="title text-center">Sản phẩm mới lên kệ</h2>
                    <p class="shopw-section-copy">Giữ block này như một mặt bằng cập nhật nhanh: hàng mới, giá rõ, CTA đủ mạnh và không bị nhiễu bởi quá nhiều phụ trợ.</p>
                </div>
                <a href="{{ url('/trang-chu') }}" class="shopw-section-link">Làm mới danh sách</a>
            </div>

            <div id="all_product" class="row shopw-home-latest-grid"></div>
            <div id="cart_session"></div>
        </section>

        <section class="features_items shopw-home-section shopw-home-recommendation-section">
            <div class="shopw-section-head shopw-section-head-split">
                <div>
                    <span class="shopw-section-kicker">{{ config('storefront.labels.recommend_for_you', 'Gợi ý cho bạn') }}</span>
                    <h2 class="title text-center">Sản phẩm nên xem tiếp</h2>
                    <p class="shopw-section-copy">
                        {{ $recommendation_description ?? 'Ưu tiên từ lịch sử mua hàng, sau đó bổ sung từ lượt xem gần đây và mức độ quan tâm hiện tại.' }}
                    </p>
                </div>
                <a href="{{ url('/goi-y-san-pham') }}" class="shopw-section-link">Xem thêm gợi ý</a>
            </div>

            @if ($recommendedProducts->isNotEmpty())
                <div class="row shopw-search-grid shopw-home-recommendation-grid {{ $recommendationGridClass }}">
                    @include('pages.category.partials.product_cards', [
                        'products' => $recommendedProducts,
                        'emptyMessage' => 'Chưa có dữ liệu gợi ý phù hợp ở thời điểm này.',
                        'emptyStateClass' => 'shopw-home-recommendation-empty',
                        'cardColumnClass' => $recommendationCardColumnClass,
                        'showQuickView' => false,
                        'showCartToggle' => false,
                        'showWishlist' => true,
                        'showCompare' => true,
                    ])
                </div>
            @else
                <div class="shopw-home-recommendation-fallback">
                    @foreach ($recommendationFallbackLinks as $fallbackLink)
                        <a href="{{ $fallbackLink['url'] }}" class="shopw-home-recommendation-fallback-card">
                            <span class="shopw-home-recommendation-fallback-icon">
                                <i class="fa {{ $fallbackLink['icon'] }}"></i>
                            </span>
                            <span class="shopw-home-recommendation-fallback-copy">
                                <strong>{{ $fallbackLink['title'] }}</strong>
                                <small>{{ $fallbackLink['description'] }}</small>
                            </span>
                            <span class="shopw-home-recommendation-fallback-arrow" aria-hidden="true">
                                <i class="fa fa-angle-right"></i>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        @include('pages.home.partials.ecosystem_board', [
            'brandHighlights' => $brandHighlights,
            'serviceHighlights' => $serviceHighlights,
            'contentHighlights' => $contentHighlights,
        ])
    </div>

    <style type="text/css">
        div#quick-cart,
        div#xemnhanh {
            margin-top: 40px;
        }
    </style>

    <div class="modal fade" id="quick-cart" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg shopw-home-modal-dialog" role="document">
            <div class="modal-content shopw-home-modal">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Giỏ hàng của bạn</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="show_quick_cart_alert"></div>
                    <div id="show_quick_cart"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="xemnhanh" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg shopw-home-modal-dialog" role="document">
            <div class="modal-content shopw-home-modal">
                <div class="modal-header">
                    <h5 class="modal-title product_quickview_title">
                        <span id="product_quickview_title"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <style type="text/css">
                        span#product_quickview_content img {
                            width: 100%;
                        }

                        @media screen and (min-width: 768px) {
                            .shopw-home-modal-dialog {
                                width: 700px;
                            }

                            .modal-sm {
                                width: 350px;
                            }
                        }

                        @media screen and (min-width: 992px) {
                            .shopw-home-modal-dialog.modal-lg {
                                width: 1200px;
                            }
                        }
                    </style>
                    <div class="row">
                        <div class="col-md-5">
                            <span id="product_quickview_image"></span>
                            <span id="product_quickview_gallery"></span>
                        </div>
                        <form>
                            @csrf
                            <div id="product_quickview_value"></div>
                            <div class="col-md-7">
                                <h2><span id="product_quickview_title_body"></span></h2>
                                <p>Mã sản phẩm: <span id="product_quickview_id"></span></p>
                                <p style="font-size: 20px; color: brown;font-weight: bold;">Giá sản phẩm: <span
                                        id="product_quickview_price"></span></p>

                                <label>Số lượng:</label>

                                <input name="qty" type="number" min="1" class="cart_product_qty_"
                                    value="1" />

                                <h4 style="font-size: 20px; color: brown;font-weight: bold;">Mô tả sản phẩm</h4>
                                <hr>
                                <p><span id="product_quickview_desc"></span></p>
                                <p><span id="product_quickview_content"></span></p>

                                <div id="product_quickview_button"></div>
                                <div id="beforesend_quickview"></div>
                            </div>
                        </form>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-default redirect-cart">Đi tới giỏ hàng</button>
                </div>
            </div>
        </div>
    </div>
@endsection

