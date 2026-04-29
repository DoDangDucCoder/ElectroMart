@extends('layout')

@section('body_class', 'g-cart')

@section('content')
    @php
        $cartCount = $cartItems->sum('product_qty');
        $hasCoupon = !empty($coupon);
        $discountCode = data_get($coupon, 'code');
        $discountType = data_get($coupon, 'type');
        $discountValue = (float) data_get($coupon, 'value', 0);
        $isLoggedIn = (bool) Session::get('customer_id');
        $checkoutUrl = $isLoggedIn
            ? url('/checkout')
            : (url('/dang-nhap') . '?' . http_build_query([
                'redirect' => 'checkout',
                'redirect_to' => url('/checkout'),
            ]));
    @endphp

    <div class="dd-cart-page">
        <nav class="dd-cart-breadcrumb">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span>/</span>
            <strong>Giỏ hàng</strong>
        </nav>

        @if (session('message'))
            <div class="dd-cart-alert dd-cart-alert--success">
                <i class="fa fa-check-circle"></i>
                {!! session('message') !!}
            </div>
        @elseif(session('error'))
            <div class="dd-cart-alert dd-cart-alert--error">
                <i class="fa fa-exclamation-circle"></i>
                {!! session('error') !!}
            </div>
        @endif

        <div class="dd-cart-container shopw-cart-shell">
            <main class="dd-cart-main">
                <div class="dd-cart-header">
                    @if (!$isLoggedIn && $cartItems->isEmpty())
                        <h1>GIỎ HÀNG (CHƯA ĐĂNG NHẬP)</h1>
                    @elseif ($isLoggedIn && $cartItems->isEmpty())
                        <h1>GIỎ HÀNG (TRỐNG)</h1>
                    @else
                        <h1>GIỎ HÀNG ({{ $cartCount }} SẢN PHẨM)</h1>
                    @endif
                </div>

                @if ($cartItems->isEmpty())
                    <div class="dd-cart-empty">
                        <div class="dd-cart-empty-icon">
                            <i class="fa fa-shopping-cart"></i>
                        </div>
                        @if (!$isLoggedIn)
                            <p class="dd-cart-empty-message">Bạng chưa đăng nhập</p>
                            <p class="dd-cart-empty-hint">Bạng chưa đăng nhập vào hệ thống để tiến hành mua hàng, vui lòng đăng nhập để tiếp tục.</p>
                            <div class="dd-cart-empty-actions">
                                <a href="{{ url('/dang-nhap') }}" class="dd-btn-primary">Đăng nhập</a>
                                <a href="{{ url('/san-pham') }}" class="dd-btn-secondary">Tiếp tục mua hàng</a>
                            </div>
                        @else
                            <p class="dd-cart-empty-message">Giỏ hàng trống</p>
                            <p class="dd-cart-empty-hint">Giỏ hàng của bạn đang trống. Hãy khám phá và thêm sản phẩm yêu thích vào giỏ hàng.</p>
                            <div class="dd-cart-empty-actions">
                                <a href="{{ url('/san-pham') }}" class="dd-btn-primary">Tiếp tục mua hàng</a>
                            </div>
                        @endif
                    </div>
                @else
                    <form action="{{ url('/update-cart') }}" method="POST" class="dd-cart-form">
                        @csrf
                        <div class="dd-cart-items">
                            @foreach ($cartItems as $item)
                                @php
                                    $image = \App\Support\AssetUrl::product($item['product_image'] ?? null);
                                    $subtotal = (float) $item['product_price'] * (int) $item['product_qty'];
                                    $stock = max(1, (int) ($item['product_quantity'] ?? 1));
                                    $attribute = trim((string) ($item['product_attribute'] ?? ''));
                                    $originalPrice = (float) ($item['product_original_price'] ?? $item['product_price']);
                                    $hasDiscount = $originalPrice > $item['product_price'];
                                @endphp

                                <article class="dd-cart-item">
                                    <div class="dd-cart-item-media">
                                        <img src="{{ $image }}" alt="{{ $item['product_name'] }}">
                                    </div>

                                    <div class="dd-cart-item-info">
                                        <h3 class="dd-cart-item-name">{{ $item['product_name'] }}</h3>
                                        <p class="dd-cart-item-sku">SKU: #{{ $item['product_id'] }}</p>
                                        @if ($attribute !== '')
                                            <p class="dd-cart-item-variant">Phân loại: {{ $attribute }}</p>
                                        @endif
                                    </div>

                                    <div class="dd-cart-item-price">
                                        @if ($hasDiscount)
                                            <span class="dd-cart-item-price-original">{{ number_format($originalPrice, 0, ',', '.') }}đ</span>
                                        @endif
                                        <strong class="dd-cart-item-price-current">{{ number_format($item['product_price'], 0, ',', '.') }}đ</strong>
                                    </div>

                                    <div class="dd-cart-item-quantity">
                                        <button type="button" class="dd-qty-btn" onclick="decreaseCartQty('{{ $item['session_id'] }}')">-</button>
                                        <input type="number" min="1" max="{{ $stock }}" 
                                            name="cart_qty[{{ $item['session_id'] }}]" 
                                            id="qty-{{ $item['session_id'] }}"
                                            value="{{ $item['product_qty'] }}" 
                                            class="dd-qty-input">
                                        <button type="button" class="dd-qty-btn" onclick="increaseCartQty('{{ $item['session_id'] }}', {{ $stock }})">+</button>
                                    </div>

                                    <div class="dd-cart-item-total">
                                        <strong>{{ number_format($subtotal, 0, ',', '.') }}đ</strong>
                                    </div>

                                    <button type="button" class="dd-cart-item-remove" onclick="removeCartItem('{{ $item['session_id'] }}')">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </article>
                            @endforeach
                        </div>

                        <div class="dd-cart-actions">
                            <button type="submit" class="dd-btn-secondary">
                                <i class="fa fa-refresh"></i> Cập nhật giỏ hàng
                            </button>
                            <a href="{{ url('/san-pham') }}" class="dd-btn-link">Tiếp tục mua hàng</a>
                        </div>
                    </form>
                @endif

                <section class="dd-cart-viewed">
                    <h2>Sản phẩm đã xem</h2>
                    <div id="shopw_cart_viewed_grid" class="dd-cart-viewed-grid shopw-cart-viewed-grid"></div>
                </section>
            </main>

            <aside class="dd-cart-sidebar">
                <div class="dd-cart-summary">
                    <h2>Thông tin đơn hàng</h2>

                    <div class="dd-cart-summary-rows">
                        <div class="dd-cart-summary-row">
                            <span>Tổng tiền</span>
                            <strong>{{ number_format($cartTotals['subtotal'], 0, ',', '.') }}đ</strong>
                        </div>
                        <div class="dd-cart-summary-row">
                            <span>Giảm giá</span>
                            <strong class="{{ $cartTotals['discount'] > 0 ? 'is-discount' : '' }}">
                                -{{ number_format($cartTotals['discount'], 0, ',', '.') }}đ
                            </strong>
                        </div>
                        <div class="dd-cart-summary-row">
                            <span>Giảm giá</span>
                            <strong>0đ</strong>
                        </div>
                        <div class="dd-cart-summary-row">
                            <span>Phí vận chuyển</span>
                            <strong>{{ number_format($cartTotals['shipping'], 0, ',', '.') }}đ</strong>
                        </div>
                    </div>

                    @if ($cartItems->isEmpty())
                        <button type="button" class="dd-btn-checkout" disabled>Thanh toán ngay</button>
                    @else
                        <a href="{{ $checkoutUrl }}" class="dd-btn-checkout">
                            {{ $isLoggedIn ? 'Thanh toán ngay' : 'Đăng nhập để thanh toán' }}
                        </a>
                    @endif

                    <a href="{{ url('/san-pham') }}" class="dd-link-continue">Tiếp tục mua hàng</a>

                    @if (!$cartItems->isEmpty())
                        <div class="dd-cart-coupon">
                            <h3>Coupon/Mã giảm giá</h3>
                            <form method="POST" action="{{ url('/check-coupon') }}" class="dd-cart-coupon-form">
                                @csrf
                                <input type="text" name="coupon" value="{{ old('coupon') }}" 
                                    placeholder="Coupon/Mã giảm giá" class="dd-coupon-input">
                                <button type="submit" class="dd-btn-apply-coupon">Áp dụng mã</button>
                            </form>
                            @if ($hasCoupon)
                                <div class="dd-cart-coupon-active">
                                    <span>Mã đang áp dụng: <strong>{{ $discountCode }}</strong></span>
                                    <a href="{{ url('/unset-coupon') }}" class="dd-link-remove-coupon">
                                        <i class="fa fa-times"></i> Gỡ mã
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </div>

    <script>
        function decreaseCartQty(sessionId) {
            const input = document.getElementById('qty-' + sessionId);
            if (input && input.value > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }

        function increaseCartQty(sessionId, max) {
            const input = document.getElementById('qty-' + sessionId);
            if (input && parseInt(input.value) < max) {
                input.value = parseInt(input.value) + 1;
            }
        }

        function removeCartItem(sessionId) {
            if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                window.location.href = '{{ url("/del-product") }}/' + sessionId;
            }
        }
    </script>
@endsection
