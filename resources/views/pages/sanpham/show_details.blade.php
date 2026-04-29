@extends('layout')

@section('body_class', 'g-product-detail')

@section('content')
    @php
        $storefrontLabel = \App\Support\StorefrontLabel::class;
        $product = $product_details->first();
        $productName = $storefrontLabel::neutralizeSource($product->product_name);
        $productBrandLabel = $storefrontLabel::neutralizeSource($product->brand_name ?: 'Đang cập nhật');
        $productDescriptionHtml = $storefrontLabel::neutralizeSourceHtml($product->product_desc);
        $productContentHtml = $storefrontLabel::neutralizeSourceHtml($product->product_content);
        $fallbackImage = \App\Support\AssetUrl::product();
        $primaryImage = \App\Support\AssetUrl::product($product->product_image);
        $categoryUrl = $cate_slug ? url('/danh-muc/' . $cate_slug) : null;
        $brandUrl = optional($product->brand)->brand_slug ? url('/thuong-hieu/' . optional($product->brand)->brand_slug) : null;
        $shortDescription = trim(strip_tags($productDescriptionHtml));
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
            $attributeImage = \App\Support\AssetUrl::resolve(
                !empty($attribute->image) ? 'uploads/attribute/' . $attribute->image : null,
                \App\Support\AssetUrl::PRODUCT_FALLBACK,
            );
            $attributeSlides[] = [
                'thumb' => $attributeImage,
                'src' => $attributeImage,
                'alt' => $storefrontLabel::neutralizeSource($attribute->name ?: $productName),
                'attribute_key' => $index,
            ];
        }

        $gallerySlides = [];
        foreach ($gallery as $media) {
            $galleryUrl = $media->url
                ?? \App\Support\AssetUrl::resolve(
                    $media->file_path ?? (!empty($media->gallery_image) ? 'uploads/gallery/' . $media->gallery_image : null),
                    null,
                );

            if (! $galleryUrl) {
                continue;
            }

            $gallerySlides[] = [
                'thumb' => $galleryUrl,
                'src' => $galleryUrl,
                'alt' => $storefrontLabel::neutralizeSource($media->gallery_name ?? $productName),
                'attribute_key' => null,
            ];
        }

        $imageSlides = array_merge($attributeSlides, $gallerySlides);
        if ($imageSlides === []) {
            $imageSlides[] = [
                'thumb' => $primaryImage,
                'src' => $primaryImage,
                'alt' => $productName,
                'attribute_key' => null,
            ];
        }
    @endphp

    <div class="shopw-pdp-page">
        <input type="hidden" id="product_viewed_id" value="{{ $product->product_id }}">
        <input type="hidden" id="viewed_productname{{ $product->product_id }}" value="{{ $productName }}">
        <input type="hidden" id="viewed_producturl{{ $product->product_id }}"
            value="{{ url('/chi-tiet/' . $product->product_slug) }}">
        <input type="hidden" id="viewed_productimage{{ $product->product_id }}" value="{{ $primaryImage }}">
        <input type="hidden" id="viewed_productprice{{ $product->product_id }}"
            value="{{ number_format($product->final_price, 0, ',', '.') }}đ">

        <nav aria-label="breadcrumb" class="shopw-pdp-breadcrumb">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span>/</span>
            @if ($categoryUrl)
                <a href="{{ $categoryUrl }}">{{ $productCategoryLabel }}</a>
                <span>/</span>
            @endif
            <strong>{{ $productName }}</strong>
        </nav>

        <article class="shopw-pdp-shell">
            {{-- Left: Gallery --}}
            <div class="shopw-pdp-gallery-card">
                <div class="shopw-pdp-gallery-head">
                    <span class="shopw-pdp-kicker">Hình ảnh sản phẩm</span>
                    <h2>{{ $productName }}</h2>
                </div>
                <ul id="imageGallery" class="shopw-pdp-gallery-list">
                    @foreach ($imageSlides as $slide)
                        <li
                            @if ($slide['attribute_key'] !== null) class="pro_attr_image_{{ $slide['attribute_key'] }}" @endif
                            data-thumb="{{ $slide['thumb'] }}" data-src="{{ $slide['src'] }}">
                            <img src="{{ $slide['src'] }}" alt="{{ $slide['alt'] }}">
                        </li>
                    @endforeach
                </ul>
                <p class="shopw-pdp-gallery-hint">Xem nhanh sản phẩm từ nhiều góc nhìn</p>
            </div>

            {{-- Right: Product Info --}}
            <div class="shopw-pdp-summary">
                <div class="shopw-pdp-header">
                    <span class="shopw-pdp-category-label">{{ $productCategoryLabel }}</span>
                    <h1 class="shopw-pdp-title">{{ $productName }}</h1>
                    
                    <div class="shopw-pdp-rating">
                        <div class="shopw-pdp-stars" aria-label="Đánh giá trung bình {{ $rating }} trên 5">
                            @for ($count = 1; $count <= 5; $count++)
                                <i class="fa {{ $count <= $rating ? 'fa-star' : 'fa-star-o' }}"></i>
                            @endfor
                        </div>
                        <span class="shopw-pdp-rating-text">{{ number_format($rating, 1) }} ({{ $product->product_views }} đánh giá)</span>
                    </div>

                    @if ($shortDescription !== '')
                        <p class="shopw-pdp-description">{{ $shortDescription }}</p>
                    @endif
                </div>

                <div class="shopw-pdp-price-panel" id="price_init">
                    <span class="shopw-pdp-price-label">Giá bán</span>
                    <div class="shopw-pdp-price-static">
                        @include('partials.storefront.product-price', [
                            'product' => $product,
                            'currency' => 'VND',
                            'showExplanation' => true,
                        ])
                    </div>
                </div>
                <div class="shopw-pdp-price-panel" id="price_after_click" aria-live="polite" style="display: none;"></div>

                <div class="shopw-pdp-badges">
                    <span class="shopw-pdp-sku">#EF4444</span>
                    <span class="shopw-pdp-stock {{ $stockClass }}">{{ $stockLabel }}</span>
                </div>

                <div class="shopw-pdp-purchase-section shopw-pdp-purchase"
                    data-default-stock="{{ max(0, (int) $product->product_quantity) }}"
                    data-default-label="Thêm vào giỏ">
                    @csrf
                    <input type="hidden" class="cart_product_id_attribute" value="0">
                    <input type="hidden" class="product_id" value="{{ $product->product_id }}">
                    <input type="hidden" class="cart_product_price" value="{{ $product->final_price }}">
                    <input type="hidden" class="cart_price_attribute" value="{{ $product->final_price }}">

                    <div class="shopw-pdp-quantity">
                        <label>Quantity</label>
                        <div class="shopw-pdp-qty-control">
                            <button type="button" class="shopw-qty-btn shopw-qty-minus" onclick="decreaseQty()">-</button>
                            <input id="product-detail-qty" name="qty" type="number" min="1"
                                max="{{ max(1, (int) $product->product_quantity) }}" class="cart_product_qty shopw-qty-input"
                                value="{{ $stockAvailable ? 1 : 0 }}" {{ $stockAvailable ? '' : 'disabled' }} />
                            <button type="button" class="shopw-qty-btn shopw-qty-plus" onclick="increaseQty()">+</button>
                        </div>
                    </div>

                    <div class="shopw-pdp-actions">
                        <button type="button" class="shopw-btn-add-cart add-to-cart" {{ $stockAvailable ? '' : 'disabled' }}>
                            <span class="shopw-pdp-purchase-btn-label">Thêm vào giỏ</span>
                        </button>
                        <button type="button" class="shopw-btn-buy-now" {{ $stockAvailable ? '' : 'disabled' }} onclick="buyNow()">
                            Mua ngay
                        </button>
                    </div>

                    <div class="shopw-pdp-quick-actions">
                        <button type="button" class="shopw-quick-action" onclick="addToWishlist({{ $product->product_id }})">
                            <i class="fa fa-heart-o"></i> Wishlist
                        </button>
                        <button type="button" class="shopw-quick-action" onclick="addToCompare({{ $product->product_id }})">
                            <i class="fa fa-random"></i> Compare it
                        </button>
                        <button type="button" class="shopw-quick-action" onclick="shareProduct()">
                            <i class="fa fa-share-alt"></i> Share info
                        </button>
                    </div>

                    <div id="product-detail-purchase-status" class="shopw-pdp-purchase-status" aria-live="polite"></div>
                </div>

                <section class="shopw-pdp-service-panel" aria-labelledby="shopw-pdp-service-title">
                    <h3 id="shopw-pdp-service-title" class="shopw-pdp-block-title">Hỗ trợ nhanh</h3>
                    <div class="shopw-pdp-service-grid">
                        @foreach ($detailSupportCards as $supportCard)
                            <article class="shopw-pdp-service-card">
                                <span class="shopw-pdp-service-icon">
                                    <i class="fa {{ $supportCard['icon'] }}"></i>
                                </span>
                                <div class="shopw-pdp-service-copy">
                                    <strong>{{ $supportCard['title'] }}</strong>
                                    <p>{{ $supportCard['text'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>

                <div class="shopw-pdp-features">
                    <div class="shopw-pdp-feature-card">
                        <i class="fa fa-truck"></i>
                        <div>
                            <strong>Giao nhanh</strong>
                            <p>Sản phẩm đang có sẵn để xử lý đơn</p>
                        </div>
                    </div>
                    <div class="shopw-pdp-feature-card">
                        <i class="fa fa-shield"></i>
                        <div>
                            <strong>Bảo hành</strong>
                            <p>Chính sách bảo hành rõ ràng theo từng loại hàng</p>
                        </div>
                    </div>
                    <div class="shopw-pdp-feature-card">
                        <i class="fa fa-comments"></i>
                        <div>
                            <strong>Tư vấn</strong>
                            <p>Đội ngũ tư vấn sẵn sàng hỗ trợ 24/7</p>
                        </div>
                    </div>
                    <div class="shopw-pdp-feature-card">
                        <i class="fa fa-refresh"></i>
                        <div>
                            <strong>Đổi trả</strong>
                            <p>Đổi trả dễ dàng trong vòng 7 ngày</p>
                        </div>
                    </div>
                </div>

                <div class="shopw-pdp-specs-table">
                    <div class="shopw-pdp-spec-row">
                        <span>Thương hiệu</span>
                        @if ($brandUrl)
                            <a href="{{ $brandUrl }}">{{ $productBrandLabel }}</a>
                        @else
                            <strong>{{ $productBrandLabel }}</strong>
                        @endif
                    </div>
                    <div class="shopw-pdp-spec-row">
                        <span>Dòng máy</span>
                        <strong>{{ $productCategoryLabel }}</strong>
                    </div>
                    <div class="shopw-pdp-spec-row">
                        <span>Tên máy</span>
                        <strong>{{ $productName }}</strong>
                    </div>
                    <div class="shopw-pdp-spec-row">
                        <span>Hotline</span>
                        <a href="{{ $supportPhoneHref }}">{{ $supportPhoneDisplay }}</a>
                    </div>
                </div>

                @if ($tagList->isNotEmpty())
                    <div class="shopw-pdp-tags">
                        <h3 class="shopw-pdp-block-title">Từ khóa sản phẩm</h3>
                        <div class="shopw-pdp-tag-list">
                            @foreach ($tagList as $tag)
                                <a href="{{ url('/tag/' . \Illuminate\Support\Str::slug($tag)) }}" class="shopw-pdp-tag">
                                    {{ $tag }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($product_attribute->isNotEmpty())
                    <div class="shopw-pdp-attributes">
                        <h3>Phiên bản khả dụng</h3>
                        <div class="shopw-pdp-attribute-grid">
                            @foreach ($product_attribute as $index => $attribute)
                                @php
                                    $attributeImage = !empty($attribute->image) ? asset('/uploads/attribute/' . $attribute->image) : $fallbackImage;
                                    $attributeLabel = $storefrontLabel::neutralizeSource($attribute->name);
                                @endphp
                                <div class="shopw-pdp-attribute-item pro_attr_image_click" data-key_image="{{ $index }}"
                                    data-id_attribute="{{ $attribute->id }}"
                                    data-name_attribute="{{ $attributeLabel }}"
                                    data-price_attribute="{{ $attribute->price }}"
                                    data-qty_attribute="{{ $attribute->quantity }}"
                                    data-image_attribute="{{ $attribute->image }}">
                                    <img src="{{ $attributeImage }}" alt="{{ $attributeLabel }}">
                                    <div>
                                        <strong>{{ $attributeLabel }}</strong>
                                        <span>{{ number_format($attribute->price, 0, ',', '.') }}đ</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </article>

        <div class="shopw-pdp-tabs">
            <div class="shopw-pdp-tabs-nav">
                <button class="shopw-tab-btn active" data-tab="mo-ta">Mô tả</button>
                <button class="shopw-tab-btn" data-tab="thong-so">Thông số kỹ thuật</button>
                <button class="shopw-tab-btn" data-tab="danh-gia">Đánh giá</button>
            </div>
            <div class="shopw-pdp-tabs-content">
                <div class="shopw-tab-pane active" id="mo-ta">
                    <h3>Mô tả sản phẩm</h3>
                    {!! $productDescriptionHtml ?: '<p class="text-muted">Hiện chưa có mô tả sản phẩm.</p>' !!}
                </div>

                <div class="shopw-tab-pane" id="thong-so">
                    <h3>Chi tiết sản phẩm</h3>
                    {!! $productContentHtml ?: '<p class="text-muted">Hiện chưa có nội dung chi tiết.</p>' !!}
                </div>

                <div class="shopw-tab-pane" id="danh-gia">
                    <h3>Đánh giá sản phẩm</h3>
                    <div class="shopw-pdp-review-summary">
                        <strong>Đánh giá trung bình: {{ number_format($rating, 1) }}/5 sao</strong>
                    </div>

                    <div class="shopw-pdp-review-list shopw-pdp-review-thread-shell">
                        @csrf
                        <input type="hidden" name="comment_product_id" class="comment_product_id"
                            value="{{ $product->product_id }}">
                        <div id="comment_show"></div>
                    </div>

                    <div class="shopw-pdp-review-form shopw-pdp-review-form-shell">
                        <h4>Viết đánh giá của bạn</h4>

                        <ul class="list-inline rating" title="Đánh giá trung bình">
                            @for ($count = 1; $count <= 5; $count++)
                                @php
                                    $color = $count <= $rating ? 'color:#ffcc00;' : 'color:#cbd5e1;';
                                @endphp
                                <li title="Chọn {{ $count }} sao" id="{{ $product->product_id }}-{{ $count }}"
                                    data-index="{{ $count }}" data-product_id="{{ $product->product_id }}"
                                    data-rating="{{ $rating }}" class="rating"
                                    style="cursor:pointer; {{ $color }} font-size:30px;">&#9733;</li>
                            @endfor
                        </ul>

                        <div class="shopw-pdp-review-fields">
                            <input type="text" class="comment_name" placeholder="Tên bình luận" />
                            <textarea name="comment" class="comment_content" placeholder="Nội dung bình luận"></textarea>
                        </div>
                        <div id="notify_comment"></div>
                        <button type="button" class="shopw-btn-submit-review send-comment">
                            Gửi bình luận
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <section class="shopw-pdp-related">
            <h2 class="shopw-section-title">Sản phẩm liên quan</h2>
            <div class="shopw-product-grid shopw-product-grid--4col">
                @foreach ($relate as $relatedProduct)
                    @php
                        $relatedName = $storefrontLabel::neutralizeSource($relatedProduct->product_name);
                        $relatedUrl = url('chi-tiet/' . $relatedProduct->product_slug);
                        $relatedImage = \App\Support\AssetUrl::product($relatedProduct->product_image);
                        $relatedPrice = (float) ($relatedProduct->final_price ?? $relatedProduct->product_price ?? 0);
                    @endphp
                    <article class="shopw-product-card">
                        <a href="{{ $relatedUrl }}" class="shopw-product-media">
                            <img src="{{ $relatedImage }}" alt="{{ $relatedName }}">
                        </a>
                        <div class="shopw-product-info">
                            <span class="shopw-product-code">#EF4444</span>
                            <h3 class="shopw-product-name">
                                <a href="{{ $relatedUrl }}">{{ $relatedName }}</a>
                            </h3>
                            <div class="shopw-product-price-section">
                                <strong class="shopw-product-price-current">{{ number_format($relatedPrice, 0, ',', '.') }}đ</strong>
                            </div>
                        </div>
                        <div class="shopw-product-actions">
                            <a href="{{ $relatedUrl }}" class="shopw-btn-detail">trắng</a>
                            <button type="button" class="shopw-btn-cart" onclick="Addtocart({{ $relatedProduct->product_id }});">Thêm giỏ</button>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="shopw-pdp-recommended">
            <h2 class="shopw-section-title">Gợi ý dành cho bạn</h2>
            <div class="shopw-product-grid shopw-product-grid--4col">
                @foreach ($recommended_products as $recommendedProduct)
                    @php
                        $recommendedName = $storefrontLabel::neutralizeSource($recommendedProduct->product_name);
                        $recommendedUrl = url('chi-tiet/' . $recommendedProduct->product_slug);
                        $recommendedImage = \App\Support\AssetUrl::product($recommendedProduct->product_image);
                        $recommendedPrice = (float) ($recommendedProduct->final_price ?? $recommendedProduct->product_price ?? 0);
                    @endphp
                    <article class="shopw-product-card">
                        <a href="{{ $recommendedUrl }}" class="shopw-product-media">
                            <img src="{{ $recommendedImage }}" alt="{{ $recommendedName }}">
                        </a>
                        <div class="shopw-product-info">
                            <span class="shopw-product-code">#EF4444</span>
                            <h3 class="shopw-product-name">
                                <a href="{{ $recommendedUrl }}">{{ $recommendedName }}</a>
                            </h3>
                            <div class="shopw-product-price-section">
                                <strong class="shopw-product-price-current">{{ number_format($recommendedPrice, 0, ',', '.') }}đ</strong>
                            </div>
                        </div>
                        <div class="shopw-product-actions">
                            <a href="{{ $recommendedUrl }}" class="shopw-btn-detail">trắng</a>
                            <button type="button" class="shopw-btn-cart" onclick="Addtocart({{ $recommendedProduct->product_id }});">Thêm giỏ</button>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>

    <script>
        function decreaseQty() {
            const input = document.getElementById('product-detail-qty');
            if (input && input.value > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }

        function increaseQty() {
            const input = document.getElementById('product-detail-qty');
            const max = parseInt(input.getAttribute('max'));
            if (input && input.value < max) {
                input.value = parseInt(input.value) + 1;
            }
        }

        function addToWishlist(productId) {
            if (typeof add_wishlist === 'function') {
                return add_wishlist(productId);
            }

            if (typeof add_wistlist === 'function') {
                return add_wistlist(productId);
            }

            if (typeof showStorefrontStatus === 'function') {
                showStorefrontStatus('Chưa thể thêm yêu thích',
                    'Chức năng yêu thích tạm thời chưa sẵn sàng.', 'warning');
            }

            return false;
        }

        function addToCompare(productId) {
            if (typeof add_compare === 'function') {
                return add_compare(productId);
            }

            if (typeof showStorefrontStatus === 'function') {
                showStorefrontStatus('Chưa thể so sánh',
                    'Chức năng so sánh tạm thời chưa sẵn sàng.', 'warning');
            }

            return false;
        }

        function shareProduct() {
            const shareData = {
                title: document.title,
                url: window.location.href
            };

            if (navigator.share) {
                navigator.share(shareData).catch(() => {});
                return;
            }

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(shareData.url).then(() => {
                    if (typeof showStorefrontStatus === 'function') {
                        showStorefrontStatus('Đã sao chép liên kết',
                            'Liên kết sản phẩm đã được sao chép.', 'success');
                    }
                }).catch(() => {});
            }
        }

        function buyNow() {
            // Check dependencies
            if (typeof $ === 'undefined' || typeof addProductToCart !== 'function') {
                console.error('buyNow: Missing dependencies (jQuery or addProductToCart)');
                alert('Chức năng mua ngay tạm thời không khả dụng. Vui lòng thử lại sau.');
                return;
            }

            // Get product data
            var id_product = $('.product_id').val();
            var id_attribute = $('.cart_product_id_attribute').val();
            var maxQuantity = parseInt($('.cart_product_qty').attr('max') || 0, 10);
            var cart_product_qty = parseInt($('.cart_product_qty').val() || 1, 10);
            var _token = $('input[name="_token"]').val();

            // Clear previous status
            if (typeof clearStorefrontInlineStatus === 'function') {
                clearStorefrontInlineStatus('#product-detail-purchase-status');
            }

            // Validate stock availability
            if (!maxQuantity) {
                if (typeof setStorefrontInlineStatus === 'function') {
                    setStorefrontInlineStatus('#product-detail-purchase-status',
                        'Sản phẩm hiện chưa sẵn sàng để mua.', 'warning');
                }
                if (typeof showStorefrontStatus === 'function') {
                    showStorefrontStatus('Tạm hết hàng',
                        'Sản phẩm hiện chưa sẵn sàng để mua.', 'warning');
                }
                return;
            }

            // Validate quantity
            if (cart_product_qty > maxQuantity) {
                $('.cart_product_qty').val(maxQuantity);
                var quantityMessage = 'Sản phẩm chỉ còn ' + maxQuantity + ' sản phẩm.';
                if (typeof setStorefrontInlineStatus === 'function') {
                    setStorefrontInlineStatus('#product-detail-purchase-status', quantityMessage, 'warning');
                }
                if (typeof showStorefrontStatus === 'function') {
                    showStorefrontStatus('Số lượng chưa hợp lệ', quantityMessage, 'warning');
                }
                return;
            }

            // Add to cart and redirect only on success
            addProductToCart({
                id_product: id_product,
                id_attribute: id_attribute,
                cart_product_qty: cart_product_qty,
                _token: _token
            }, {
                onSuccess: function() {
                    // Refresh cart UI
                    if (typeof refreshStorefrontCartUi === 'function') {
                        refreshStorefrontCartUi();
                    }
                    // Redirect to cart page only after successful add
                    window.location.href = '{{ url("/gio-hang") }}';
                },
                onError: function(xhr) {
                    // Show error without redirecting
                    var errorMessage = 'Không thể thêm sản phẩm vào giỏ hàng lúc này.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    if (typeof setStorefrontInlineStatus === 'function') {
                        setStorefrontInlineStatus('#product-detail-purchase-status', errorMessage, 'error');
                    }
                    if (typeof showStorefrontStatus === 'function') {
                        showStorefrontStatus('Lỗi', errorMessage, 'error');
                    }
                }
            });
        }

        // Tab switching
        document.querySelectorAll('.shopw-tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                
                // Remove active from all buttons and panes
                document.querySelectorAll('.shopw-tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.shopw-tab-pane').forEach(p => p.classList.remove('active'));
                
                // Add active to clicked button and corresponding pane
                this.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
    </script>
@endsection

