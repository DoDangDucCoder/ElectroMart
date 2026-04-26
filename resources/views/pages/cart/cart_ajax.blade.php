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

    <section id="cart_items" class="shopw-cart-page">
        <div class="shopw-cart-breadcrumbs">
            <ol class="breadcrumb">
                <li><a href="{{ url('/') }}">Trang chủ</a></li>
                <li class="active">Giỏ hàng</li>
            </ol>
        </div>

        @if (session('message'))
            <div class="alert alert-success">{!! session('message') !!}</div>
        @elseif(session('error'))
            <div class="alert alert-danger">{!! session('error') !!}</div>
        @endif

        <div class="shopw-cart-shell">
            <div class="shopw-cart-primary">
                <section class="shopw-cart-panel shopw-cart-overview-panel">
                    <div class="shopw-cart-section-head">
                        <div>
                            <h1>Giỏ hàng</h1>
                            <p>Kiểm tra lại sản phẩm, số lượng và mã giảm giá trước khi sang bước thanh toán.</p>
                        </div>
                        <span class="shopw-cart-section-meta">{{ number_format($cartCount) }} sản phẩm</span>
                    </div>

                    @if ($cartItems->isEmpty())
                        <div class="shopw-cart-empty-inline">
                            <p>
                                Giỏ hàng của bạn đang trống. Mời bạn mua thêm sản phẩm
                                <a href="{{ url('/san-pham-ban-chay') }}">tại đây</a>.
                            </p>
                        </div>
                    @else
                        <form action="{{ url('/update-cart') }}" method="POST" class="shopw-cart-items-form">
                            @csrf
                            <div class="shopw-cart-items">
                                @foreach ($cartItems as $item)
                                    @php
                                        $image = \App\Support\AssetUrl::product($item['product_image'] ?? null);
                                        $subtotal = (float) $item['product_price'] * (int) $item['product_qty'];
                                        $stock = max(1, (int) ($item['product_quantity'] ?? 1));
                                        $attribute = trim((string) ($item['product_attribute'] ?? ''));
                                    @endphp

                                    <article class="shopw-cart-line">
                                        <div class="shopw-cart-line-media">
                                            <img src="{{ $image }}" alt="{{ $item['product_name'] }}">
                                        </div>

                                        <div class="shopw-cart-line-body">
                                            <div class="shopw-cart-line-head">
                                                <div class="shopw-cart-line-copy">
                                                    <h2>{{ $item['product_name'] }}</h2>
                                                    <div class="shopw-cart-line-meta">
                                                        <span>Phân loại: {{ $attribute !== '' ? $attribute : 'Mặc định' }}</span>
                                                        <span>Tồn kho: {{ number_format((int) ($item['product_quantity'] ?? 0)) }}</span>
                                                    </div>
                                                </div>

                                                <a class="shopw-cart-remove-link"
                                                    href="{{ url('/del-product/' . $item['session_id']) }}">
                                                    <i class="fa fa-times"></i>
                                                    <span>Xóa</span>
                                                </a>
                                            </div>

                                            <div class="shopw-cart-line-foot">
                                                <div class="shopw-cart-line-price">
                                                    <span>Đơn giá</span>
                                                    <strong>{{ number_format($item['product_price'], 0, ',', '.') }}đ</strong>
                                                </div>

                                                <label class="shopw-cart-qty-field">
                                                    <span>Số lượng</span>
                                                    <input class="cart_quantity" type="number" min="1"
                                                        max="{{ $stock }}" name="cart_qty[{{ $item['session_id'] }}]"
                                                        value="{{ $item['product_qty'] }}">
                                                </label>

                                                <div class="shopw-cart-line-total">
                                                    <span>Thành tiền</span>
                                                    <strong>{{ number_format($subtotal, 0, ',', '.') }}đ</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            <div class="shopw-cart-actions-row">
                                <button type="submit" name="update_qty" class="shopw-cart-secondary-btn">
                                    <i class="fa fa-refresh"></i>Cập nhật giỏ hàng
                                </button>
                                <a class="shopw-cart-secondary-btn" href="{{ url('/del-all-product') }}">
                                    <i class="fa fa-trash"></i>Xóa tất cả
                                </a>
                                <a class="shopw-cart-secondary-btn" href="{{ url('/san-pham-ban-chay') }}">
                                    <i class="fa fa-arrow-left"></i>Tiếp tục mua hàng
                                </a>
                            </div>
                        </form>
                    @endif
                </section>

                <section class="shopw-cart-panel shopw-cart-viewed-panel">
                    <div class="shopw-cart-section-head">
                        <div>
                            <h2>Sản phẩm đã xem</h2>
                            <p>Quay lại nhanh những sản phẩm bạn vừa xem gần đây.</p>
                        </div>
                    </div>

                    <div id="shopw_cart_viewed_grid" class="shopw-cart-viewed-grid"></div>
                </section>
            </div>

            <aside class="shopw-cart-sidebar">
                <section class="shopw-cart-panel shopw-cart-summary-panel">
                    <div class="shopw-cart-section-head is-sidebar">
                        <div>
                            <h2>Thông tin đơn hàng</h2>
                            <p>Tổng tiền được tính trực tiếp từ giỏ hàng hiện tại.</p>
                        </div>
                    </div>

                    <div class="shopw-cart-summary-total">
                        <span>Tổng tiền</span>
                        <strong>{{ number_format($cartTotals['total'], 0, ',', '.') }}đ</strong>
                    </div>

                    <ul class="shopw-cart-summary-list">
                        <li>
                            <span>Tạm tính</span>
                            <strong>{{ number_format($cartTotals['subtotal'], 0, ',', '.') }}đ</strong>
                        </li>
                        <li>
                            <span>Giảm giá</span>
                            <strong class="{{ $cartTotals['discount'] > 0 ? 'is-discount' : '' }}">
                                -{{ number_format($cartTotals['discount'], 0, ',', '.') }}đ
                            </strong>
                        </li>
                        <li>
                            <span>Phí vận chuyển</span>
                            <strong>{{ number_format($cartTotals['shipping'], 0, ',', '.') }}đ</strong>
                        </li>
                    </ul>

                    @if ($hasCoupon)
                        <div class="shopw-cart-coupon-active">
                            <div>
                                <span>Mã đang áp dụng</span>
                                <strong>{{ $discountCode }}</strong>
                            </div>
                            <p>
                                @if ($discountType === 'percent')
                                    Giảm {{ rtrim(rtrim(number_format($discountValue, 2, '.', ''), '0'), '.') }}%
                                @elseif ($discountType === 'fixed')
                                    Giảm {{ number_format($discountValue, 0, ',', '.') }}đ
                                @elseif ($discountType === 'shipping')
                                    Hỗ trợ phí ship {{ number_format($discountValue, 0, ',', '.') }}đ
                                @else
                                    Mã giảm giá đã được áp dụng.
                                @endif
                            </p>
                            <a class="shopw-cart-inline-link" href="{{ url('/unset-coupon') }}">
                                <i class="fa fa-times-circle"></i>Gỡ mã khuyến mại
                            </a>
                        </div>
                    @endif

                    <form method="POST" action="{{ url('/check-coupon') }}" class="shopw-cart-coupon-form">
                        @csrf
                        <label for="coupon" class="shopw-cart-field-label">Mã khuyến mại</label>
                        <input type="text" class="form-control" id="coupon" name="coupon"
                            value="{{ old('coupon') }}" placeholder="Nhập mã khuyến mại (nếu có)">
                        <button type="submit" class="shopw-cart-secondary-btn shopw-cart-secondary-btn--full">
                            <i class="fa fa-ticket"></i>Áp dụng mã
                        </button>
                    </form>

                    @if ($cartItems->isEmpty())
                        <span class="shopw-cart-primary-link is-disabled">Chọn sản phẩm để thanh toán</span>
                    @else
                        <a class="shopw-cart-primary-link" href="{{ $checkoutUrl }}">
                            <i class="fa fa-credit-card"></i>{{ $isLoggedIn ? 'Thanh toán ngay' : 'Đăng nhập để thanh toán' }}
                        </a>
                    @endif

                    <a class="shopw-cart-continue-link" href="{{ url('/san-pham-ban-chay') }}">Tiếp tục mua hàng</a>
                </section>
            </aside>
        </div>
    </section>
@endsection
