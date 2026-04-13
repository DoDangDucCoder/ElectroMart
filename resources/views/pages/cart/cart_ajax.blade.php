@extends('layout')

@section('body_class', 'g-cart')

@section('content')
    @php
        $cartCount = $cartItems->sum('product_qty');
        $hasCoupon = !empty($coupon);
        $discountCode = data_get($coupon, 'code');
        $discountType = data_get($coupon, 'type');
        $discountValue = (float) data_get($coupon, 'value', 0);
        $supportConfig = config('services.storefront_support', []);
        $supportPhoneDisplay = $supportConfig['phone_display'] ?? '1900.0000';
        $supportPhoneHref = $supportConfig['phone_href'] ?? 'tel:19000000';
        $checkoutUrl = Session::get('customer_id')
            ? url('/checkout')
            : (url('/dang-nhap') . '?' . http_build_query([
                'redirect' => 'checkout',
                'redirect_to' => url('/checkout'),
            ]));
        $cartFlowSteps = [
            ['label' => 'Giỏ hàng', 'state' => 'is-active'],
            ['label' => 'Thanh toán', 'state' => ''],
            ['label' => 'Hoàn tất', 'state' => ''],
        ];
        $cartSupportItems = [
            [
                'icon' => 'fa-phone',
                'title' => config('storefront.labels.hotline_ordering', 'Hotline đặt hàng'),
                'text' => $supportPhoneDisplay,
                'url' => $supportPhoneHref,
            ],
            [
                'icon' => 'fa-comments',
                'title' => 'Chat tư vấn',
                'text' => config('storefront.copy.cart_chat_help', 'Mở khung chat để được hỗ trợ chọn cấu hình nhanh.'),
                'url' => url('/lien-he'),
            ],
            [
                'icon' => 'fa-history',
                'title' => 'Theo dõi đơn',
                'text' => config('storefront.copy.cart_order_tracking_help', 'Xem lịch sử mua sắm và trạng thái giao hàng hiện tại.'),
                'url' => url('/history'),
            ],
        ];
    @endphp

    <section id="cart_items" class="shopw-cart-page">
        <div class="shopw-cart-breadcrumbs">
            <ol class="breadcrumb">
                <li><a href="{{ url('/') }}">Trang chủ</a></li>
                <li class="active">Giỏ hàng của bạn</li>
            </ol>
        </div>

        @if (session('message'))
            <div class="alert alert-success">{!! session('message') !!}</div>
        @elseif(session('error'))
            <div class="alert alert-danger">{!! session('error') !!}</div>
        @endif

        <div class="shopw-cart-hero">
            <div class="shopw-cart-hero-copy">
                <span class="shopw-cart-kicker">Giỏ hàng</span>
                <h1>Giỏ hàng của bạn</h1>
                <p>Kiểm tra lại sản phẩm, cập nhật số lượng và áp dụng mã giảm giá trước khi chuyển sang bước thanh toán.</p>

                <div class="shopw-cart-flow" aria-label="Các bước mua hàng">
                    @foreach ($cartFlowSteps as $step)
                        <span class="shopw-cart-flow-step {{ $step['state'] }}">{{ $step['label'] }}</span>
                    @endforeach
                </div>
            </div>
            <div class="shopw-cart-hero-stats">
                <div class="shopw-cart-stat">
                    <span>Sản phẩm trong giỏ</span>
                    <strong>{{ number_format($cartCount) }}</strong>
                </div>
                <div class="shopw-cart-stat">
                    <span>Tạm tính</span>
                    <strong>{{ number_format($cartTotals['subtotal'], 0, ',', '.') }} VND</strong>
                </div>
                <div class="shopw-cart-stat">
                    <span>Tổng thanh toán</span>
                    <strong>{{ number_format($cartTotals['total'], 0, ',', '.') }} VND</strong>
                </div>
            </div>
        </div>

        @if ($cartItems->isEmpty())
            <div class="shopw-cart-empty">
                <div class="shopw-cart-empty-icon">
                    <i class="fa fa-shopping-basket"></i>
                </div>
                <h2>Giỏ hàng đang trống</h2>
                <p>Chưa có sản phẩm nào trong giỏ. Quay lại cửa hàng để tiếp tục mua sắm.</p>
                <a href="{{ url('/') }}" class="shopw-cart-primary-link">
                    <i class="fa fa-arrow-left"></i>Tiếp tục mua sắm
                </a>
            </div>
        @else
            <div class="shopw-cart-layout">
                <div class="shopw-cart-main">
                    <form action="{{ url('/update-cart') }}" method="POST" class="shopw-cart-items-form">
                        @csrf
                        <div class="shopw-cart-items">
                            @foreach ($cartItems as $item)
                                @php
                                    $image = $item['product_image']
                                        ? asset($item['product_image'])
                                        : asset('uploads/product/no-image.svg');
                                    $subtotal = (float) $item['product_price'] * (int) $item['product_qty'];
                                @endphp
                                <article class="shopw-cart-item">
                                    <div class="shopw-cart-item-media">
                                        <img src="{{ $image }}" alt="{{ $item['product_name'] }}">
                                    </div>

                                    <div class="shopw-cart-item-copy">
                                        <div class="shopw-cart-item-top">
                                            <div>
                                                <h3>{{ $item['product_name'] }}</h3>
                                                <div class="shopw-cart-item-meta">
                                                    <span>Thuộc tính: {{ $item['product_attribute'] ?? 'Mặc định' }}</span>
                                                    <span>Tồn kho: {{ number_format((int) ($item['product_quantity'] ?? 0)) }}</span>
                                                </div>
                                            </div>

                                            <a class="shopw-cart-item-remove"
                                                href="{{ url('/del-product/' . $item['session_id']) }}">
                                                <i class="fa fa-times"></i>
                                                <span>Xóa</span>
                                            </a>
                                        </div>

                                        <div class="shopw-cart-item-bottom">
                                            <div class="shopw-cart-price-stack">
                                                <span>Đơn giá</span>
                                                <strong>{{ number_format($item['product_price'], 0, ',', '.') }} VND</strong>
                                            </div>

                                            <label class="shopw-cart-qty-field">
                                                <span>Số lượng</span>
                                                <input class="cart_quantity" type="number" min="1"
                                                    max="{{ max(1, (int) ($item['product_quantity'] ?? 1)) }}"
                                                    name="cart_qty[{{ $item['session_id'] }}]"
                                                    value="{{ $item['product_qty'] }}">
                                            </label>

                                            <div class="shopw-cart-price-stack is-total">
                                                <span>Thành tiền</span>
                                                <strong>{{ number_format($subtotal, 0, ',', '.') }} VND</strong>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="shopw-cart-actions-row">
                            <button type="submit" name="update_qty" class="btn btn-default shopw-cart-secondary-btn">
                                <i class="fa fa-refresh"></i>Cập nhật giỏ hàng
                            </button>
                            <a class="shopw-cart-secondary-btn" href="{{ url('/del-all-product') }}">
                                <i class="fa fa-trash"></i>Xóa tất cả
                            </a>
                            <a class="shopw-cart-secondary-btn" href="{{ url('/') }}">
                                <i class="fa fa-arrow-left"></i>Tiếp tục mua sắm
                            </a>
                        </div>
                    </form>
                </div>

                <aside class="shopw-cart-sidebar">
                    <div class="shopw-cart-summary-card">
                        <div class="shopw-cart-card-head">
                            <h2>Tóm tắt đơn hàng</h2>
                            <p>Toàn bộ số tiền đang được tính từ session cart hiện tại.</p>
                        </div>

                        <ul class="shopw-cart-summary-list">
                            <li>
                                <span>Tạm tính</span>
                                <strong>{{ number_format($cartTotals['subtotal'], 0, ',', '.') }} VND</strong>
                            </li>
                            <li>
                                <span>Giảm giá</span>
                                <strong class="is-discount">-{{ number_format($cartTotals['discount'], 0, ',', '.') }} VND</strong>
                            </li>
                            <li>
                                <span>Phí vận chuyển</span>
                                <strong>{{ number_format($cartTotals['shipping'], 0, ',', '.') }} VND</strong>
                            </li>
                            <li class="is-grand-total">
                                <span>Tổng cộng</span>
                                <strong>{{ number_format($cartTotals['total'], 0, ',', '.') }} VND</strong>
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
                                        Giảm {{ number_format($discountValue, 0, ',', '.') }} VND
                                    @elseif ($discountType === 'shipping')
                                        Hỗ trợ phí ship {{ number_format($discountValue, 0, ',', '.') }} VND
                                    @else
                                        Mã giảm giá đã được áp dụng.
                                    @endif
                                </p>
                                <a class="shopw-cart-inline-link" href="{{ url('/unset-coupon') }}">
                                    <i class="fa fa-times-circle"></i>Gỡ mã khuyến mại
                                </a>
                            </div>
                        @endif

                        <a class="shopw-cart-primary-link" href="{{ $checkoutUrl }}">
                            <i class="fa fa-credit-card"></i>{{ Session::get('customer_id') ? 'Tiến hành thanh toán' : 'Đăng nhập để thanh toán' }}
                        </a>
                    </div>

                    <div class="shopw-cart-summary-card shopw-cart-support-card">
                        <div class="shopw-cart-card-head">
                            <h2>Hỗ trợ trước khi chốt đơn</h2>
                            <p>Giữ nhịp mua hàng liên tục với các lối tắt hỗ trợ thiết thực trước khi chốt đơn.</p>
                        </div>

                        <div class="shopw-cart-support-list">
                            @foreach ($cartSupportItems as $supportItem)
                                <a href="{{ $supportItem['url'] }}" class="shopw-cart-support-item">
                                    <span class="shopw-cart-support-icon">
                                        <i class="fa {{ $supportItem['icon'] }}"></i>
                                    </span>
                                    <span class="shopw-cart-support-copy">
                                        <strong>{{ $supportItem['title'] }}</strong>
                                        <small>{{ $supportItem['text'] }}</small>
                                    </span>
                                    <span class="shopw-cart-support-arrow">
                                        <i class="fa fa-angle-right"></i>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="shopw-cart-summary-card">
                        <div class="shopw-cart-card-head">
                            <h2>Mã giảm giá</h2>
                            <p>Áp dụng mã giảm giá trực tiếp trên giỏ hàng trước khi sang bước thanh toán.</p>
                        </div>

                        <form method="POST" action="{{ url('/check-coupon') }}" class="shopw-cart-coupon-form">
                            @csrf
                            <label for="coupon" class="shopw-cart-field-label">Nhập mã giảm giá</label>
                            <input type="text" class="form-control" id="coupon" name="coupon"
                                value="{{ old('coupon') }}" placeholder="Ví dụ: SALE10">
                            <button type="submit" class="btn btn-default shopw-cart-secondary-btn is-full">
                                <i class="fa fa-ticket"></i>Áp dụng mã
                            </button>
                        </form>
                    </div>
                </aside>
            </div>
        @endif
    </section>
@endsection

