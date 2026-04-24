@php
    $emptyMessage = $emptyMessage ?? 'Hiện chưa có sản phẩm nào.';
    $emptyMessageClasses = $emptyMessageClasses ?? 'text-center text-muted';
    $emptyStateIcon = $emptyStateIcon ?? 'fa fa-cubes';
    $emptyStateActionText = $emptyStateActionText ?? 'Về trang chủ';
    $emptyStateActionUrl = $emptyStateActionUrl ?? url('/');
    $emptyStateClass = $emptyStateClass ?? 'shopw-product-empty';
    $cardColumnClass = $cardColumnClass ?? 'col-sm-3';
    $showQuickView = $showQuickView ?? false;
    $showCartToggle = $showCartToggle ?? false;
    $showWishlist = $showWishlist ?? true;
    $showCompare = $showCompare ?? true;
@endphp

@forelse ($products as $product)
    @php
        $productId = $product->product_id;
        $productName = $product->product_name;
        $productUrl = url('chi-tiet/' . $product->product_slug);
        $imagePath = $product->product_image ? asset($product->product_image) : asset('uploads/product/no-image.svg');
        $priceFormatted = number_format($product->final_price ?? $product->product_price, 0, ',', '.') . ' VND';
        $productBrand = optional($product->brand)->brand_name ?? ($product->brand_name ?? null);
    @endphp
    <div class="{{ $cardColumnClass }}">
        <div class="product-image-wrapper shopw-product-card">
            <div class="single-products">
                <div class="productinfo text-center shopw-product-shell">
                    <form class="shopw-product-form">
                        @csrf
                        <input type="hidden" value="{{ $productId }}" class="cart_product_id_{{ $productId }}">
                        <input type="hidden" id="wishlist_productname{{ $productId }}" value="{{ $productName }}"
                            class="cart_product_name_{{ $productId }}">
                        <input type="hidden" value="{{ $product->product_image }}"
                            class="cart_product_image_{{ $productId }}">
                        <input type="hidden" value="{{ $product->product_quantity }}"
                            class="cart_product_quantity_{{ $productId }}">
                        <input type="hidden" id="wishlist_productprice{{ $productId }}" value="{{ $priceFormatted }}">
                        <input type="hidden" value="{{ $product->product_price }}"
                            class="cart_product_price_{{ $productId }}">
                        <input type="hidden" value="1" class="cart_product_qty_{{ $productId }}">
                        <a id="wishlist_producturl{{ $productId }}" href="{{ $productUrl }}" class="hidden"
                            aria-hidden="true"></a>
                        <a href="{{ $productUrl }}" class="shopw-product-link">
                            <span class="shopw-product-media">
                                <img id="wishlist_productimage{{ $productId }}" src="{{ $imagePath }}"
                                    alt="{{ $productName }}" />
                            </span>
                            <span class="shopw-product-copy">
                                @if ($productBrand)
                                    <span class="shopw-product-brand">{{ $productBrand }}</span>
                                @endif
                                <span class="tieude-product_name shopw-product-title">{{ $productName }}</span>
                                <span class="shopw-product-price">
                                    @include('partials.storefront.product-price', [
                                        'product' => $product,
                                        'currency' => 'VND',
                                        'showExplanation' => true,
                                        'compact' => true,
                                    ])
                                </span>
                            </span>
                        </a>
                        <div class="shopw-card-actions">
                            @if ($showCartToggle)
                                <button type="button"
                                    class="btn btn-default shopw-card-btn shopw-card-btn-primary home_cart_{{ $productId }}"
                                    id="{{ $productId }}" onclick="Addtocart(this.id);">
                                    <i class="fa fa-shopping-cart"></i>Thêm giỏ hàng
                                </button>
                                <button type="button" class="btn btn-danger shopw-card-btn rm_home_cart_{{ $productId }}"
                                    id="{{ $productId }}" onclick="Deletecart(this.id);" style="display:none">
                                    <i class="fa fa-times"></i>Bỏ khỏi giỏ
                                </button>
                            @else
                                <button type="button" class="btn btn-default shopw-card-btn shopw-card-btn-primary"
                                    data-id_product="{{ $productId }}" onclick="Addtocart({{ $productId }});">
                                    <i class="fa fa-shopping-cart"></i>Thêm giỏ hàng
                                </button>
                            @endif

                            @if ($showQuickView)
                                <button type="button" class="btn btn-default shopw-card-btn shopw-card-btn-secondary"
                                    data-toggle="modal" data-target="#xemnhanh" onclick="XemNhanh({{ $productId }});">
                                    <i class="fa fa-eye"></i>Xem nhanh
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            @if ($showWishlist || $showCompare)
                <div class="choose shopw-card-meta">
                    <ul class="shopw-card-meta-list">
                        @if ($showWishlist)
                            <li class="shopw-card-meta-item">
                                <button type="button" class="shopw-card-meta-link shopw-card-meta-link--wishlist"
                                    onclick="add_wistlist({{ $productId }})">
                                    <i class="fa fa-heart-o" aria-hidden="true"></i>
                                    <span>Yêu thích</span>
                                </button>
                            </li>
                        @endif
                        @if ($showCompare)
                            <li class="shopw-card-meta-item">
                                <button type="button" class="shopw-card-meta-link shopw-card-meta-link--compare"
                                    onclick="add_compare({{ $productId }})">
                                    <i class="fa fa-exchange" aria-hidden="true"></i>
                                    <span>So sánh</span>
                                </button>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>
    </div>
@empty
    <div class="col-sm-12">
        @include('partials.storefront.empty-state', [
            'title' => $emptyMessage,
            'message' => 'Hãy thử chuyển danh mục, thay đổi bộ lọc hoặc quay lại trang chủ để khám phá thêm sản phẩm.',
            'icon' => $emptyStateIcon,
            'compact' => true,
            'class' => trim($emptyStateClass . ' ' . $emptyMessageClasses),
            'actionText' => $emptyStateActionText,
            'actionUrl' => $emptyStateActionUrl,
        ])
    </div>
@endforelse

