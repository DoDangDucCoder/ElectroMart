@extends('layouts.home-tech')

@section('body_class', 'g-cart cart-v2-page')

@section('content')
    @php
        $cartCount = $cartItems->sum('product_qty');
        $hasCoupon = !empty($coupon);
        $discountCode = data_get($coupon, 'code');
        $isLoggedIn = (bool) Session::get('customer_id');
        $checkoutUrl = url('/checkout');
        $totals = $cartTotals ?? ['subtotal' => 0, 'discount' => 0, 'shipping' => 0, 'total' => 0];
        $itemsTotalQty = (int) $cartCount;
        $hasFreeShipping = (float) $totals['shipping'] === 0.0;
    @endphp

    <main class="cart-v2">
        <div class="cart-v2-container">
            @include('partials.storefront.breadcrumb', [
                'class' => 'cart-v2-breadcrumb',
                'separator' => '›',
                'separatorClass' => 'cart-v2-breadcrumb-sep',
                'items' => [
                    [
                        'label' => 'Giỏ hàng',
                        'current' => true,
                    ],
                ],
            ])

            @if (session('message'))
                <div class="cart-v2-flash cart-v2-flash--success">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                    <span>{!! session('message') !!}</span>
                </div>
            @elseif(session('error'))
                <div class="cart-v2-flash cart-v2-flash--error">
                    <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                    <span>{!! session('error') !!}</span>
                </div>
            @endif

            <header class="cart-v2-pagehead">
                <h1 class="cart-v2-title">
                    Giỏ hàng của bạn
                    @if (!$cartItems->isEmpty())
                        <span class="cart-v2-title-meta">({{ $itemsTotalQty }} sản phẩm)</span>
                    @endif
                </h1>
                <a href="{{ url('/san-pham') }}" class="cart-v2-back">
                    <i class="fa fa-angle-left" aria-hidden="true"></i>
                    Tiếp tục mua hàng
                </a>
            </header>

            @if ($cartItems->isEmpty())
                <div class="cart-v2-empty">
                    <div class="cart-v2-empty-icon">
                        <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                    </div>
                    <h2>Giỏ hàng đang trống</h2>
                    <p>Hãy khám phá các sản phẩm công nghệ mới nhất và thêm vào giỏ hàng của bạn.</p>
                    <div class="cart-v2-empty-actions">
                        @if (!$isLoggedIn)
                            <a href="{{ url('/dang-nhap') }}" class="cart-v2-btn cart-v2-btn--primary">Đăng nhập</a>
                        @endif
                        <a href="{{ url('/san-pham') }}" class="cart-v2-btn {{ $isLoggedIn ? 'cart-v2-btn--primary' : 'cart-v2-btn--ghost' }}">
                            Khám phá sản phẩm
                        </a>
                    </div>
                </div>
            @else
                <div class="cart-v2-grid">
                    {{-- ===== LEFT: Cart items ===== --}}
                    <section class="cart-v2-card cart-v2-list" aria-label="Danh sách sản phẩm">
                        <form action="{{ url('/update-cart') }}" method="POST" id="cart-v2-form">
                            @csrf

                            <div class="cart-v2-list-head">
                                <label class="cart-v2-check">
                                    <input type="checkbox" id="cart-v2-check-all" checked>
                                    <span class="cart-v2-check-box"></span>
                                </label>
                                <div class="cart-v2-col cart-v2-col-product">Sản phẩm</div>
                                <div class="cart-v2-col cart-v2-col-price">Đơn giá</div>
                                <div class="cart-v2-col cart-v2-col-qty">Số lượng</div>
                                <div class="cart-v2-col cart-v2-col-total">Thành tiền</div>
                                <div class="cart-v2-col cart-v2-col-action">Thao tác</div>
                            </div>

                            <div class="cart-v2-list-body">
                                @foreach ($cartItems as $item)
                                    @php
                                        $image = \App\Support\AssetUrl::product($item['product_image'] ?? null);
                                        $qty = (int) $item['product_qty'];
                                        $price = (float) $item['product_price'];
                                        $subtotal = $price * $qty;
                                        $stock = max(1, (int) ($item['product_quantity'] ?? 1));
                                        $attribute = trim((string) ($item['product_attribute'] ?? ''));
                                        $originalPrice = (float) ($item['product_original_price'] ?? $item['product_price']);
                                        $hasDiscount = $originalPrice > $price;
                                        $discountPct = $hasDiscount && $originalPrice > 0
                                            ? (int) round(($originalPrice - $price) / $originalPrice * 100)
                                            : 0;
                                        $sid = $item['session_id'];
                                    @endphp

                                    <article class="cart-v2-row" data-cart-row data-line-total="{{ $subtotal }}">
                                        <label class="cart-v2-check">
                                            <input type="checkbox" class="cart-v2-row-check" checked>
                                            <span class="cart-v2-check-box"></span>
                                        </label>

                                        <div class="cart-v2-col cart-v2-col-product">
                                            <div class="cart-v2-product">
                                                <a href="javascript:void(0)" class="cart-v2-thumb">
                                                    <img src="{{ $image }}" alt="{{ $item['product_name'] }}" loading="lazy">
                                                </a>
                                                <div class="cart-v2-product-info">
                                                    <h3 class="cart-v2-product-name">{{ $item['product_name'] }}</h3>
                                                    @if ($attribute !== '')
                                                        <p class="cart-v2-product-variant">
                                                            <span class="cart-v2-variant-dot" aria-hidden="true"></span>
                                                            {{ $attribute }}
                                                        </p>
                                                    @endif
                                                    <p class="cart-v2-product-stock">
                                                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                                                        Còn hàng
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="cart-v2-col cart-v2-col-price">
                                            <div class="cart-v2-price">
                                                <strong class="cart-v2-price-current">{{ number_format($price, 0, ',', '.') }}đ</strong>
                                                @if ($hasDiscount)
                                                    <span class="cart-v2-price-old">{{ number_format($originalPrice, 0, ',', '.') }}đ</span>
                                                    <span class="cart-v2-price-pct">-{{ $discountPct }}%</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="cart-v2-col cart-v2-col-qty">
                                            <div class="cart-v2-stepper" data-stepper data-max="{{ $stock }}">
                                                <button type="button" class="cart-v2-stepper-btn" data-act="dec" aria-label="Giảm số lượng">
                                                    <i class="fa fa-minus" aria-hidden="true"></i>
                                                </button>
                                                <input type="number"
                                                    class="cart-v2-stepper-input"
                                                    name="cart_qty[{{ $sid }}]"
                                                    value="{{ $qty }}"
                                                    min="1"
                                                    max="{{ $stock }}"
                                                    data-unit-price="{{ $price }}"
                                                    aria-label="Số lượng">
                                                <button type="button" class="cart-v2-stepper-btn" data-act="inc" aria-label="Tăng số lượng">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="cart-v2-col cart-v2-col-total">
                                            <strong class="cart-v2-line-total" data-line-total-display>
                                                {{ number_format($subtotal, 0, ',', '.') }}đ
                                            </strong>
                                        </div>

                                        <div class="cart-v2-col cart-v2-col-action">
                                            <button type="button" class="cart-v2-row-remove"
                                                data-cart-remove="{{ $sid }}"
                                                aria-label="Xóa sản phẩm">
                                                <i class="fa fa-trash-o" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            <div class="cart-v2-list-foot">
                                <div class="cart-v2-list-foot-left">
                                    <label class="cart-v2-check cart-v2-check--inline">
                                        <input type="checkbox" id="cart-v2-check-all-foot" checked>
                                        <span class="cart-v2-check-box"></span>
                                        <span class="cart-v2-check-label">Chọn tất cả ({{ $cartItems->count() }})</span>
                                    </label>
                                    <button type="button" class="cart-v2-link-danger" id="cart-v2-clear-all">
                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                        Xóa tất cả
                                    </button>
                                </div>
                                <div class="cart-v2-list-foot-right">
                                    <button type="submit" class="cart-v2-btn cart-v2-btn--ghost">
                                        <i class="fa fa-refresh" aria-hidden="true"></i>
                                        Cập nhật giỏ hàng
                                    </button>
                                </div>
                            </div>
                        </form>
                    </section>

                    {{-- ===== RIGHT: Order summary ===== --}}
                    <aside class="cart-v2-aside" aria-label="Tóm tắt đơn hàng">
                        <div class="cart-v2-card cart-v2-summary" data-cart-summary>
                            <h2 class="cart-v2-summary-title">Tóm tắt đơn hàng</h2>

                            <div class="cart-v2-summary-rows">
                                <div class="cart-v2-summary-row">
                                    <span>Tạm tính (<span data-summary-count>{{ $cartItems->count() }}</span> sản phẩm)</span>
                                    <strong data-summary-subtotal>{{ number_format($totals['subtotal'], 0, ',', '.') }}đ</strong>
                                </div>

                                @if ($totals['discount'] > 0)
                                    <div class="cart-v2-summary-row">
                                        <span>Giảm giá sản phẩm</span>
                                        <strong class="is-discount">-{{ number_format($totals['discount'], 0, ',', '.') }}đ</strong>
                                    </div>
                                @endif

                                <div class="cart-v2-coupon">
                                    <label class="cart-v2-coupon-label">Mã giảm giá</label>
                                    <form method="POST" action="{{ url('/check-coupon') }}" class="cart-v2-coupon-form">
                                        @csrf
                                        <input type="text" name="coupon" value="{{ old('coupon') }}"
                                            class="cart-v2-coupon-input" placeholder="Nhập mã giảm giá">
                                        <button type="submit" class="cart-v2-coupon-btn">Áp dụng</button>
                                    </form>
                                    @if ($hasCoupon)
                                        <div class="cart-v2-coupon-active">
                                            <span>Đang áp dụng: <strong>{{ $discountCode }}</strong></span>
                                            <form method="POST" action="{{ url('/unset-coupon') }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="cart-v2-coupon-remove">
                                                    <i class="fa fa-times" aria-hidden="true"></i>
                                                    Gỡ
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>

                                <div class="cart-v2-summary-row">
                                    <span>Phí vận chuyển</span>
                                    <strong class="{{ $hasFreeShipping ? 'is-free' : '' }}">
                                        {{ $hasFreeShipping ? 'Miễn phí' : number_format($totals['shipping'], 0, ',', '.') . 'đ' }}
                                    </strong>
                                </div>
                            </div>

                            <div class="cart-v2-summary-total">
                                <div class="cart-v2-summary-total-row">
                                    <span>Tổng cộng</span>
                                    <strong data-summary-total>{{ number_format($totals['total'], 0, ',', '.') }}đ</strong>
                                </div>
                                <p class="cart-v2-summary-vat">(Đã bao gồm VAT)</p>
                            </div>

                            <div class="cart-v2-summary-actions">
                                <a href="{{ $checkoutUrl }}" class="cart-v2-btn cart-v2-btn--checkout">
                                    <i class="fa fa-lock" aria-hidden="true"></i>
                                    Tiến hành thanh toán
                                </a>
                                <a href="{{ $checkoutUrl }}?quick=1" class="cart-v2-btn cart-v2-btn--quick">
                                    <i class="fa fa-bolt" aria-hidden="true"></i>
                                    Thanh toán nhanh
                                </a>
                            </div>

                            <div class="cart-v2-payments" aria-hidden="true">
                                <span class="cart-v2-pay cart-v2-pay--visa"><i class="fa fa-cc-visa"></i></span>
                                <span class="cart-v2-pay cart-v2-pay--mc"><i class="fa fa-cc-mastercard"></i></span>
                                <span class="cart-v2-pay cart-v2-pay--momo">momo</span>
                                <span class="cart-v2-pay cart-v2-pay--zalo">ZaloPay</span>
                            </div>

                            <div class="cart-v2-secure">
                                <span class="cart-v2-secure-icon">
                                    <i class="fa fa-shield" aria-hidden="true"></i>
                                </span>
                                <div class="cart-v2-secure-copy">
                                    <strong>Thanh toán an toàn, bảo mật</strong>
                                    <small>Thông tin của bạn được bảo mật tuyệt đối.</small>
                                </div>
                            </div>

                            @if (!$isLoggedIn)
                                <p class="cart-v2-summary-note">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    Đã có tài khoản?
                                    <a href="{{ url('/dang-nhap') . '?' . http_build_query(['redirect' => 'checkout', 'redirect_to' => url('/checkout')]) }}">Đăng nhập</a>
                                </p>
                            @endif
                        </div>
                    </aside>
                </div>
            @endif

            {{-- ===== Service features ===== --}}
            <section class="cart-v2-services" aria-label="Cam kết dịch vụ">
                <div class="cart-v2-service">
                    <div class="cart-v2-service-icon"><i class="fa fa-truck" aria-hidden="true"></i></div>
                    <div class="cart-v2-service-copy">
                        <strong>Miễn phí giao hàng</strong>
                        <small>Áp dụng cho đơn hàng công nghệ đủ điều kiện</small>
                    </div>
                </div>
                <div class="cart-v2-service">
                    <div class="cart-v2-service-icon"><i class="fa fa-refresh" aria-hidden="true"></i></div>
                    <div class="cart-v2-service-copy">
                        <strong>Đối trả hàng</strong>
                        <small>Hỗ trợ đổi trả trong 30 ngày nếu có lỗi từ NSX</small>
                    </div>
                </div>
                <div class="cart-v2-service">
                    <div class="cart-v2-service-icon"><i class="fa fa-shield" aria-hidden="true"></i></div>
                    <div class="cart-v2-service-copy">
                        <strong>Bảo hành chính hãng</strong>
                        <small>Cam kết sản phẩm chính hãng 100%</small>
                    </div>
                </div>
                <div class="cart-v2-service">
                    <div class="cart-v2-service-icon"><i class="fa fa-headphones" aria-hidden="true"></i></div>
                    <div class="cart-v2-service-copy">
                        <strong>Hỗ trợ tư vấn 24/7</strong>
                        <small>Đội ngũ chuyên viên sẵn sàng hỗ trợ</small>
                    </div>
                </div>
            </section>

            {{-- ===== Recommendations ===== --}}
            <section class="cart-v2-recommend" aria-label="Có thể bạn cũng cần">
                <div class="cart-v2-recommend-head">
                    <h2>Có thể bạn cũng cần</h2>
                    <a href="{{ url('/san-pham') }}" class="cart-v2-recommend-more">
                        Xem tất cả <i class="fa fa-angle-right" aria-hidden="true"></i>
                    </a>
                </div>
                <div id="shopw_cart_viewed_grid" class="cart-v2-recommend-grid"></div>
            </section>
        </div>
    </main>

    <script>
        (function () {
            'use strict';

            const formatVND = (value) => {
                const n = Math.max(0, Math.round(Number(value) || 0));
                return n.toLocaleString('vi-VN') + 'đ';
            };

            // ----- Quantity steppers -----
            document.querySelectorAll('[data-stepper]').forEach((stepper) => {
                const input = stepper.querySelector('.cart-v2-stepper-input');
                const max = parseInt(stepper.dataset.max, 10) || 1;

                const clamp = () => {
                    let v = parseInt(input.value, 10);
                    if (isNaN(v) || v < 1) v = 1;
                    if (v > max) v = max;
                    input.value = v;
                    updateRow(stepper.closest('[data-cart-row]'));
                };

                stepper.querySelectorAll('.cart-v2-stepper-btn').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const act = btn.dataset.act;
                        let v = parseInt(input.value, 10) || 1;
                        v = act === 'inc' ? v + 1 : v - 1;
                        if (v < 1) v = 1;
                        if (v > max) v = max;
                        input.value = v;
                        updateRow(stepper.closest('[data-cart-row]'));
                    });
                });

                input.addEventListener('change', clamp);
                input.addEventListener('blur', clamp);
            });

            const updateRow = (row) => {
                if (!row) return;
                const input = row.querySelector('.cart-v2-stepper-input');
                const display = row.querySelector('[data-line-total-display]');
                if (!input || !display) return;
                const price = parseFloat(input.dataset.unitPrice) || 0;
                const qty = parseInt(input.value, 10) || 0;
                const total = price * qty;
                row.dataset.lineTotal = total;
                display.textContent = formatVND(total);
                refreshSummary();
            };

            const refreshSummary = () => {
                const summary = document.querySelector('[data-cart-summary]');
                if (!summary) return;

                let subtotal = 0;
                let count = 0;
                document.querySelectorAll('[data-cart-row]').forEach((row) => {
                    const checked = row.querySelector('.cart-v2-row-check')?.checked;
                    if (checked) {
                        subtotal += parseFloat(row.dataset.lineTotal) || 0;
                        count += 1;
                    }
                });

                const discount = {{ (float) ($totals['discount'] ?? 0) }};
                const shipping = {{ (float) ($totals['shipping'] ?? 0) }};
                const total = Math.max(0, subtotal - discount + shipping);

                summary.querySelector('[data-summary-count]').textContent = count;
                summary.querySelector('[data-summary-subtotal]').textContent = formatVND(subtotal);
                summary.querySelector('[data-summary-total]').textContent = formatVND(total);
            };

            // ----- Select all -----
            const headCheck = document.getElementById('cart-v2-check-all');
            const footCheck = document.getElementById('cart-v2-check-all-foot');
            const rowChecks = document.querySelectorAll('.cart-v2-row-check');

            const setAll = (state) => {
                rowChecks.forEach((c) => { c.checked = state; });
                if (headCheck) headCheck.checked = state;
                if (footCheck) footCheck.checked = state;
                refreshSummary();
            };

            headCheck?.addEventListener('change', (e) => setAll(e.target.checked));
            footCheck?.addEventListener('change', (e) => setAll(e.target.checked));

            rowChecks.forEach((c) => {
                c.addEventListener('change', () => {
                    const allOn = Array.from(rowChecks).every((x) => x.checked);
                    if (headCheck) headCheck.checked = allOn;
                    if (footCheck) footCheck.checked = allOn;
                    refreshSummary();
                });
            });

            // ----- Remove single item -----
            const submitCartMutation = (url, method = 'POST') => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.style.display = 'none';

                const token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = '{{ csrf_token() }}';
                form.appendChild(token);

                if (method !== 'POST') {
                    const spoof = document.createElement('input');
                    spoof.type = 'hidden';
                    spoof.name = '_method';
                    spoof.value = method;
                    form.appendChild(spoof);
                }

                document.body.appendChild(form);
                form.submit();
            };

            document.querySelectorAll('[data-cart-remove]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) return;
                    const sid = btn.getAttribute('data-cart-remove');
                    submitCartMutation('{{ url("/del-product") }}/' + encodeURIComponent(sid), 'DELETE');
                });
            });

            // ----- Clear all -----
            document.getElementById('cart-v2-clear-all')?.addEventListener('click', () => {
                if (!confirm('Xóa toàn bộ sản phẩm trong giỏ hàng?')) return;
                submitCartMutation('{{ url("/del-all-product") }}');
            });

            refreshSummary();
        })();
    </script>
@endsection
