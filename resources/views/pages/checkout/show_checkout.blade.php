@extends('layout')

@section('body_class', 'g-checkout-redesign minimal-layout')

@section('content')
    @php
        $cartCount = $cartItems->sum('product_qty');
        $hasCoupon = !empty($coupon);
        $discountCode = data_get($coupon, 'code');
        $customerLoggedIn = (bool) $currentCustomer;
        $checkoutTotal = number_format($cartTotals['total'], 0, ',', '.') . ' VND';
        $checkoutBaseTotal = max(0, (float) $cartTotals['total'] - (float) $cartTotals['shipping']);
        $checkoutCustomerDisplayName = $currentCustomer?->name ?: ($currentCustomer?->email ?: 'Khách hàng');
    @endphp

    <style>
        .g-checkout-redesign {
            background-color: #f5f5f5;
            color: #333;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        .shopw-checkout-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            gap: 40px;
        }

        .shopw-checkout-left {
            flex: 1;
            padding-right: 20px;
        }

        .shopw-checkout-right {
            width: 420px;
            background-color: #fafafa;
            border-left: 1px solid #e1e1e1;
            padding: 20px;
            min-height: 100vh;
        }

        .checkout-logo {
            margin-bottom: 25px;
        }

        .checkout-logo img {
            max-width: 220px;
        }

        .checkout-breadcrumb {
            margin-bottom: 20px;
            font-size: 13px;
            color: #999;
        }

        .checkout-breadcrumb a {
            color: #338dbc;
            text-decoration: none;
        }

        .checkout-section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .checkout-login-hint {
            margin-bottom: 20px;
            font-size: 14px;
        }

        .checkout-login-hint a {
            color: #338dbc;
        }

        .form-group-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-control-custom {
            width: 100%;
            padding: 12px;
            border: 1px solid #d9d9d9;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .form-control-custom:focus {
            border-color: #338dbc;
            outline: none;
            box-shadow: 0 0 0 1px #338dbc;
        }

        .checkout-fieldset {
            margin-bottom: 30px;
        }

        .method-box {
            border: 1px solid #d9d9d9;
            border-radius: 4px;
            background: #fff;
            overflow: hidden;
        }

        .method-item {
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #d9d9d9;
            cursor: pointer;
        }

        .method-item:last-child {
            border-bottom: none;
        }

        .method-item input[type="radio"] {
            width: 18px;
            height: 18px;
        }

        .method-icon {
            width: 40px;
            text-align: center;
            color: #338dbc;
            font-size: 20px;
        }

        .checkout-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .btn-complete-order {
            background-color: #338dbc;
            color: #fff;
            border: none;
            padding: 15px 25px;
            border-radius: 4px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-complete-order:hover {
            background-color: #2b78a0;
        }

        .back-to-cart {
            color: #338dbc;
            text-decoration: none;
            font-size: 14px;
        }

        /* Sidebar styles */
        .order-summary-item {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            align-items: center;
        }

        .item-image {
            width: 60px;
            height: 60px;
            border: 1px solid #e1e1e1;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
            position: relative;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-qty-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #338dbc;
            color: #fff;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .item-attr {
            font-size: 12px;
            color: #999;
        }

        .item-price {
            font-size: 14px;
            color: #4b4b4b;
        }

        .coupon-area {
            display: flex;
            gap: 10px;
            padding: 20px 0;
            border-top: 1px solid #e1e1e1;
            border-bottom: 1px solid #e1e1e1;
            margin-bottom: 20px;
        }

        .btn-apply-coupon {
            background: #c8c8c8;
            color: #fff;
            border: none;
            padding: 0 15px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-apply-coupon.active {
            background: #338dbc;
        }

        .summary-totals {
            margin-bottom: 20px;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
            color: #717171;
        }

        .summary-line.total {
            border-top: 1px solid #e1e1e1;
            padding-top: 15px;
            color: #333;
            font-size: 18px;
            font-weight: 600;
        }

        .total-currency {
            font-size: 14px;
            color: #999;
            font-weight: normal;
            margin-right: 5px;
        }

        .why-choose-us {
            margin-top: 30px;
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #e1e1e1;
        }

        .why-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .why-item {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .why-icon {
            color: #27ae60;
            font-size: 16px;
        }

        @media (max-width: 991px) {
            .shopw-checkout-container {
                flex-direction: column;
                gap: 20px;
            }
            .shopw-checkout-right {
                width: 100%;
                min-height: auto;
                border-left: none;
                background: #fff;
            }
        }
    </style>

    <div class="shopw-checkout-container">
        <div class="shopw-checkout-left">
            <div class="checkout-logo">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('uploads/brand/dangduc-logo-horizontal-width-600.png') }}" alt="DangDuc Logo">
                </a>
            </div>

            <div class="checkout-breadcrumb">
                <a href="{{ url('/gio-hang') }}">Giỏ hàng</a> &nbsp; &rsaquo; &nbsp; Thông tin giao hàng
            </div>

            <div class="checkout-main-form">
                <div class="checkout-section-title">Thông tin giao hàng</div>

                <div class="checkout-login-hint">
                    @if($customerLoggedIn)
                        <i class="fa fa-user-circle"></i> {{ $checkoutCustomerDisplayName }} ({{ $currentCustomer->email }}) - <a href="{{ url('/logout-checkout') }}">Đăng xuất</a>
                    @else
                        Bạn đã có tài khoản? <a href="{{ url('/dang-nhap?redirect=checkout') }}">Đăng nhập</a>
                    @endif
                </div>

                <form id="checkout-form">
                    @csrf
                    <div class="form-group mb-3">
                        <input type="text" name="shipping_name" class="shipping_name form-control-custom"
                            value="{{ old('shipping_name', $currentCustomer?->name) }}"
                            placeholder="Họ và tên" required>
                    </div>

                    <div class="form-group-grid">
                        <input type="email" name="shipping_email" class="shipping_email form-control-custom"
                            value="{{ old('shipping_email', $currentCustomer?->email) }}"
                            placeholder="Email" required>
                        <input type="text" name="shipping_phone" class="shipping_phone form-control-custom"
                            value="{{ old('shipping_phone', $currentCustomer?->phone) }}"
                            placeholder="Số điện thoại" required>
                    </div>

                    <div class="form-group mb-3">
                        <input type="text" name="shipping_address" class="shipping_address form-control-custom"
                            value="{{ old('shipping_address') }}"
                            placeholder="Địa chỉ (ví dụ: 123 Đường ABC...)" required>
                    </div>

                    <div class="form-group-grid">
                        <select name="city_id" class="form-control-custom choose city" id="city">
                            <option value="">--- Tỉnh / thành ---</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                        <select name="province_id" class="form-control-custom choose province" id="province">
                            <option value="">--- Quận / huyện ---</option>
                        </select>
                    </div>

                    <div class="form-group-grid">
                        <select name="ward_id" class="form-control-custom wards" id="wards">
                            <option value="">--- Phường / xã ---</option>
                        </select>
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-default calculate_delivery w-100" style="height: 44px; border: 1px solid #d9d9d9; border-radius: 4px; background: #fff;">
                                <i class="fa fa-truck"></i> Tính phí ship
                            </button>
                        </div>
                    </div>

                    <div id="checkout_delivery_notice" style="margin-top: 10px;"></div>

                    <div class="form-group mt-3">
                        <textarea name="shipping_notes" class="shipping_notes form-control-custom" rows="3"
                            placeholder="Ghi chú (tùy chọn)">{{ old('shipping_notes') }}</textarea>
                    </div>

                    <div class="checkout-fieldset mt-4">
                        <div class="checkout-section-title">Phương thức vận chuyển</div>
                        <div class="method-box">
                            <div class="method-item">
                                <input type="radio" checked>
                                <div class="method-icon"><i class="fa fa-truck"></i></div>
                                <div class="flex-grow-1">Giao hàng tận nơi</div>
                                <div class="method-fee" data-checkout-fee-display="method">0đ</div>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-fieldset">
                        <div class="checkout-section-title">Phương thức thanh toán</div>
                        <div class="method-box">
                            <label class="method-item" style="cursor: pointer;">
                                <input type="radio" name="shipping_method" class="payment_select" value="cod" checked>
                                <div class="method-icon"><i class="fa fa-money"></i></div>
                                <div>Thanh toán khi nhận hàng (COD)</div>
                            </label>
                            <label class="method-item" style="cursor: pointer;">
                                <input type="radio" name="shipping_method" class="payment_select" value="bank_transfer">
                                <div class="method-icon"><i class="fa fa-university"></i></div>
                                <div>Chuyển khoản ngân hàng</div>
                            </label>
                        </div>
                    </div>

                    <input type="hidden" name="checkout_base_total" class="checkout_base_total" value="{{ $checkoutBaseTotal }}">
                    <input type="hidden" name="order_fee" class="order_fee" value="{{ $shipping_fee }}">
                    <input type="hidden" name="order_coupon" class="order_coupon" value="{{ $coupon['code'] ?? 'no' }}">
                    <input type="hidden" name="total_after" class="total_after" value="{{ $cartTotals['total'] }}">

                    <div class="checkout-footer">
                        <a href="{{ url('/gio-hang') }}" class="back-to-cart">&lsaquo; Quay về giỏ hàng</a>
                        <button type="button" class="btn-complete-order send_order">HOÀN TẤT ĐƠN HÀNG</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="shopw-checkout-right">
            <div class="order-summary">
                <div class="order-items-list">
                    @foreach ($cartItems as $item)
                        @php
                            $image = \App\Support\AssetUrl::product($item['product_image'] ?? null);
                            $subtotal = (float) $item['product_price'] * (int) $item['product_qty'];
                        @endphp
                        <div class="order-summary-item">
                            <div class="item-image">
                                <img src="{{ $image }}" alt="{{ $item['product_name'] }}">
                                <span class="item-qty-badge">{{ $item['product_qty'] }}</span>
                            </div>
                            <div class="item-info">
                                <div class="item-name">{{ $item['product_name'] }}</div>
                                @if (! empty($item['product_attribute']))
                                    <div class="item-attr">{{ $item['product_attribute'] }}</div>
                                @endif
                            </div>
                            <div class="item-price">{{ number_format($subtotal, 0, ',', '.') }}đ</div>
                        </div>
                    @endforeach
                </div>

                <div class="coupon-area">
                    <input type="text" id="coupon_code_input" class="form-control-custom" placeholder="Mã giảm giá" value="{{ $discountCode }}">
                    <button type="button" class="btn-apply-coupon {{ $discountCode ? '' : 'active' }}" id="apply_coupon_btn">Sử dụng</button>
                </div>

                <div class="summary-totals">
                    <div class="summary-line">
                        <span>Tạm tính</span>
                        <span>{{ number_format($cartTotals['subtotal'], 0, ',', '.') }}đ</span>
                    </div>
                    @if($cartTotals['discount'] > 0)
                    <div class="summary-line">
                        <span>Giảm giá</span>
                        <span>-{{ number_format($cartTotals['discount'], 0, ',', '.') }}đ</span>
                    </div>
                    @endif
                    <div class="summary-line">
                        <span>Phí vận chuyển</span>
                        <span data-checkout-fee-display="summary">{{ number_format($cartTotals['shipping'], 0, ',', '.') }}đ</span>
                    </div>
                    <div class="summary-line total">
                        <span>Tổng cộng</span>
                        <span data-checkout-grand-total-display="summary">{{ $checkoutTotal }}</span>
                    </div>
                </div>

                <div class="why-choose-us">
                    <div class="why-title"><i class="fa fa-shield" style="color: #338dbc;"></i> Lý do bạn chọn DangDuc</div>
                    <div class="why-item">
                        <i class="fa fa-check-circle why-icon"></i>
                        <span>Sản phẩm chính hãng, đảm bảo chất lượng.</span>
                    </div>
                    <div class="why-item">
                        <i class="fa fa-truck why-icon"></i>
                        <span>Giao hàng thần tốc, đóng gói cẩn thận.</span>
                    </div>
                    <div class="why-item">
                        <i class="fa fa-refresh why-icon"></i>
                        <span>Đổi trả dễ dàng, an tâm đặt hàng.</span>
                    </div>
                    <div class="why-item">
                        <i class="fa fa-headphones why-icon"></i>
                        <span>Hỗ trợ kỹ thuật 24/7 tận tâm.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script xử lý Coupon riêng cho trang Checkout --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const couponBtn = document.getElementById('apply_coupon_btn');
            const couponInput = document.getElementById('coupon_code_input');

            if (couponBtn && couponInput) {
                couponBtn.addEventListener('click', function() {
                    const code = couponInput.value.trim();
                    if (!code) {
                        swal("Thông báo", "Vui lòng nhập mã giảm giá", "warning");
                        return;
                    }

                    // Sử dụng logic ajax có sẵn trong interactions.blade.php nếu được,                    // hoặc gọi trực tiếp ở đây
                    $.ajax({
                        url: '{{ url('/check-coupon') }}',
                        method: 'POST',
                        data: {
                            coupon_code: code,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            // Sau khi check coupon thành công, load lại trang để cập nhật tiền
                            location.reload();
                        },
                        error: function(xhr) {
                            swal("Lỗi", "Mã giảm giá không hợp lệ hoặc đã hết hạn", "error");
                        }
                    });
                });
            }
        });
    </script>
@endsection

