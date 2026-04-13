@extends('layout')

@section('body_class', 'g-product-detail')

@section('content')
    @php
        $storefrontLabel = \App\Support\StorefrontLabel::class;
        $product = $product_details->first();
        $fallbackImage = asset('uploads/product/no-image.svg');
        $primaryImage = $product->product_image ? asset($product->product_image) : $fallbackImage;
        $categoryUrl = $cate_slug ? url('/danh-muc/' . $cate_slug) : null;
        $brandUrl = optional($product->brand)->brand_slug ? url('/thuong-hieu/' . optional($product->brand)->brand_slug) : null;
        $shortDescription = trim(strip_tags((string) $product->product_desc));
        $documentUrl = data_get($product->meta, 'document_path') ? asset(data_get($product->meta, 'document_path')) : null;
        $supportConfig = config('services.storefront_support', []);
        $supportPhoneDisplay = $supportConfig['phone_display'] ?? '1900.0000';
        $supportPhoneHref = $supportConfig['phone_href'] ?? 'tel:19000000';
        $stockAvailable = (int) $product->product_quantity > 0;
        $stockLabel = $stockAvailable ? 'Còn hàng' : 'Tạm hết hàng';
        $stockClass = $stockAvailable ? 'is-in-stock' : 'is-out-stock';
        $productCategoryLabel = $storefrontLabel::category(
            $product_cate ?: $product->category_name,
            $cate_slug,
            'Đang cập nhật',
        );
        $tagList = collect(explode(',', (string) $product->product_tags))
            ->map(static fn($tag) => trim($tag))
            ->filter()
            ->values();
        $detailSupportCards = [
            [
                'icon' => 'fa-truck',
                'title' => 'Giao nhanh',
                'text' => $stockAvailable
                    ? config('storefront.copy.detail_shipping_in_stock', 'Sản phẩm đang có sẵn để xử lý đơn và giao nhanh trong khung giờ hỗ trợ.')
                    : config('storefront.copy.detail_shipping_out_stock', 'Đăng ký nhận tư vấn để được cập nhật ngay khi hàng về kho.'),
            ],
            [
                'icon' => 'fa-shield',
                'title' => 'Hậu mãi rõ ràng',
                'text' => $documentUrl
                    ? 'Có sẵn tài liệu sản phẩm để kiểm tra thông tin và chính sách trước khi mua.'
                    : 'Đội ngũ cửa hàng hỗ trợ chính sách bảo hành và hậu mãi theo từng nhóm hàng.',
            ],
            [
                'icon' => 'fa-comments',
                'title' => 'Tư vấn trước khi mua',
                'text' => config('storefront.copy.detail_prepurchase_help', 'So sánh sản phẩm, liên hệ nhanh hoặc xem thêm gợi ý phù hợp với nhu cầu hiện tại.'),
            ],
        ];
        $detailQuickLinks = [
            [
                'icon' => 'fa-phone',
                'label' => config('storefront.labels.hotline_support', 'Hotline hỗ trợ'),
                'url' => $supportPhoneHref,
            ],
            [
                'icon' => 'fa-random',
                'label' => config('storefront.labels.compare_products', 'So sánh sản phẩm'),
                'url' => url('/so-sanh'),
            ],
            [
                'icon' => 'fa-lightbulb-o',
                'label' => 'Xem thêm gợi ý',
                'url' => url('/goi-y-san-pham'),
            ],
        ];

        $attributeSlides = [];
        foreach ($product_attribute as $index => $attribute) {
            $attributeImage = !empty($attribute->image) ? asset('uploads/attribute/' . $attribute->image) : $fallbackImage;
            $attributeSlides[] = [
                'thumb' => $attributeImage,
                'src' => $attributeImage,
                'alt' => $attribute->name ?: $product->product_name,
                'attribute_key' => $index,
            ];
        }

        $gallerySlides = [];
        foreach ($gallery as $media) {
            $galleryUrl = $media->url ?? ($media->file_path ? asset($media->file_path) : (!empty($media->gallery_image) ? asset('uploads/gallery/' . $media->gallery_image) : null));

            if (! $galleryUrl) {
                continue;
            }

            $gallerySlides[] = [
                'thumb' => $galleryUrl,
                'src' => $galleryUrl,
                'alt' => $media->gallery_name ?? $product->product_name,
                'attribute_key' => null,
            ];
        }

        $imageSlides = array_merge($attributeSlides, $gallerySlides);
        if ($imageSlides === []) {
            $imageSlides[] = [
                'thumb' => $primaryImage,
                'src' => $primaryImage,
                'alt' => $product->product_name,
                'attribute_key' => null,
            ];
        }
    @endphp

    <div class="shopw-pdp">
        <input type="hidden" id="product_viewed_id" value="{{ $product->product_id }}">
        <input type="hidden" id="viewed_productname{{ $product->product_id }}" value="{{ $product->product_name }}">
        <input type="hidden" id="viewed_producturl{{ $product->product_id }}"
            value="{{ url('/chi-tiet/' . $product->product_slug) }}">
        <input type="hidden" id="viewed_productimage{{ $product->product_id }}" value="{{ $primaryImage }}">
        <input type="hidden" id="viewed_productprice{{ $product->product_id }}"
            value="{{ number_format($product->final_price, 0, ',', '.') }} VND">

        <nav aria-label="breadcrumb" class="shopw-pdp-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                @if ($categoryUrl)
                    <li class="breadcrumb-item"><a href="{{ $categoryUrl }}">{{ $productCategoryLabel }}</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $product->product_name }}</li>
            </ol>
        </nav>

        <article class="shopw-pdp-shell">
            <div class="shopw-pdp-gallery-card">
                <div class="shopw-pdp-gallery-head">
                    <span class="shopw-pdp-kicker">Bộ sưu tập hình ảnh</span>
                    <h2>Xem nhanh sản phẩm từ nhiều góc nhìn</h2>
                </div>

                <ul id="imageGallery">
                    @foreach ($imageSlides as $slide)
                        <li
                            @if ($slide['attribute_key'] !== null) class="pro_attr_image_{{ $slide['attribute_key'] }}" @endif
                            data-thumb="{{ $slide['thumb'] }}" data-src="{{ $slide['src'] }}">
                            <img src="{{ $slide['src'] }}" alt="{{ $slide['alt'] }}">
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="shopw-pdp-summary">
                <div class="shopw-pdp-summary-top">
                    <div class="shopw-pdp-badges">
                        <span class="shopw-pdp-badge {{ $stockClass }}">{{ $stockLabel }}</span>
                        <span class="shopw-pdp-badge is-neutral">Mã SP #{{ $product->product_id }}</span>
                        <span class="shopw-pdp-badge is-neutral">{{ number_format($product->product_views) }} lượt xem</span>
                    </div>

                    <h1 class="shopw-pdp-title">{{ $product->product_name }}</h1>

                    <div class="shopw-pdp-rating-summary">
                        <div class="shopw-pdp-stars" aria-label="Average rating {{ $rating }} out of 5">
                            @for ($count = 1; $count <= 5; $count++)
                                <i class="fa {{ $count <= $rating ? 'fa-star' : 'fa-star-o' }}"></i>
                            @endfor
                        </div>
                        <span class="shopw-pdp-rating-value">{{ number_format($rating, 0) }}/5</span>
                    </div>
                </div>

                @if ($shortDescription !== '')
                    <p class="shopw-pdp-short">{{ $shortDescription }}</p>
                @endif

                <div class="shopw-pdp-price-panel">
                    <div class="shopw-pdp-price-wrap">
                        <div class="shopw-pdp-price-label">Giá bán</div>
                        <div class="shopw-pdp-price-value">
                            <input type="hidden" class="cart_product_id_attribute" value="0">
                            <input type="hidden" class="product_id" value="{{ $product->product_id }}">
                            <input type="hidden" class="cart_product_price" value="{{ $product->final_price }}">
                            <input type="hidden" class="cart_price_attribute" value="{{ $product->final_price }}">
                            <div id="price_init" class="shopw-pdp-price-static">
                                @include('partials.storefront.product-price', [
                                    'product' => $product,
                                    'currency' => 'VND',
                                    'showExplanation' => true,
                                ])
                            </div>
                            <span id="price_after_click">Giá {{ number_format($product->final_price, 0, ',', '.') }} VND</span>
                        </div>
                    </div>

                    <div class="shopw-pdp-purchase"
                        data-default-stock="{{ max(0, (int) $product->product_quantity) }}"
                        data-default-label="{{ $stockAvailable ? 'Thêm giỏ hàng' : 'Tạm hết hàng' }}">
                        @csrf
                        <label for="product-detail-qty" class="shopw-pdp-qty-label">Số lượng</label>
                        <input id="product-detail-qty" name="qty" type="number" min="1"
                            max="{{ max(1, (int) $product->product_quantity) }}" class="cart_product_qty"
                            value="{{ $stockAvailable ? 1 : 0 }}" {{ $stockAvailable ? '' : 'disabled' }} />
                        <button type="button" class="btn btn-default add-to-cart"
                            {{ $stockAvailable ? '' : 'disabled' }}>
                            <i class="fa fa-shopping-cart"></i>
                            <span class="shopw-pdp-purchase-btn-label">
                                {{ $stockAvailable ? 'Thêm giỏ hàng' : 'Tạm hết hàng' }}
                            </span>
                        </button>
                        <div id="product-detail-purchase-status" class="shopw-pdp-purchase-status" aria-live="polite"></div>
                    </div>
                </div>

                <div class="shopw-pdp-service-grid">
                    @foreach ($detailSupportCards as $supportCard)
                        <div class="shopw-pdp-service-card">
                            <span class="shopw-pdp-service-icon">
                                <i class="fa {{ $supportCard['icon'] }}"></i>
                            </span>
                            <div class="shopw-pdp-service-copy">
                                <strong>{{ $supportCard['title'] }}</strong>
                                <p>{{ $supportCard['text'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($product_attribute->isNotEmpty())
                    <div class="shopw-pdp-attributes">
                        <div class="shopw-pdp-block-title">Phiên bản khả dụng</div>
                        <div class="shopw-pdp-attribute-grid">
                            @foreach ($product_attribute as $index => $attribute)
                                @php
                                    $attributeImage = !empty($attribute->image) ? asset('/uploads/attribute/' . $attribute->image) : $fallbackImage;
                                @endphp
                                <div class="box-attribute pro_attr_image_click" data-key_image="{{ $index }}"
                                    data-id_attribute="{{ $attribute->id }}"
                                    data-name_attribute="{{ $attribute->name }}"
                                    data-price_attribute="{{ $attribute->price }}"
                                    data-qty_attribute="{{ $attribute->quantity }}"
                                    data-image_attribute="{{ $attribute->image }}">
                                    <div class="shopw-pdp-attribute-media">
                                        <img src="{{ $attributeImage }}" alt="{{ $attribute->name }}">
                                    </div>
                                    <div class="shopw-pdp-attribute-copy">
                                        <strong>{{ $attribute->name }}</strong>
                                        <span>{{ number_format($attribute->price, 0, ',', '.') }} VND</span>
                                        <small>{{ $attribute->quantity }} sản phẩm khả dụng</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="shopw-pdp-meta-grid">
                    <div class="shopw-pdp-meta-item">
                        <span>Thương hiệu</span>
                        @if ($brandUrl)
                            <a href="{{ $brandUrl }}">{{ $product->brand_name ?: 'Đang cập nhật' }}</a>
                        @else
                            <strong>{{ $product->brand_name ?: 'Đang cập nhật' }}</strong>
                        @endif
                    </div>
                    <div class="shopw-pdp-meta-item">
                        <span>Danh mục</span>
                        @if ($categoryUrl)
                            <a href="{{ $categoryUrl }}">{{ $productCategoryLabel }}</a>
                        @else
                            <strong>{{ $productCategoryLabel }}</strong>
                        @endif
                    </div>
                    <div class="shopw-pdp-meta-item">
                        <span>Tồn kho</span>
                        <strong>{{ number_format($product->product_quantity) }} sản phẩm</strong>
                    </div>
                    <div class="shopw-pdp-meta-item">
                        <span>Hỗ trợ nhanh</span>
                        <a href="{{ $supportPhoneHref }}">{{ $supportPhoneDisplay }}</a>
                    </div>
                </div>

                @if ($tagList->isNotEmpty())
                    <div class="shopw-pdp-tags">
                        <div class="shopw-pdp-block-title">Tags</div>
                        <div class="shopw-pdp-tag-list">
                            @foreach ($tagList as $tag)
                                <a href="{{ url('/tag/' . \Illuminate\Support\Str::slug($tag)) }}"
                                    class="shopw-pdp-tag">{{ $tag }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="shopw-pdp-actions">
                    @foreach ($detailQuickLinks as $detailQuickLink)
                        <a href="{{ $detailQuickLink['url'] }}" class="shopw-pdp-action-link is-utility">
                            <i class="fa {{ $detailQuickLink['icon'] }}"></i>{{ $detailQuickLink['label'] }}
                        </a>
                    @endforeach
                    @if ($documentUrl)
                        <a href="{{ $documentUrl }}" target="_blank" rel="noopener" class="shopw-pdp-action-link">
                            <i class="fa fa-file-text-o"></i>Tài liệu sản phẩm
                        </a>
                    @endif
                    <div class="fb-share-button" data-href="{{ $url_canonical }}" data-layout="button"
                        data-size="small">
                        <a target="_blank" href="{{ $url_canonical }}" rel="noopener"
                            class="fb-xfbml-parse-ignore shopw-pdp-action-link">
                            <i class="fa fa-share-alt"></i>Chia sẻ
                        </a>
                    </div>
                </div>
            </div>
        </article>

        <div class="category-tab shop-details-tab shopw-pdp-tabs">
            <div class="shopw-pdp-tabs-head">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#details" data-toggle="tab">Mô tả</a></li>
                    <li><a href="#companyprofile" data-toggle="tab">Chi tiết sản phẩm</a></li>
                    <li><a href="#reviews" data-toggle="tab">Đánh giá</a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade active in" id="details">
                    {!! $product->product_desc ?: '<p class="text-muted">Hiện chưa có mô tả sản phẩm.</p>' !!}
                </div>

                <div class="tab-pane fade" id="companyprofile">
                    {!! $product->product_content ?: '<p class="text-muted">Hiện chưa có nội dung chi tiết.</p>' !!}
                </div>

                <div class="tab-pane fade" id="reviews">
                    <div class="shopw-pdp-review-shell">
                        <div class="shopw-pdp-review-meta">
                            <strong>Đánh giá trung bình</strong>
                            <span>{{ number_format($rating, 0) }}/5 sao</span>
                        </div>

                        <div class="shopw-pdp-review-thread-shell">
                            @csrf
                            <input type="hidden" name="comment_product_id" class="comment_product_id"
                                value="{{ $product->product_id }}">
                            <div id="comment_show" class="shopw-pdp-review-thread"></div>
                        </div>

                        <div class="shopw-pdp-review-form">
                            <p class="shopw-pdp-block-title">Viết đánh giá của bạn</p>

                            <ul class="list-inline rating" title="Average Rating">
                                @for ($count = 1; $count <= 5; $count++)
                                    @php
                                        $color = $count <= $rating ? 'color:#ffcc00;' : 'color:#cbd5e1;';
                                    @endphp
                                    <li title="star_rating" id="{{ $product->product_id }}-{{ $count }}"
                                        data-index="{{ $count }}" data-product_id="{{ $product->product_id }}"
                                        data-rating="{{ $rating }}" class="rating"
                                        style="cursor:pointer; {{ $color }} font-size:30px;">&#9733;</li>
                                @endfor
                            </ul>

                            <div class="shopw-pdp-review-form-shell">
                                <div class="shopw-pdp-review-fields">
                                    <input type="text" class="comment_name" placeholder="Tên bình luận" />
                                    <textarea name="comment" class="comment_content" placeholder="Nội dung bình luận"></textarea>
                                </div>
                                <div id="notify_comment"></div>
                                <button type="button" class="btn btn-default send-comment">
                                    Gửi bình luận
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="recommended_items shopw-pdp-related">
            <div class="shopw-pdp-section-head">
                <span class="shopw-pdp-kicker">Khám phá thêm</span>
                <h2 class="title text-center">Sản phẩm liên quan</h2>
                <p>Tiếp tục khám phá các sản phẩm cùng nhóm đang được khách hàng quan tâm trong thời điểm hiện tại.</p>
            </div>
            <div class="row shopw-related-grid">
                @include('pages.category.partials.product_cards', [
                    'products' => $relate,
                    'emptyMessage' => 'Chưa có sản phẩm liên quan trong cùng danh mục.',
                    'emptyMessageClasses' => 'text-center text-muted',
                    'cardColumnClass' => 'col-xs-12 col-sm-6 col-md-3',
                    'showQuickView' => false,
                    'showCartToggle' => false,
                ])
            </div>
        </div>

        @include('partials.storefront.recommendation-section', [
            'title' => 'Gợi ý dành cho bạn',
            'description' => $recommendation_description ?? 'Ưu tiên từ lịch sử mua hàng, sau đó bổ sung từ lịch sử xem và mức độ quan tâm hiện tại.',
            'products' => $recommended_products ?? collect(),
            'cardColumnClass' => 'col-xs-12 col-sm-6 col-md-3',
            'emptyTitle' => 'Hệ thống chưa có thêm gợi ý phù hợp ở thời điểm này.',
        ])
    </div>
@endsection

