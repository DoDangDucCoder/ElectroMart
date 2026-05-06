@extends('layouts.home-tech')

@section('body_class', 'g-checkout-redesign checkout-quick-page')

@section('content')
    @php
        $cartCount = $cartItems->sum('product_qty');
        $hasCoupon = !empty($coupon);
        $discountCode = data_get($coupon, 'code');
        $customerLoggedIn = (bool) $currentCustomer;
        $checkoutTotal = number_format($cartTotals['total'], 0, ',', '.') . 'đ';
        $checkoutBaseTotal = max(0, (float) $cartTotals['total'] - (float) $cartTotals['shipping']);
        $checkoutCustomerDisplayName = $currentCustomer?->name ?: ($currentCustomer?->email ?: 'Khách hàng');
        $checkoutDiscountTotal = (float) ($cartTotals['discount'] ?? 0);
    @endphp

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ====== PAGE BASE ====== */
        body.checkout-quick-page {
            background: #f3f5f7;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1f2937;
        }

        body.checkout-quick-page #header .header-bottom { display: none; }
        body.checkout-quick-page .shopw-topbar { background:#0f172a; }
        body.checkout-quick-page .shopw-topbar-pill { color:#e5e7eb; }

        .ckq-wrap {
            max-width: 1180px;
            margin: 0 auto;
            padding: 28px 16px 40px;
        }

        /* ====== BREADCRUMB ====== */
        .ckq-crumb { font-size: 13px; color:#6b7280; margin-bottom: 18px; display:flex; align-items:center; gap:8px; flex-wrap:wrap;}
        .ckq-crumb a { color:#6b7280; text-decoration:none; }
        .ckq-crumb a:hover { color:#0d9488; }
        .ckq-crumb .sep { color:#cbd5e1; }
        .ckq-crumb .current { color:#111827; font-weight:500; }

        /* ====== TITLE + STEPS ====== */
        .ckq-head {
            display:flex; justify-content:space-between; align-items:flex-end;
            gap: 24px; margin-bottom: 22px; flex-wrap: wrap;
        }
        .ckq-title h1 {
            font-size: 28px; font-weight: 800; letter-spacing: .5px;
            color:#0f172a; margin:0 0 6px;
        }
        .ckq-title p { font-size: 14px; color:#6b7280; margin:0; }

        .ckq-steps { display:flex; align-items:center; gap: 10px; }
        .ckq-step { display:flex; flex-direction:column; align-items:center; gap:8px; min-width:120px;}
        .ckq-step .dot {
            width: 38px; height: 38px; border-radius: 50%;
            display:flex; align-items:center; justify-content:center;
            background:#fff; border:2px solid #e5e7eb; color:#9ca3af;
            font-weight:600; font-size: 14px;
        }
        .ckq-step .label { font-size: 13px; color:#9ca3af; }
        .ckq-step.is-active .dot {
            background:#0d9488; border-color:#0d9488; color:#fff;
            box-shadow: 0 6px 14px rgba(13,148,136,.25);
        }
        .ckq-step.is-active .label { color:#0d9488; font-weight:600; }
        .ckq-line { flex:1; height:2px; background:#e5e7eb; min-width:40px; max-width:90px; margin-bottom: 26px;}

        /* ====== TWO COLUMN LAYOUT ====== */
        .ckq-grid {
            display:grid;
            grid-template-columns: 1fr 420px;
            gap: 24px;
            align-items:flex-start;
        }
        @media (max-width: 991px) {
            .ckq-grid { grid-template-columns: 1fr; }
        }

        .ckq-card {
            background:#fff;
            border-radius: 12px;
            border:1px solid #e5e7eb;
            box-shadow: 0 1px 2px rgba(15,23,42,.04);
            padding: 22px 24px;
        }

        .ckq-section-title {
            font-size: 13px; font-weight: 700; letter-spacing: 1px;
            color:#0f172a; text-transform: uppercase; margin: 0 0 16px;
        }
        .ckq-section + .ckq-section { margin-top: 26px; }

        /* ====== FORM ====== */
        .ckq-row { display:grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;}
        .ckq-row.single { grid-template-columns: 1fr; }
        .ckq-row.three { grid-template-columns: 1fr 1fr 1fr; }
        @media (max-width: 600px) {
            .ckq-row, .ckq-row.three { grid-template-columns: 1fr; }
        }

        .ckq-field { position: relative; }
        .ckq-field label {
            display:block; font-size: 13px; font-weight: 500;
            color:#374151; margin-bottom: 6px;
        }
        .ckq-field label .req { color:#ef4444; margin-left: 2px; }
        .ckq-field .hint { color:#9ca3af; font-weight:400; margin-left:4px; font-size:12px;}

        .ckq-input,
        .ckq-select,
        .ckq-textarea {
            width: 100%; height: 44px;
            background:#fff; border:1px solid #e5e7eb;
            border-radius: 8px; padding: 0 14px;
            font-size: 14px; color:#111827;
            transition: border-color .15s, box-shadow .15s;
            font-family: inherit;
        }
        .ckq-input.has-icon { padding-left: 38px; }
        .ckq-textarea { height: auto; padding: 12px 14px; resize: vertical; min-height: 80px; }
        .ckq-input:focus, .ckq-select:focus, .ckq-textarea:focus {
            outline:none; border-color:#0d9488;
            box-shadow: 0 0 0 3px rgba(13,148,136,.12);
        }
        .ckq-field .ckq-input-icon {
            position:absolute; left: 12px; top: 50%;
            transform: translateY(-50%); color:#94a3b8; font-size: 14px;
            pointer-events:none;
        }
        .ckq-field.with-icon .ckq-input-icon { top: calc(50% + 11px); }

        .ckq-textarea-wrap { position: relative; }
        .ckq-textarea-counter {
            position:absolute; right: 12px; bottom: 8px;
            font-size: 12px; color:#9ca3af;
        }

        /* select arrow */
        select.ckq-select {
            appearance:none; -webkit-appearance:none;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>");
            background-repeat:no-repeat; background-position: right 14px center; padding-right: 36px;
        }

        /* ====== METHOD CARDS ====== */
        .ckq-methods-grid {
            display:grid; grid-template-columns: 1fr 1fr; gap: 12px;
        }
        @media (max-width: 600px) { .ckq-methods-grid { grid-template-columns: 1fr; } }

        .ckq-method {
            border:1.5px solid #e5e7eb; border-radius: 10px;
            padding: 14px 14px 14px 14px;
            display:flex; align-items:center; gap:12px;
            cursor:pointer; transition: all .15s;
            background:#fff;
        }
        .ckq-method:hover { border-color:#cbd5e1; }
        .ckq-method.is-active { border-color:#0d9488; background:#f0fdfa; }
        .ckq-method input[type="radio"] { position:absolute; opacity:0; pointer-events:none;}
        .ckq-method .ckq-radio-fake {
            width: 20px; height: 20px; border-radius: 50%;
            border: 2px solid #cbd5e1; flex: 0 0 20px;
            display:flex; align-items:center; justify-content:center;
            transition: all .15s;
        }
        .ckq-method.is-active .ckq-radio-fake { border-color:#0d9488; }
        .ckq-method.is-active .ckq-radio-fake::after {
            content:''; width: 10px; height: 10px; border-radius: 50%; background:#0d9488;
        }
        .ckq-method-icon {
            width: 38px; height: 38px; border-radius: 8px;
            background:#f1f5f9; color:#0f172a;
            display:flex; align-items:center; justify-content:center; font-size: 16px;
            flex: 0 0 38px;
        }
        .ckq-method.is-active .ckq-method-icon { background:#ccfbf1; color:#0d9488; }
        .ckq-method-body { flex:1; min-width: 0;}
        .ckq-method-title { font-size: 14px; font-weight: 600; color:#111827; }
        .ckq-method-sub { font-size: 12px; color:#6b7280; margin-top: 2px;}
        .ckq-method-fee { font-size: 13px; font-weight: 600; color:#0f172a;}

        .ckq-payment-list { display:flex; flex-direction: column; gap: 10px; }
        .ckq-payment-list .ckq-method { padding: 12px 14px; }
        .ckq-pay-brands { display:flex; gap: 6px; align-items:center; }
        .ckq-pay-brand {
            height: 22px; padding: 2px 6px;
            border:1px solid #e5e7eb; border-radius: 4px; background:#fff;
            display:inline-flex; align-items:center; justify-content:center;
            font-size: 10px; font-weight: 700; letter-spacing:.4px;
            color:#1f2937;
        }
        .ckq-pay-brand.visa { color:#1a1f71; border-color:#1a1f71; }
        .ckq-pay-brand.mc { color:#eb001b; border-color:#eb001b; }
        .ckq-pay-brand.jcb { color:#0e4c96; border-color:#0e4c96; }
        .ckq-pay-brand.momo { color:#a50064; border-color:#a50064; background:#fff0f7; }
        .ckq-pay-brand.zalo { color:#0068ff; border-color:#0068ff; background:#eef5ff; }

        /* ====== FOOTER NOTE ====== */
        .ckq-secure-note {
            margin-top: 22px;
            font-size: 13px; color:#6b7280;
            display:flex; align-items:center; gap: 10px;
            padding-top: 16px; border-top: 1px dashed #e5e7eb;
        }
        .ckq-secure-note i { color:#0d9488; }

        /* ====== RIGHT SUMMARY ====== */
        .ckq-summary { position:sticky; top: 16px; }
        .ckq-summary-head {
            display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;
        }
        .ckq-summary-head .title {
            font-size: 13px; font-weight:700; letter-spacing:1px; text-transform:uppercase;
        }
        .ckq-summary-head .title .qty { color:#6b7280; font-weight:500; text-transform:none; letter-spacing:0; margin-left:6px;}
        .ckq-summary-head a { color:#0d9488; font-size: 13px; text-decoration:none; }
        .ckq-summary-head a:hover { text-decoration: underline; }

        .ckq-product { display:flex; gap: 12px; align-items:center; padding: 10px 0; }
        .ckq-product + .ckq-product { border-top: 1px solid #f1f5f9; }
        .ckq-product-thumb {
            width: 56px; height: 56px; border-radius: 8px;
            border:1px solid #e5e7eb; background:#fff;
            display:flex; align-items:center; justify-content:center;
            overflow:hidden; flex: 0 0 56px;
        }
        .ckq-product-thumb img { width:100%; height:100%; object-fit:cover; }
        .ckq-product-info { flex:1; min-width:0;}
        .ckq-product-name { font-size: 13.5px; font-weight: 600; color:#111827; line-height:1.35; margin-bottom:2px; }
        .ckq-product-meta { font-size: 12px; color:#6b7280; }
        .ckq-product-price { font-size: 14px; font-weight:700; color:#dc2626; white-space: nowrap; }

        .ckq-totals { margin-top: 16px; padding-top: 16px; border-top: 1px solid #f1f5f9; }
        .ckq-line { display:flex; justify-content:space-between; padding: 6px 0; font-size: 14px; color:#374151; }
        .ckq-line.muted { color:#6b7280; }
        .ckq-line .v.discount { color:#16a34a; font-weight: 600; }
        .ckq-grand {
            display:flex; justify-content:space-between; align-items:center;
            padding: 14px 0 4px; border-top: 1px solid #f1f5f9; margin-top: 6px;
        }
        .ckq-grand .lbl { font-size: 14px; font-weight: 700; letter-spacing:.5px; text-transform:uppercase; }
        .ckq-grand .val { font-size: 24px; font-weight: 800; color:#dc2626; }
        .ckq-vat { text-align:right; font-size: 12px; color:#9ca3af; margin-bottom: 12px; }

        .ckq-save-pill {
            background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46;
            border-radius: 8px; padding: 10px 12px;
            font-size: 13px; display:flex; align-items:center; gap: 8px;
            margin-bottom: 16px;
        }
        .ckq-save-pill i { color:#10b981; }

        .ckq-coupon { display:flex; gap: 8px; margin-bottom: 14px; }
        .ckq-coupon .ckq-input { flex: 1; }
        .ckq-coupon-btn {
            height: 44px; padding: 0 16px; border-radius: 8px;
            background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;
            font-weight:600; font-size: 13px; cursor:pointer;
            transition: all .15s;
        }
        .ckq-coupon-btn:hover { background:#0d9488; color:#fff; border-color:#0d9488; }

        .ckq-place-btn {
            width: 100%; height: 52px; border:none; border-radius: 10px;
            background:#0d9488; color:#fff;
            font-size: 15px; font-weight: 700; letter-spacing:.4px;
            display:flex; align-items:center; justify-content:center; gap: 8px;
            cursor:pointer; transition: all .15s;
            box-shadow: 0 8px 18px rgba(13,148,136,.25);
        }
        .ckq-place-btn:hover { background:#0f766e; }
        .ckq-place-btn:disabled { opacity:.7; cursor: not-allowed;}

        .ckq-terms {
            text-align:center; font-size: 12px; color:#6b7280; margin-top: 12px;
        }
        .ckq-terms a { color:#0d9488; text-decoration:none; }

        /* ====== TRUST STRIP ====== */
        .ckq-trust {
            margin-top: 22px;
            background:#fff; border:1px solid #e5e7eb; border-radius: 12px;
            padding: 18px 20px;
            display:grid; grid-template-columns: repeat(4, 1fr); gap: 18px;
        }
        @media (max-width: 768px) { .ckq-trust { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .ckq-trust { grid-template-columns: 1fr; } }

        .ckq-trust-item { display:flex; gap: 12px; align-items:flex-start; }
        .ckq-trust-icon {
            width: 40px; height: 40px; border-radius: 10px;
            background:#f0fdfa; color:#0d9488;
            display:flex; align-items:center; justify-content:center;
            font-size: 18px; flex: 0 0 40px;
        }
        .ckq-trust-title { font-size: 13.5px; font-weight: 700; color:#111827; }
        .ckq-trust-sub { font-size: 12px; color:#6b7280; line-height:1.45; margin-top:2px;}

        /* ====== LOGIN HINT ====== */
        .ckq-login-hint {
            background:#f8fafc; border:1px dashed #cbd5e1;
            border-radius: 8px; padding: 10px 14px;
            font-size: 13px; color:#475569;
            margin-bottom: 18px;
        }
        .ckq-login-hint a { color:#0d9488; font-weight:600; text-decoration:none; }

        /* ====== Notice for delivery ====== */
        #checkout_delivery_notice:not(:empty) { margin: 8px 0 0; }
        #checkout_delivery_notice .alert { margin: 0; padding: 8px 12px; font-size: 13px; border-radius:8px;}

        /* ====== Select2 fixes ====== */
        body.checkout-quick-page .select2-container .select2-selection--single {
            height: 44px !important; border:1px solid #e5e7eb !important;
            border-radius: 8px !important;
        }
        body.checkout-quick-page .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 42px !important; padding-left: 14px; color:#111827;
        }
        body.checkout-quick-page .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px !important;
        }
    </style>

    <div class="ckq-wrap">
        @include('partials.storefront.breadcrumb', [
            'class' => 'ckq-crumb',
            'separator' => '›',
            'items' => [
                [
                    'label' => 'Giỏ hàng',
                    'url' => url('/gio-hang'),
                ],
                [
                    'label' => 'Thanh toán',
                    'current' => true,
                ],
            ],
        ])

        <div class="ckq-head">
            <div class="ckq-title">
                <h1>THANH TOÁN NHANH</h1>
                <p>Vui lòng kiểm tra thông tin đơn hàng và thanh toán</p>
            </div>
            <div class="ckq-steps">
                <div class="ckq-step is-active">
                    <div class="dot"><i class="fa fa-shopping-cart"></i></div>
                    <div class="label">Thông tin đơn hàng</div>
                </div>
                <div class="ckq-line"></div>
                <div class="ckq-step">
                    <div class="dot">2</div>
                    <div class="label">Thanh toán</div>
                </div>
                <div class="ckq-line"></div>
                <div class="ckq-step">
                    <div class="dot">3</div>
                    <div class="label">Hoàn tất</div>
                </div>
            </div>
        </div>

        <div class="ckq-grid">
            {{-- LEFT --}}
            <div>
                <form id="checkout-form" class="ckq-card">
                    @csrf

                    <div class="ckq-login-hint">
                        @if($customerLoggedIn)
                            <i class="fa fa-user-circle"></i> Xin chào <strong>{{ $checkoutCustomerDisplayName }}</strong>
                            ({{ $currentCustomer->email }}) —
                            <form method="POST" action="{{ url('/logout-checkout') }}" style="display:inline;">
                                @csrf
                                <button type="submit" style="border:0;background:transparent;padding:0;color:#0d9488;font-weight:600;">
                                    Đăng xuất
                                </button>
                            </form>
                        @else
                            Bạn đã có tài khoản? <a href="{{ url('/dang-nhap?redirect=checkout') }}">Đăng nhập</a> để thanh toán nhanh hơn.
                        @endif
                    </div>

                    {{-- THÔNG TIN NHẬN HÀNG --}}
                    <div class="ckq-section">
                        <h3 class="ckq-section-title">Thông tin nhận hàng</h3>

                        <div class="ckq-row">
                            <div class="ckq-field">
                                <label>Họ và tên <span class="req">*</span></label>
                                <i class="fa fa-user ckq-input-icon" style="top: calc(50% + 11px);"></i>
                                <input type="text" name="shipping_name"
                                    class="shipping_name ckq-input has-icon"
                                    value="{{ old('shipping_name', $currentCustomer?->name) }}"
                                    placeholder="Nguyễn Văn An" required>
                            </div>
                            <div class="ckq-field">
                                <label>Số điện thoại <span class="req">*</span></label>
                                <i class="fa fa-phone ckq-input-icon" style="top: calc(50% + 11px);"></i>
                                <input type="text" name="shipping_phone"
                                    class="shipping_phone ckq-input has-icon"
                                    value="{{ old('shipping_phone', $currentCustomer?->phone) }}"
                                    placeholder="0912 345 678" required>
                            </div>
                        </div>

                        <div class="ckq-row single">
                            <div class="ckq-field">
                                <label>Email <span class="req">*</span></label>
                                <i class="fa fa-envelope ckq-input-icon" style="top: calc(50% + 11px);"></i>
                                <input type="email" name="shipping_email"
                                    class="shipping_email ckq-input has-icon"
                                    value="{{ old('shipping_email', $currentCustomer?->email) }}"
                                    placeholder="email@example.com" required>
                            </div>
                        </div>

                        <div class="ckq-row single">
                            <div class="ckq-field">
                                <label>Địa chỉ nhận hàng <span class="req">*</span></label>
                                <i class="fa fa-map-marker ckq-input-icon" style="top: calc(50% + 11px);"></i>
                                <input type="text" name="shipping_address"
                                    class="shipping_address ckq-input has-icon"
                                    value="{{ old('shipping_address') }}"
                                    placeholder="Số nhà, tên đường (vd: 123 Nguyễn Văn Cừ)" required>
                            </div>
                        </div>

                        <div class="ckq-row three">
                            <div class="ckq-field">
                                <label>Tỉnh / Thành phố <span class="hint">(không bắt buộc)</span></label>
                                <select name="city_id" class="ckq-select choose city" id="city">
                                    <option value="">--- Chọn tỉnh / TP nếu có dữ liệu ---</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="ckq-field">
                                <label>Quận / Huyện <span class="hint">(không bắt buộc)</span></label>
                                <select name="province_id" class="ckq-select choose province" id="province">
                                    <option value="">--- Chọn quận ---</option>
                                </select>
                            </div>
                            <div class="ckq-field">
                                <label>Phường / Xã <span class="hint">(không bắt buộc)</span></label>
                                <select name="ward_id" class="ckq-select wards" id="wards">
                                    <option value="">--- Chọn phường ---</option>
                                </select>
                            </div>
                        </div>

                        <div class="ckq-row single" style="margin-bottom: 0;">
                            <button type="button" class="calculate_delivery ckq-coupon-btn" style="height:40px;">
                                <i class="fa fa-truck"></i> Tính phí vận chuyển
                            </button>
                            <div id="checkout_delivery_notice"></div>
                        </div>

                        <div class="ckq-row single" style="margin-top: 14px; margin-bottom: 0;">
                            <div class="ckq-field">
                                <label>Ghi chú đơn hàng <span class="hint">(không bắt buộc)</span></label>
                                <div class="ckq-textarea-wrap">
                                    <textarea name="shipping_notes"
                                        class="shipping_notes ckq-textarea"
                                        rows="3" maxlength="200"
                                        placeholder="Ví dụ: Giao hàng ngoài giờ hành chính, gọi trước khi giao..."
                                        oninput="document.getElementById('ckq-note-count').textContent = this.value.length"
                                        >{{ old('shipping_notes') }}</textarea>
                                    <span class="ckq-textarea-counter"><span id="ckq-note-count">{{ strlen(old('shipping_notes', '')) }}</span>/200</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PHƯƠNG THỨC GIAO HÀNG --}}
                    <div class="ckq-section">
                        <h3 class="ckq-section-title">Phương thức giao hàng</h3>
                        <div class="ckq-methods-grid">
                            <label class="ckq-method is-active" data-shipping-method>
                                <input type="radio" name="shipping_speed" value="fast" checked>
                                <span class="ckq-radio-fake"></span>
                                <span class="ckq-method-icon"><i class="fa fa-bolt"></i></span>
                                <span class="ckq-method-body">
                                    <span class="ckq-method-title">Giao hàng nhanh</span>
                                    <span class="ckq-method-sub">Nhận hàng từ 2 - 4 giờ</span>
                                </span>
                                <span class="ckq-method-fee" data-checkout-fee-display="method">{{ number_format($cartTotals['shipping'], 0, ',', '.') }}đ</span>
                            </label>
                            <label class="ckq-method" data-shipping-method>
                                <input type="radio" name="shipping_speed" value="standard">
                                <span class="ckq-radio-fake"></span>
                                <span class="ckq-method-icon"><i class="fa fa-truck"></i></span>
                                <span class="ckq-method-body">
                                    <span class="ckq-method-title">Giao hàng tiêu chuẩn</span>
                                    <span class="ckq-method-sub">Nhận hàng từ 1 - 2 ngày</span>
                                </span>
                                <span class="ckq-method-fee">Miễn phí</span>
                            </label>
                        </div>
                    </div>

                    {{-- PHƯƠNG THỨC THANH TOÁN --}}
                    <div class="ckq-section">
                        <h3 class="ckq-section-title">Phương thức thanh toán</h3>
                        <div class="ckq-payment-list">
                            <label class="ckq-method is-active" data-payment>
                                <input type="radio" name="shipping_method" class="payment_select" value="cod" checked>
                                <span class="ckq-radio-fake"></span>
                                <span class="ckq-method-body">
                                    <span class="ckq-method-title">Thanh toán khi nhận hàng (COD)</span>
                                    <span class="ckq-method-sub">Thanh toán bằng tiền mặt khi nhận hàng</span>
                                </span>
                            </label>
                            <label class="ckq-method" data-payment>
                                <input type="radio" name="shipping_method" class="payment_select" value="bank_transfer">
                                <span class="ckq-radio-fake"></span>
                                <span class="ckq-method-body">
                                    <span class="ckq-method-title">Chuyển khoản ngân hàng</span>
                                    <span class="ckq-method-sub">Quét mã QR hoặc chuyển khoản</span>
                                </span>
                            </label>
                            <label class="ckq-method" data-payment>
                                <input type="radio" name="shipping_method" class="payment_select" value="card">
                                <span class="ckq-radio-fake"></span>
                                <span class="ckq-method-body">
                                    <span class="ckq-method-title">Thẻ tín dụng / Thẻ ghi nợ</span>
                                </span>
                                <span class="ckq-pay-brands">
                                    <span class="ckq-pay-brand visa">VISA</span>
                                    <span class="ckq-pay-brand mc">MC</span>
                                    <span class="ckq-pay-brand jcb">JCB</span>
                                </span>
                            </label>
                            <label class="ckq-method" data-payment>
                                <input type="radio" name="shipping_method" class="payment_select" value="ewallet">
                                <span class="ckq-radio-fake"></span>
                                <span class="ckq-method-body">
                                    <span class="ckq-method-title">Ví điện tử</span>
                                </span>
                                <span class="ckq-pay-brands">
                                    <span class="ckq-pay-brand momo">MoMo</span>
                                    <span class="ckq-pay-brand zalo">ZaloPay</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="ckq-secure-note">
                        <i class="fa fa-shield"></i>
                        Thông tin của bạn được bảo mật tuyệt đối. Đơn hàng sẽ được xử lý nhanh chóng và an toàn.
                    </div>

                    <input type="hidden" name="checkout_base_total" class="checkout_base_total" value="{{ $checkoutBaseTotal }}">
                    <input type="hidden" name="order_fee" class="order_fee" value="{{ $shipping_fee }}">
                    <input type="hidden" name="order_coupon" class="order_coupon" value="{{ $coupon['code'] ?? 'no' }}">
                    <input type="hidden" name="total_after" class="total_after" value="{{ $cartTotals['total'] }}">
                </form>
            </div>

            {{-- RIGHT - SUMMARY --}}
            <aside>
                <div class="ckq-card ckq-summary">
                    <div class="ckq-summary-head">
                        <span class="title">Đơn hàng của bạn <span class="qty">({{ $cartCount }} sản phẩm)</span></span>
                        <a href="{{ url('/gio-hang') }}">Chỉnh sửa giỏ hàng</a>
                    </div>

                    <div>
                        @foreach ($cartItems as $item)
                            @php
                                $image = \App\Support\AssetUrl::product($item['product_image'] ?? null);
                                $subtotal = (float) $item['product_price'] * (int) $item['product_qty'];
                            @endphp
                            <div class="ckq-product">
                                <div class="ckq-product-thumb">
                                    <img src="{{ $image }}" alt="{{ $item['product_name'] }}">
                                </div>
                                <div class="ckq-product-info">
                                    <div class="ckq-product-name">{{ $item['product_name'] }}</div>
                                    <div class="ckq-product-meta">
                                        @if (! empty($item['product_attribute']))
                                            {{ $item['product_attribute'] }} ·
                                        @endif
                                        x{{ $item['product_qty'] }}
                                    </div>
                                </div>
                                <div class="ckq-product-price">{{ number_format($subtotal, 0, ',', '.') }}đ</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="ckq-totals">
                        <div class="ckq-coupon">
                            <input type="text" id="coupon_code_input" class="ckq-input"
                                placeholder="Nhập mã giảm giá" value="{{ $discountCode }}">
                            <button type="button" class="ckq-coupon-btn" id="apply_coupon_btn">Áp dụng</button>
                        </div>

                        <div class="ckq-line muted">
                            <span>Tạm tính ({{ $cartCount }} sản phẩm)</span>
                            <span>{{ number_format($cartTotals['subtotal'], 0, ',', '.') }}đ</span>
                        </div>
                        <div class="ckq-line muted">
                            <span>Phí vận chuyển</span>
                            <span data-checkout-fee-display="summary">{{ number_format($cartTotals['shipping'], 0, ',', '.') }}đ</span>
                        </div>
                        @if($checkoutDiscountTotal > 0)
                            <div class="ckq-line">
                                <span>Giảm giá @if($discountCode) (Mã: {{ $discountCode }}) @endif</span>
                                <span class="v discount">-{{ number_format($checkoutDiscountTotal, 0, ',', '.') }}đ</span>
                            </div>
                        @endif
                    </div>

                    <div class="ckq-grand">
                        <span class="lbl">Tổng cộng</span>
                        <span class="val" data-checkout-grand-total-display="summary">{{ $checkoutTotal }}</span>
                    </div>
                    <div class="ckq-vat">(Đã bao gồm VAT)</div>

                    @if($checkoutDiscountTotal > 0)
                        <div class="ckq-save-pill">
                            <i class="fa fa-tag"></i>
                            Bạn sẽ tiết kiệm được <strong style="margin: 0 4px;">{{ number_format($checkoutDiscountTotal, 0, ',', '.') }}đ</strong> cho đơn hàng này!
                        </div>
                    @endif

                    <button type="button" class="ckq-place-btn send_order">
                        <i class="fa fa-lock"></i> Đặt hàng và thanh toán
                    </button>

                    <div class="ckq-terms">
                        Bằng việc đặt hàng, bạn đồng ý với
                        <a href="#">Điều khoản sử dụng</a> và
                        <a href="#">Chính sách bảo mật</a> của DangDuc.
                    </div>
                </div>
            </aside>
        </div>

        {{-- TRUST STRIP --}}
        <div class="ckq-trust">
            <div class="ckq-trust-item">
                <div class="ckq-trust-icon"><i class="fa fa-truck"></i></div>
                <div>
                    <div class="ckq-trust-title">Miễn phí giao hàng</div>
                    <div class="ckq-trust-sub">Áp dụng cho đơn hàng công nghệ đủ điều kiện</div>
                </div>
            </div>
            <div class="ckq-trust-item">
                <div class="ckq-trust-icon"><i class="fa fa-refresh"></i></div>
                <div>
                    <div class="ckq-trust-title">Đổi trả hàng</div>
                    <div class="ckq-trust-sub">Hỗ trợ đổi trả trong 30 ngày nếu có lỗi do NSX</div>
                </div>
            </div>
            <div class="ckq-trust-item">
                <div class="ckq-trust-icon"><i class="fa fa-shield"></i></div>
                <div>
                    <div class="ckq-trust-title">Bảo hành chính hãng</div>
                    <div class="ckq-trust-sub">Cam kết sản phẩm chính hãng 100%</div>
                </div>
            </div>
            <div class="ckq-trust-item">
                <div class="ckq-trust-icon"><i class="fa fa-headphones"></i></div>
                <div>
                    <div class="ckq-trust-title">Hỗ trợ tư vấn 24/7</div>
                    <div class="ckq-trust-sub">Đội ngũ chuyên viên sẵn sàng hỗ trợ</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Page-specific JS: method toggling + coupon --}}
    <script>
        (function () {
            // Toggle active state for method/payment cards
            document.querySelectorAll('.ckq-method').forEach(function (el) {
                el.addEventListener('click', function (e) {
                    var input = el.querySelector('input[type="radio"]');
                    if (!input) return;
                    var name = input.getAttribute('name');
                    document.querySelectorAll('input[type="radio"][name="' + name + '"]').forEach(function (r) {
                        var card = r.closest('.ckq-method');
                        if (card) card.classList.remove('is-active');
                    });
                    input.checked = true;
                    el.classList.add('is-active');
                });
            });

            // Coupon apply
            var couponBtn = document.getElementById('apply_coupon_btn');
            var couponInput = document.getElementById('coupon_code_input');
            if (couponBtn && couponInput) {
                couponBtn.addEventListener('click', function () {
                    var code = (couponInput.value || '').trim();
                    if (!code) {
                        if (typeof swal === 'function') {
                            swal('Thông báo', 'Vui lòng nhập mã giảm giá', 'warning');
                        }
                        return;
                    }
                    if (typeof $ === 'undefined') return;
                    $.ajax({
                        url: '{{ url('/check-coupon') }}',
                        method: 'POST',
                        data: { coupon: code, _token: '{{ csrf_token() }}' },
                        success: function () { location.reload(); },
                        error: function () {
                            if (typeof swal === 'function') {
                                swal('Lỗi', 'Mã giảm giá không hợp lệ hoặc đã hết hạn', 'error');
                            }
                        }
                    });
                });
            }
        })();
    </script>
@endsection
