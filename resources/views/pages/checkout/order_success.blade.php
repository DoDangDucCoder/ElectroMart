@extends('layouts.home-tech')

@section('body_class', 'g-checkout-redesign ors-page')

@php
    $order = $orderView ?? [];
    $items = $order['items'] ?? [];
    $totals = $order['totals'] ?? ['subtotal' => 0, 'shipping' => 0, 'discount' => 0, 'total' => 0];
    $shipping = $order['shipping'] ?? ['name' => '', 'phone' => '', 'address' => ''];
    $payment = $order['payment'] ?? ['provider_label' => 'COD'];
    $orderCode = $order['code'] ?? 'DD0000000000';
    $orderDate = $order['date'] ?? now()->format('d/m/Y H:i');
    $itemCount = count($items);
    $couponCode = $order['coupon_code'] ?? null;
    $estimatedDelivery = $order['estimated_delivery'] ?? now()->addDays(3)->format('d/m/Y') . ' - ' . now()->addDays(5)->format('d/m/Y');
    $visibleRecommendedProducts = collect($recommendedProducts ?? []);
    $visibleRecommendedProducts = $visibleRecommendedProducts
        ->take($visibleRecommendedProducts->count() >= 8 ? 8 : min(4, $visibleRecommendedProducts->count()));
@endphp

@section('content')
<style>
/* ====== ORDER RESULT PAGE ====== */
body.ors-page { background: #f3f5f7; font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
body.ors-page #header .header-bottom { display: none; }

.ors-wrap { max-width: 1180px; margin: 0 auto; padding: 0 16px; }

/* Breadcrumb */
.ors-crumb { font-size: 13px; color: #6b7280; padding: 14px 0; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.ors-crumb a { color: #6b7280; text-decoration: none; }
.ors-crumb a:hover { color: #0d9488; }
.ors-crumb .sep { color: #cbd5e1; }
.ors-crumb .current { color: #111827; font-weight: 500; }

/* Stepper */
.ors-stepper { display: flex; align-items: center; justify-content: center; gap: 0; padding: 24px 0 28px; max-width: 600px; margin: 0 auto; }
.ors-step { display: flex; flex-direction: column; align-items: center; gap: 8px; min-width: 100px; position: relative; z-index: 2; }
.ors-step-dot { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 600; border: 2px solid #e5e7eb; background: #fff; color: #9ca3af; transition: all .3s; }
.ors-step-dot i { font-size: 16px; }
.ors-step-label { font-size: 12px; color: #9ca3af; font-weight: 500; white-space: nowrap; }
.ors-step.done .ors-step-dot { background: #0d9488; border-color: #0d9488; color: #fff; }
.ors-step.done .ors-step-label { color: #0d9488; font-weight: 600; }
.ors-step.fail .ors-step-dot { background: #dc2626; border-color: #dc2626; color: #fff; }
.ors-step.fail .ors-step-label { color: #dc2626; font-weight: 600; }
.ors-step-line { flex: 1; height: 3px; background: #e5e7eb; min-width: 60px; margin-bottom: 26px; }
.ors-step-line.done { background: #0d9488; }
.ors-step-line.fail { background: #dc2626; }

/* Main grid */
.ors-grid { display: grid; grid-template-columns: 1fr 380px; gap: 24px; align-items: flex-start; margin-bottom: 32px; }
@media (max-width: 991px) { .ors-grid { grid-template-columns: 1fr; } }

/* Card */
.ors-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,.04); padding: 28px; }

/* Success header */
.ors-header { display: flex; gap: 20px; align-items: flex-start; margin-bottom: 24px; }
.ors-icon { width: 80px; height: 80px; flex-shrink: 0; position: relative; }
.ors-icon-circle { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 36px; }
.ors-icon-circle.success { background: #ecfdf5; color: #16a34a; border: 2px solid #bbf7d0; }
.ors-icon-circle.fail { background: #fef2f2; color: #dc2626; border: 2px solid #fecaca; }
.ors-header-text h1 { font-size: 24px; font-weight: 800; color: #111827; margin: 0 0 6px; }
.ors-header-text p { font-size: 14px; color: #6b7280; margin: 0; line-height: 1.5; }

/* Order details grid */
.ors-details { margin-bottom: 24px; }
.ors-detail-row { display: flex; align-items: flex-start; gap: 10px; padding: 8px 0; font-size: 13.5px; }
.ors-detail-row + .ors-detail-row { border-top: 1px solid #f3f4f6; }
.ors-detail-row .icon { width: 20px; color: #9ca3af; flex-shrink: 0; text-align: center; margin-top: 1px; }
.ors-detail-row .label { color: #6b7280; min-width: 160px; flex-shrink: 0; }
.ors-detail-row .value { color: #111827; font-weight: 600; flex: 1; }

/* Action buttons */
.ors-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; }
.ors-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 24px; font-size: 14px; font-weight: 600; border-radius: 8px; text-decoration: none; border: none; cursor: pointer; transition: all .2s; }
.ors-btn--primary { background: #0d9488; color: #fff; box-shadow: 0 2px 8px rgba(13,148,136,.25); }
.ors-btn--primary:hover { background: #0f766e; color: #fff; text-decoration: none; }
.ors-btn--danger { background: #dc2626; color: #fff; box-shadow: 0 2px 8px rgba(220,38,38,.25); }
.ors-btn--danger:hover { background: #b91c1c; color: #fff; text-decoration: none; }
.ors-btn--outline { background: #fff; color: #374151; border: 1.5px solid #d1d5db; }
.ors-btn--outline:hover { border-color: #9ca3af; background: #f9fafb; color: #111827; text-decoration: none; }
.ors-btn--link { background: transparent; color: #6b7280; padding: 12px 8px; }
.ors-btn--link:hover { color: #0d9488; text-decoration: none; }

/* Contact strip */
.ors-contact { display: flex; gap: 32px; padding: 18px 0; border-top: 1px solid #e5e7eb; }
.ors-contact-item { display: flex; align-items: center; gap: 10px; font-size: 13px; }
.ors-contact-item .icon { width: 36px; height: 36px; border-radius: 50%; background: #f0fdfa; color: #0d9488; display: flex; align-items: center; justify-content: center; font-size: 14px; }
.ors-contact-item .text strong { display: block; color: #6b7280; font-weight: 500; font-size: 11px; text-transform: uppercase; letter-spacing: .3px; }
.ors-contact-item .text span { color: #0d9488; font-weight: 600; font-size: 13px; }
.ors-contact-item .text a { color: #0d9488; font-weight: 600; text-decoration: none; }

/* Right sidebar - summary */
.ors-summary { position: sticky; top: 16px; }
.ors-summary-head { display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9; margin-bottom: 12px; }
.ors-summary-head h3 { font-size: 15px; font-weight: 700; color: #111827; margin: 0; text-transform: uppercase; letter-spacing: .5px; }
.ors-summary-head .count { font-size: 13px; color: #6b7280; font-weight: 500; }

/* Product items in sidebar */
.ors-product { display: flex; gap: 12px; align-items: center; padding: 10px 0; }
.ors-product + .ors-product { border-top: 1px solid #f3f4f6; }
.ors-product-thumb { width: 56px; height: 56px; border-radius: 8px; border: 1px solid #e5e7eb; overflow: hidden; flex-shrink: 0; background: #fff; }
.ors-product-thumb img { width: 100%; height: 100%; object-fit: cover; }
.ors-product-info { flex: 1; min-width: 0; }
.ors-product-name { font-size: 13px; font-weight: 600; color: #111827; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.ors-product-meta { font-size: 11.5px; color: #9ca3af; margin-top: 2px; }
.ors-product-price { font-size: 14px; font-weight: 700; color: #dc2626; white-space: nowrap; }

/* Totals */
.ors-totals { padding-top: 14px; border-top: 1px solid #f1f5f9; margin-top: 8px; }
.ors-total-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 13.5px; color: #6b7280; }
.ors-total-row .discount { color: #16a34a; font-weight: 600; }
.ors-grand { display: flex; justify-content: space-between; align-items: center; padding: 14px 0 4px; border-top: 1px solid #e5e7eb; margin-top: 8px; }
.ors-grand .lbl { font-size: 15px; font-weight: 700; color: #111827; text-transform: uppercase; letter-spacing: .5px; }
.ors-grand .val { font-size: 22px; font-weight: 800; color: #dc2626; }
.ors-vat { text-align: right; font-size: 11px; color: #9ca3af; margin-bottom: 14px; }

/* Notice pill */
.ors-notice { border-radius: 8px; padding: 12px 16px; font-size: 13px; display: flex; align-items: center; gap: 10px; margin-top: 8px; }
.ors-notice.success { background: #ecfdf5; border: 1px solid #bbf7d0; color: #065f46; }
.ors-notice.danger { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
.ors-notice i { font-size: 18px; flex-shrink: 0; }
.ors-notice.success i { color: #16a34a; }
.ors-notice.danger i { color: #dc2626; }

/* Policy strip */
.ors-policy { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px 24px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 32px; }
@media (max-width: 768px) { .ors-policy { grid-template-columns: repeat(2, 1fr); } }
.ors-policy-item { display: flex; gap: 12px; align-items: flex-start; }
.ors-policy-icon { width: 40px; height: 40px; border-radius: 10px; background: #f0fdfa; color: #0d9488; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.ors-policy-title { font-size: 13px; font-weight: 700; color: #111827; }
.ors-policy-sub { font-size: 11.5px; color: #6b7280; line-height: 1.4; margin-top: 2px; }

/* Recommended products */
.ors-recommend { margin-bottom: 40px; }
.ors-recommend-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.ors-recommend-head h2 { font-size: 18px; font-weight: 700; color: #111827; margin: 0; }
.ors-recommend-head a { font-size: 13px; color: #0d9488; text-decoration: none; font-weight: 500; }
.ors-recommend-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
@media (max-width: 991px) { .ors-recommend-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px) { .ors-recommend-grid { grid-template-columns: repeat(2, 1fr); } }
</style>

<div class="ors-wrap">
    @include('partials.storefront.breadcrumb', [
        'class' => 'ors-crumb',
        'separator' => '›',
        'items' => [
            [
                'label' => 'Thanh toán',
                'url' => url('/checkout'),
            ],
            [
                'label' => 'Đặt hàng thành công',
                'current' => true,
            ],
        ],
    ])

    {{-- Stepper --}}
    <div class="ors-stepper">
        <div class="ors-step done">
            <div class="ors-step-dot"><i class="fa fa-shopping-cart"></i></div>
            <div class="ors-step-label">Giỏ hàng</div>
        </div>
        <div class="ors-step-line done"></div>
        <div class="ors-step done">
            <div class="ors-step-dot"><i class="fa fa-check"></i></div>
            <div class="ors-step-label">Thanh toán</div>
        </div>
        <div class="ors-step-line done"></div>
        <div class="ors-step done">
            <div class="ors-step-dot"><i class="fa fa-check"></i></div>
            <div class="ors-step-label">Hoàn tất</div>
        </div>
    </div>

    {{-- Main grid --}}
    <div class="ors-grid">
        {{-- LEFT --}}
        <div>
            <div class="ors-card">
                <div class="ors-header">
                    <div class="ors-icon">
                        <div class="ors-icon-circle success">
                            <i class="fa fa-check"></i>
                        </div>
                    </div>
                    <div class="ors-header-text">
                        <h1>ĐẶT HÀNG THÀNH CÔNG</h1>
                        <p>Cảm ơn bạn đã mua sắm tại DangDuc.<br>Đơn hàng của bạn đã được tiếp nhận và đang được xử lý.</p>
                    </div>
                </div>

                <div class="ors-details">
                    <div class="ors-detail-row">
                        <span class="icon"><i class="fa fa-file-text-o"></i></span>
                        <span class="label">Mã đơn hàng</span>
                        <span class="value">{{ $orderCode }}</span>
                    </div>
                    <div class="ors-detail-row">
                        <span class="icon"><i class="fa fa-clock-o"></i></span>
                        <span class="label">Thời gian đặt hàng</span>
                        <span class="value">{{ $orderDate }}</span>
                    </div>
                    <div class="ors-detail-row">
                        <span class="icon"><i class="fa fa-credit-card"></i></span>
                        <span class="label">Phương thức thanh toán</span>
                        <span class="value">{{ $payment['provider_label'] }}</span>
                    </div>
                    <div class="ors-detail-row">
                        <span class="icon"><i class="fa fa-map-marker"></i></span>
                        <span class="label">Địa chỉ giao hàng</span>
                        <span class="value">{{ $shipping['name'] }}, {{ $shipping['address'] }}</span>
                    </div>
                    <div class="ors-detail-row">
                        <span class="icon"><i class="fa fa-truck"></i></span>
                        <span class="label">Thời gian giao dự kiến</span>
                        <span class="value">{{ $estimatedDelivery }}</span>
                    </div>
                </div>

                <div class="ors-actions">
                    <a href="{{ url('/kiem-tra-don-hang?code=' . $orderCode) }}" class="ors-btn ors-btn--primary">
                        <i class="fa fa-truck"></i> Theo dõi đơn hàng
                    </a>
                    <a href="{{ Session::get('customer_id') ? url('/account/order-tracking/' . $orderCode) : url('/view-history-order/' . $orderCode) }}" class="ors-btn ors-btn--outline">
                        <i class="fa fa-file-text-o"></i> Xem chi tiết đơn hàng
                    </a>
                    <a href="{{ url('/san-pham') }}" class="ors-btn ors-btn--link">
                        Tiếp tục mua sắm <i class="fa fa-arrow-right"></i>
                    </a>
                </div>

                <div class="ors-contact">
                    <div class="ors-contact-item">
                        <span class="icon"><i class="fa fa-phone"></i></span>
                        <div class="text">
                            <strong>Hotline hỗ trợ 24/7</strong>
                            <span>1900 1234</span>
                        </div>
                    </div>
                    <div class="ors-contact-item">
                        <span class="icon"><i class="fa fa-envelope"></i></span>
                        <div class="text">
                            <strong>Email hỗ trợ</strong>
                            <a href="mailto:support@dangduc.vn">support@dangduc.vn</a>
                        </div>
                    </div>
                    <div class="ors-contact-item">
                        <span class="icon"><i class="fa fa-comments"></i></span>
                        <div class="text">
                            <strong>Chat với chúng tôi</strong>
                            <a href="#">Trò chuyện ngay</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT - ORDER SUMMARY --}}
        <aside>
            <div class="ors-card ors-summary">
                <div class="ors-summary-head">
                    <h3>ĐƠN HÀNG CỦA BẠN</h3>
                    <span class="count">{{ $itemCount }} sản phẩm</span>
                </div>

                @foreach ($items as $item)
                    @php
                        $image = \App\Support\AssetUrl::productVariant($item['image'] ?? null, 'thumb');
                    @endphp
                    <div class="ors-product">
                        <div class="ors-product-thumb">
                            <img src="{{ $image }}" alt="{{ $item['name'] }}">
                        </div>
                        <div class="ors-product-info">
                            <div class="ors-product-name">{{ $item['name'] }}</div>
                            <div class="ors-product-meta">{{ $item['attributes'] ?? '' }} · x{{ $item['quantity'] }}</div>
                        </div>
                        <div class="ors-product-price">{{ number_format($item['subtotal'], 0, ',', '.') }}đ</div>
                    </div>
                @endforeach

                <div class="ors-totals">
                    <div class="ors-total-row">
                        <span>Tạm tính ({{ $itemCount }} sản phẩm)</span>
                        <span>{{ number_format($totals['subtotal'], 0, ',', '.') }}đ</span>
                    </div>
                    <div class="ors-total-row">
                        <span>Phí vận chuyển</span>
                        <span>{{ number_format($totals['shipping'], 0, ',', '.') }}đ</span>
                    </div>
                    @if ($totals['discount'] > 0)
                        <div class="ors-total-row">
                            <span>Giảm giá @if($couponCode)(Mã: {{ $couponCode }})@endif</span>
                            <span class="discount">-{{ number_format($totals['discount'], 0, ',', '.') }}đ</span>
                        </div>
                    @endif
                </div>

                <div class="ors-grand">
                    <span class="lbl">TỔNG CỘNG</span>
                    <span class="val">{{ number_format($totals['total'], 0, ',', '.') }}đ</span>
                </div>
                <div class="ors-vat">(Đã bao gồm VAT)</div>

                <div class="ors-notice success">
                    <i class="fa fa-check-circle"></i>
                    <span>Thông tin đơn hàng đã được gửi đến email của bạn.</span>
                </div>
            </div>
        </aside>
    </div>

    {{-- Policy strip --}}
    <div class="ors-policy">
        <div class="ors-policy-item">
            <span class="ors-policy-icon"><i class="fa fa-truck"></i></span>
            <div>
                <div class="ors-policy-title">Miễn phí giao hàng</div>
                <div class="ors-policy-sub">Áp dụng cho đơn hàng công nghệ đủ điều kiện trong nội thành.</div>
            </div>
        </div>
        <div class="ors-policy-item">
            <span class="ors-policy-icon"><i class="fa fa-refresh"></i></span>
            <div>
                <div class="ors-policy-title">Đổi trả hàng</div>
                <div class="ors-policy-sub">Hỗ trợ đổi trả trong 30 ngày nếu có lỗi do NSX</div>
            </div>
        </div>
        <div class="ors-policy-item">
            <span class="ors-policy-icon"><i class="fa fa-shield"></i></span>
            <div>
                <div class="ors-policy-title">Bảo hành chính hãng</div>
                <div class="ors-policy-sub">Cam kết sản phẩm chính hãng 100%</div>
            </div>
        </div>
        <div class="ors-policy-item">
            <span class="ors-policy-icon"><i class="fa fa-headphones"></i></span>
            <div>
                <div class="ors-policy-title">Hỗ trợ tư vấn 24/7</div>
                <div class="ors-policy-sub">Đội ngũ chuyên viên sẵn sàng hỗ trợ</div>
            </div>
        </div>
    </div>

    {{-- Recommended products --}}
    @if ($visibleRecommendedProducts->isNotEmpty())
    <div class="ors-recommend">
        <div class="ors-recommend-head">
            <h2>Có thể bạn cũng thích</h2>
            <a href="{{ url('/san-pham') }}">Xem tất cả <i class="fa fa-arrow-right"></i></a>
        </div>
        <div class="ors-recommend-grid">
            @foreach ($visibleRecommendedProducts as $product)
                <div>
                    @include('partials.home.product-card', ['product' => $product, 'compact' => true])
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
