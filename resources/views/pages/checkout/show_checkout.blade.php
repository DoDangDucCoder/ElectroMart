@extends('layout')

@section('body_class', 'g-checkout')

@section('content')
    @php
        $cartCount = $cartItems->sum('product_qty');
        $hasCoupon = !empty($coupon);
        $discountCode = data_get($coupon, 'code');
        $customerLoggedIn = (bool) Session::get('customer_id');
        $checkoutTotal = number_format($cartTotals['total'], 0, ',', '.') . ' VND';
        $supportConfig = config('services.storefront_support', []);
        $supportPhoneDisplay = $supportConfig['phone_display'] ?? '1900.0000';
        $supportPhoneHref = $supportConfig['phone_href'] ?? 'tel:19000000';
        $checkoutFlowSteps = [
            ['label' => 'Giỏ hàng', 'state' => 'is-complete'],
            ['label' => 'Thanh toán', 'state' => 'is-active'],
            ['label' => 'Hoàn tất', 'state' => ''],
        ];
        $checkoutSupportItems = [
            [
                'icon' => 'fa-map-marker',
                'title' => 'Địa chỉ giao hàng',
                'text' => config('storefront.copy.checkout_delivery_help', 'Điền chính xác khu vực giao hàng để hệ thống tính phí ship ngay trên form.'),
            ],
            [
                'icon' => 'fa-shield',
                'title' => 'Thông tin an toàn',
                'text' => config('storefront.copy.checkout_security_help', 'Dữ liệu đơn hàng được dùng trực tiếp để tạo đơn và đối soát thanh toán.'),
            ],
            [
                'icon' => 'fa-phone',
                'title' => 'Cần xác nhận nhanh?',
                'text' => 'Liên hệ ' . $supportPhoneDisplay . ' nếu cần hỗ trợ chốt đơn ngay.',
            ],
        ];
        $checkoutQuickLinks = [
            [
                'icon' => 'fa-phone',
                'label' => config('storefront.labels.utility_hotline', 'Hotline'),
                'value' => $supportPhoneDisplay,
                'url' => $supportPhoneHref,
            ],
            [
                'icon' => 'fa-comments',
                'label' => config('storefront.labels.contact_fast', 'Liên hệ nhanh'),
                'value' => config('storefront.copy.checkout_contact_fast_value', 'Mở trang liên hệ để được hỗ trợ nhanh'),
                'url' => url('/lien-he'),
            ],
            [
                'icon' => 'fa-history',
                'label' => config('storefront.labels.view_previous_orders', 'Xem đơn cũ'),
                'value' => config('storefront.copy.checkout_previous_orders_value', 'Kiểm tra lịch sử mua sắm'),
                'url' => url('/history'),
            ],
        ];
    @endphp

    <section id="cart_items" class="shopw-checkout-page">
        <div class="shopw-checkout-breadcrumbs">
            <ol class="breadcrumb">
                <li><a href="{{ url('/') }}">Trang chủ</a></li>
                <li><a href="{{ url('/gio-hang') }}">Giỏ hàng</a></li>
                <li class="active">Thanh toán giỏ hàng</li>
            </ol>
        </div>

        @if (session('message'))
            <div class="alert alert-success">{!! session('message') !!}</div>
        @elseif(session('error'))
            <div class="alert alert-danger">{!! session('error') !!}</div>
        @endif

        <div class="shopw-checkout-hero">
            <div class="shopw-checkout-hero-copy">
                <span class="shopw-checkout-kicker">Thanh toán</span>
                <h1>Hoàn tất thông tin giao hàng</h1>
                <p>Điền địa chỉ nhận hàng, chọn phương thức thanh toán và kiểm tra lại đơn trước khi xác nhận.</p>

                <div class="shopw-checkout-flow" aria-label="Tiến trình mua hàng">
                    @foreach ($checkoutFlowSteps as $step)
                        <span class="shopw-checkout-flow-step {{ $step['state'] }}">{{ $step['label'] }}</span>
                    @endforeach
                </div>
            </div>
            <div class="shopw-checkout-hero-stats">
                <div class="shopw-checkout-stat">
                    <span>Sản phẩm</span>
                    <strong>{{ number_format($cartCount) }}</strong>
                </div>
                <div class="shopw-checkout-stat">
                    <span>Phí vận chuyển</span>
                    <strong>{{ number_format($cartTotals['shipping'], 0, ',', '.') }} VND</strong>
                </div>
                <div class="shopw-checkout-stat">
                    <span>Tổng thanh toán</span>
                    <strong>{{ $checkoutTotal }}</strong>
                </div>
            </div>
        </div>

        <div class="shopw-checkout-layout">
            <div class="shopw-checkout-main">
                <div class="shopw-checkout-card">
                    <div class="shopw-checkout-card-head">
                        <h2>Thông tin giao hàng</h2>
                        <p>Điền đầy đủ thông tin để hệ thống tính phí vận chuyển và tạo đơn hàng chính xác.</p>
                    </div>

                    <div class="shopw-checkout-assurance-grid">
                        @foreach ($checkoutSupportItems as $supportItem)
                            <div class="shopw-checkout-assurance-card">
                                <span class="shopw-checkout-assurance-icon">
                                    <i class="fa {{ $supportItem['icon'] }}"></i>
                                </span>
                                <div class="shopw-checkout-assurance-copy">
                                    <strong>{{ $supportItem['title'] }}</strong>
                                    <p>{{ $supportItem['text'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @unless ($customerLoggedIn)
                        <div class="shopw-checkout-warning">
                            <i class="fa fa-user-circle-o"></i>
                            <div>
                                <strong>Bạn cần đăng nhập trước khi đặt hàng.</strong>
                                <p>Hệ thống hiện yêu cầu đăng nhập tài khoản trước khi xác nhận đơn hàng.</p>
                            </div>
                            <a href="{{ url('/dang-nhap') }}" class="shopw-checkout-inline-link">Đăng nhập</a>
                        </div>
                    @endunless

                    <form id="checkout-form" method="POST" class="shopw-checkout-form">
                        @csrf

                        <div class="shopw-checkout-field-grid">
                            <label class="shopw-checkout-field">
                                <span>Email</span>
                                <input type="email" name="shipping_email"
                                    class="shipping_email form-control"
                                    value="{{ old('shipping_email', $currentCustomer?->email) }}"
                                    placeholder="you@example.com" required>
                            </label>

                            <label class="shopw-checkout-field">
                                <span>Họ và tên</span>
                                <input type="text" name="shipping_name"
                                    class="shipping_name form-control"
                                    value="{{ old('shipping_name', $currentCustomer?->name) }}"
                                    placeholder="Nguyen Van A" required>
                            </label>
                        </div>

                        <div class="shopw-checkout-field-grid">
                            <label class="shopw-checkout-field">
                                <span>Số điện thoại</span>
                                <input type="text" name="shipping_phone"
                                    class="shipping_phone form-control"
                                    value="{{ old('shipping_phone', $currentCustomer?->phone) }}"
                                    placeholder="09xxxxxxxx" required>
                            </label>

                            <label class="shopw-checkout-field">
                                <span>Phương thức thanh toán</span>
                                <select name="shipping_method" class="form-control payment_select">
                                    <option value="cod">Thanh toán khi nhận hàng</option>
                                    <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                                </select>
                            </label>
                        </div>

                        <label class="shopw-checkout-field">
                            <span>Địa chỉ nhận hàng</span>
                            <input type="text" name="shipping_address"
                                class="shipping_address form-control"
                                value="{{ old('shipping_address') }}"
                                placeholder="Số nhà, đường, khu vực nhận hàng" required>
                        </label>

                        <label class="shopw-checkout-field">
                            <span>Ghi chú</span>
                            <textarea name="shipping_notes" class="shipping_notes form-control" rows="4"
                                placeholder="Ghi chú thêm cho người giao hàng">{{ old('shipping_notes') }}</textarea>
                        </label>

                        <div class="shopw-checkout-address-block">
                            <div class="shopw-checkout-address-head">
                                <div>
                                    <h3>Khu vực giao hàng</h3>
                                    <p>Chọn lần lượt tỉnh/thành, quận/huyện, phường/xã để tính phí ship.</p>
                                </div>
                                <button type="button" class="btn btn-default calculate_delivery">
                                    <i class="fa fa-truck"></i>Tính phí vận chuyển
                                </button>
                            </div>

                            <div class="shopw-checkout-field-grid shopw-checkout-location-grid">
                                <label class="shopw-checkout-field">
                                    <span>Tỉnh / thành</span>
                                    <select name="city_id" class="form-control choose city" id="city">
                                        <option value="">---Chọn tỉnh / thành---</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </label>

                                <label class="shopw-checkout-field">
                                    <span>Quận / huyện</span>
                                    <select name="province_id" class="form-control choose province" id="province">
                                        <option value="">---Chọn quận / huyện---</option>
                                    </select>
                                </label>

                                <label class="shopw-checkout-field">
                                    <span>Phường / xã</span>
                                    <select name="ward_id" class="form-control wards" id="wards">
                                        <option value="">---Chọn phường / xã---</option>
                                    </select>
                                </label>
                            </div>
                        </div>

                        <input type="hidden" name="order_fee" class="order_fee" value="{{ $shipping_fee }}">
                        <input type="hidden" name="order_coupon" class="order_coupon"
                            value="{{ $coupon['code'] ?? 'no' }}">
                        <input type="hidden" name="total_after" class="total_after"
                            value="{{ $cartTotals['total'] }}">

                        <div class="shopw-checkout-submit-row">
                            <button type="button"
                                class="btn btn-primary send_order" {{ $customerLoggedIn ? '' : 'disabled' }}>
                                <i class="fa fa-check-circle"></i>Xác nhận đơn hàng
                            </button>
                            <a href="{{ url('/gio-hang') }}" class="shopw-checkout-inline-link">
                                <i class="fa fa-arrow-left"></i>Quay lại giỏ hàng
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <aside class="shopw-checkout-sidebar">
                <div class="shopw-checkout-card">
                    <div class="shopw-checkout-card-head">
                        <h2>Tóm tắt đơn hàng</h2>
                        <p>Tổng tiền được cập nhật theo giỏ hàng, mã giảm giá và phí vận chuyển hiện tại.</p>
                    </div>

                    <ul class="shopw-checkout-summary-list">
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
                            <strong>{{ $checkoutTotal }}</strong>
                        </li>
                    </ul>

                    @if ($hasCoupon)
                        <div class="shopw-checkout-coupon-chip">
                            <span>Coupon đang áp dụng</span>
                            <strong>{{ $discountCode }}</strong>
                            <a href="{{ url('/unset-coupon') }}" class="shopw-checkout-inline-link">
                                <i class="fa fa-times-circle"></i>Gỡ mã
                            </a>
                        </div>
                    @endif
                </div>

                <div class="shopw-checkout-card">
                    <div class="shopw-checkout-card-head">
                        <h2>Sản phẩm trong đơn</h2>
                        <p>Danh sách sản phẩm được chốt ngay trước khi bạn xác nhận đặt hàng.</p>
                    </div>

                    <div class="shopw-checkout-items">
                        @foreach ($cartItems as $item)
                            @php
                                $image = $item['product_image']
                                    ? asset($item['product_image'])
                                    : asset('uploads/product/no-image.svg');
                                $subtotal = (float) $item['product_price'] * (int) $item['product_qty'];
                            @endphp
                            <article class="shopw-checkout-item">
                                <div class="shopw-checkout-item-media">
                                    <img src="{{ $image }}" alt="{{ $item['product_name'] }}">
                                </div>
                                <div class="shopw-checkout-item-copy">
                                    <h3>{{ $item['product_name'] }}</h3>
                                    <div class="shopw-checkout-item-meta">
                                        <span>SL: {{ $item['product_qty'] }}</span>
                                        @if (! empty($item['product_attribute']))
                                            <span>{{ $item['product_attribute'] }}</span>
                                        @endif
                                    </div>
                                    <div class="shopw-checkout-item-pricing">
                                        <strong>{{ number_format($subtotal, 0, ',', '.') }} VND</strong>
                                        <small>{{ number_format($item['product_price'], 0, ',', '.') }} VND / sản phẩm</small>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="shopw-checkout-cart-actions">
                        <a href="{{ url('/gio-hang') }}" class="shopw-checkout-secondary-btn">Quay lại giỏ hàng</a>
                        <a href="{{ url('/del-all-product') }}" class="shopw-checkout-secondary-btn">Xóa toàn bộ giỏ</a>
                    </div>
                </div>

                <div class="shopw-checkout-card shopw-checkout-support-card">
                    <div class="shopw-checkout-card-head">
                        <h2>Hỗ trợ chốt đơn</h2>
                        <p>Dùng các lối tắt này nếu bạn cần xác nhận nhanh trước khi đặt hàng.</p>
                    </div>

                    <div class="shopw-checkout-support-list">
                        @foreach ($checkoutQuickLinks as $quickLink)
                            <a href="{{ $quickLink['url'] }}" class="shopw-checkout-support-item">
                                <span class="shopw-checkout-support-icon">
                                    <i class="fa {{ $quickLink['icon'] }}"></i>
                                </span>
                                <span class="shopw-checkout-support-copy">
                                    <strong>{{ $quickLink['label'] }}</strong>
                                    <small>{{ $quickLink['value'] }}</small>
                                </span>
                                <span class="shopw-checkout-support-arrow">
                                    <i class="fa fa-angle-right"></i>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection

