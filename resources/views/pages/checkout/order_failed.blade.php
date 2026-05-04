@extends('layouts.home-tech')

@section('body_class', 'g-checkout-redesign ors-page')

@php
    $order = $orderView ?? [];
    $items = $order['items'] ?? [];
    $totals = $order['totals'] ?? ['subtotal' => 0, 'shipping' => 0, 'discount' => 0, 'total' => 0];
    $shipping = $order['shipping'] ?? ['name' => '', 'phone' => '', 'address' => ''];
    $payment = $order['payment'] ?? ['provider_label' => 'Thanh toán thẻ quốc tế (Visa)'];
    $orderCode = $order['code'] ?? 'DD0000000000';
    $orderDate = $order['date'] ?? now()->format('d/m/Y H:i');
    $itemCount = count($items);
    $couponCode = $order['coupon_code'] ?? null;
    $failReason = $order['fail_reason'] ?? 'Giao dịch bị từ chối bởi ngân hàng (Mã lỗi: 05)';
    $orderStatus = $order['status'] ?? 'failed';
@endphp

@section('content')
{{-- Reuse success page styles --}}
<style>
body.ors-page { background: #f3f5f7; font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
body.ors-page #header .header-bottom { display: none; }

.ors-wrap { max-width: 1180px; margin: 0 auto; padding: 0 16px; }
.ors-crumb { font-size: 13px; color: #6b7280; padding: 14px 0; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.ors-crumb a { color: #6b7280; text-decoration: none; }
.ors-crumb a:hover { color: #0d9488; }
.ors-crumb .sep { color: #cbd5e1; }
.ors-crumb .current { color: #111827; font-weight: 500; }

.ors-stepper { display: flex; align-items: center; justify-content: center; gap: 0; padding: 24px 0 28px; max-width: 600px; margin: 0 auto; }
.ors-step { display: flex; flex-direction: column; align-items: center; gap: 8px; min-width: 100px; position: relative; z-index: 2; }
.ors-step-dot { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 600; border: 2px solid #e5e7eb; background: #fff; color: #9ca3af; }
.ors-step-label { font-size: 12px; color: #9ca3af; font-weight: 500; white-space: nowrap; }
.ors-step.done .ors-step-dot { background: #0d9488; border-color: #0d9488; color: #fff; }
.ors-step.done .ors-step-label { color: #0d9488; font-weight: 600; }
.ors-step.fail .ors-step-dot { background: #dc2626; border-color: #dc2626; color: #fff; }
.ors-step.fail .ors-step-label { color: #dc2626; font-weight: 600; }
.ors-step-line { flex: 1; height: 3px; background: #e5e7eb; min-width: 60px; margin-bottom: 26px; }
.ors-step-line.done { background: #0d9488; }
.ors-step-line.fail { background: #dc2626; }

.ors-grid { display: grid; grid-template-columns: 1fr 380px; gap: 24px; align-items: flex-start; margin-bottom: 32px; }
@media (max-width: 991px) { .ors-grid { grid-template-columns: 1fr; } }
.ors-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,.04); padding: 28px; }

.ors-header { display: flex; gap: 20px; align-items: flex-start; margin-bottom: 24px; }
.ors-icon-circle { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 36px; flex-shrink: 0; }
.ors-icon-circle.fail { background: #fef2f2; color: #dc2626; border: 2px solid #fecaca; }
.ors-header-text h1 { font-size: 24px; font-weight: 800; color: #111827; margin: 0 0 6px; }
.ors-header-text p { font-size: 14px; color: #6b7280; margin: 0; line-height: 1.5; }

.ors-details { margin-bottom: 24px; }
.ors-detail-row { display: flex; align-items: flex-start; gap: 10px; padding: 8px 0; font-size: 13.5px; }
.ors-detail-row + .ors-detail-row { border-top: 1px solid #f3f4f6; }
.ors-detail-row .icon { width: 20px; color: #9ca3af; flex-shrink: 0; text-align: center; margin-top: 1px; }
.ors-detail-row .label { color: #6b7280; min-width: 160px; flex-shrink: 0; }
.ors-detail-row .value { color: #111827; font-weight: 600; flex: 1; }
.ors-detail-row .value.danger { color: #dc2626; }

.ors-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; }
.ors-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 24px; font-size: 14px; font-weight: 600; border-radius: 8px; text-decoration: none; border: none; cursor: pointer; transition: all .2s; }
.ors-btn--danger { background: #dc2626; color: #fff; box-shadow: 0 2px 8px rgba(220,38,38,.25); }
.ors-btn--danger:hover { background: #b91c1c; color: #fff; text-decoration: none; }
.ors-btn--outline { background: #fff; color: #374151; border: 1.5px solid #d1d5db; }
.ors-btn--outline:hover { border-color: #9ca3af; background: #f9fafb; color: #111827; text-decoration: none; }
.ors-btn--link { background: transparent; color: #6b7280; padding: 12px 8px; }
.ors-btn--link:hover { color: #0d9488; text-decoration: none; }

.ors-contact { display: flex; gap: 32px; padding: 18px 0; border-top: 1px solid #e5e7eb; }
.ors-contact-item { display: flex; align-items: center; gap: 10px; font-size: 13px; }
.ors-contact-item .icon { width: 36px; height: 36px; border-radius: 50%; background: #f0fdfa; color: #0d9488; display: flex; align-items: center; justify-content: center; font-size: 14px; }
.ors-contact-item .text strong { display: block; color: #6b7280; font-weight: 500; font-size: 11px; text-transform: uppercase; letter-spacing: .3px; }
.ors-contact-item .text span, .ors-contact-item .text a { color: #0d9488; font-weight: 600; font-size: 13px; text-decoration: none; }

.ors-summary { position: sticky; top: 16px; }
.ors-summary-head { display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9; margin-bottom: 12px; }
.ors-summary-head h3 { font-size: 15px; font-weight: 700; color: #111827; margin: 0; text-transform: uppercase; letter-spacing: .5px; }
.ors-summary-head .count { font-size: 13px; color: #6b7280; font-weight: 500; }

.ors-product { display: flex; gap: 12px; align-items: center; padding: 10px 0; }
.ors-product + .ors-product { border-top: 1px solid #f3f4f6; }
.ors-product-thumb { width: 56px; height: 56px; border-radius: 8px; border: 1px solid #e5e7eb; overflow: hidden; flex-shrink: 0; background: #fff; }
.ors-product-thumb img { width: 100%; height: 100%; object-fit: cover; }
.ors-product-info { flex: 1; min-width: 0; }
.ors-product-name { font-size: 13px; font-weight: 600; color: #111827; line-height: 1.35; }
.ors-product-meta { font-size: 11.5px; color: #9ca3af; margin-top: 2px; }
.ors-product-price { font-size: 14px; font-weight: 700; color: #dc2626; white-space: nowrap; }

.ors-totals { padding-top: 14px; border-top: 1px solid #f1f5f9; margin-top: 8px; }
.ors-total-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 13.5px; color: #6b7280; }
.ors-total-row .discount { color: #16a34a; font-weight: 600; }
.ors-grand { display: flex; justify-content: space-between; align-items: center; padding: 14px 0 4px; border-top: 1px solid #e5e7eb; margin-top: 8px; }
.ors-grand .lbl { font-size: 15px; font-weight: 700; color: #111827; text-transform: uppercase; }
.ors-grand .val { font-size: 22px; font-weight: 800; color: #dc2626; }
.ors-vat { text-align: right; font-size: 11px; color: #9ca3af; margin-bottom: 14px; }

.ors-notice { border-radius: 8px; padding: 12px 16px; font-size: 13px; display: flex; align-items: center; gap: 10px; margin-top: 8px; }
.ors-notice.danger { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
.ors-notice.danger i { color: #dc2626; font-size: 18px; flex-shrink: 0; }

.ors-policy { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px 24px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 32px; }
@media (max-width: 768px) { .ors-policy { grid-template-columns: repeat(2, 1fr); } }
.ors-policy-item { display: flex; gap: 12px; align-items: flex-start; }
.ors-policy-icon { width: 40px; height: 40px; border-radius: 10px; background: #f0fdfa; color: #0d9488; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.ors-policy-title { font-size: 13px; font-weight: 700; color: #111827; }
.ors-policy-sub { font-size: 11.5px; color: #6b7280; line-height: 1.4; margin-top: 2px; }

.ors-recommend { margin-bottom: 40px; }
.ors-recommend-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.ors-recommend-head h2 { font-size: 18px; font-weight: 700; color: #111827; margin: 0; }
.ors-recommend-head a { font-size: 13px; color: #0d9488; text-decoration: none; font-weight: 500; }
.ors-recommend-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; }
@media (max-width: 991px) { .ors-recommend-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 600px) { .ors-recommend-grid { grid-template-columns: repeat(2, 1fr); } }
</style>

<div class="ors-wrap">
    <div class="ors-crumb">
        <a href="{{ url('/') }}">Trang chủ</a>
        <span class="sep">›</span>
        <a href="{{ url('/checkout') }}">Thanh toán</a>
        <span class="sep">›</span>
        <span class="current">Đặt hàng thất bại</span>
    </div>

    {{-- Stepper with fail state --}}
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
        <div class="ors-step-line fail"></div>
        <div class="ors-step fail">
            <div class="ors-step-dot"><i class="fa fa-times"></i></div>
            <div class="ors-step-label">Hoàn tất</div>
        </div>
    </div>

    <div class="ors-grid">
        <div>
            <div class="ors-card">
                <div class="ors-header">
                    <div class="ors-icon-circle fail">
                        <i class="fa fa-times"></i>
                    </div>
                    <div class="ors-header-text">
                        <h1>ĐẶT HÀNG THẤT BẠI</h1>
                        <p>Rất tiếc, đơn hàng của bạn chưa được hoàn tất.<br>Vui lòng kiểm tra lại thông tin thanh toán hoặc thử lại sau.</p>
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
                        <span class="label">Thời gian đặt</span>
                        <span class="value">{{ $orderDate }}</span>
                    </div>
                    <div class="ors-detail-row">
                        <span class="icon"><i class="fa fa-credit-card"></i></span>
                        <span class="label">Phương thức thanh toán</span>
                        <span class="value">{{ $payment['provider_label'] }}</span>
                    </div>
                    <div class="ors-detail-row">
                        <span class="icon"><i class="fa fa-exclamation-circle"></i></span>
                        <span class="label">Trạng thái</span>
                        <span class="value danger">● Thanh toán thất bại</span>
                    </div>
                    <div class="ors-detail-row">
                        <span class="icon"><i class="fa fa-info-circle"></i></span>
                        <span class="label">Lý do thất bại</span>
                        <span class="value danger">{{ $failReason }}</span>
                    </div>
                </div>

                <div class="ors-actions">
                    <a href="{{ url('/checkout') }}" class="ors-btn ors-btn--danger">
                        <i class="fa fa-refresh"></i> Thử thanh toán lại
                    </a>
                    <a href="{{ url('/checkout') }}" class="ors-btn ors-btn--outline">
                        <i class="fa fa-credit-card"></i> Chọn phương thức khác
                    </a>
                    <a href="{{ url('/gio-hang') }}" class="ors-btn ors-btn--link">
                        Quay lại giỏ hàng <i class="fa fa-arrow-right"></i>
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

        <aside>
            <div class="ors-card ors-summary">
                <div class="ors-summary-head">
                    <h3>ĐƠN HÀNG CỦA BẠN</h3>
                    <span class="count">{{ $itemCount }} sản phẩm</span>
                </div>

                @foreach ($items as $item)
                    @php $image = !empty($item['image']) ? asset($item['image']) : asset('uploads/product/no-image.svg'); @endphp
                    <div class="ors-product">
                        <div class="ors-product-thumb"><img src="{{ $image }}" alt="{{ $item['name'] }}"></div>
                        <div class="ors-product-info">
                            <div class="ors-product-name">{{ $item['name'] }}</div>
                            <div class="ors-product-meta">{{ $item['attributes'] ?? '' }} · x{{ $item['quantity'] }}</div>
                        </div>
                        <div class="ors-product-price">{{ number_format($item['subtotal'], 0, ',', '.') }}đ</div>
                    </div>
                @endforeach

                <div class="ors-totals">
                    <div class="ors-total-row"><span>Tạm tính ({{ $itemCount }} sản phẩm)</span><span>{{ number_format($totals['subtotal'], 0, ',', '.') }}đ</span></div>
                    <div class="ors-total-row"><span>Phí vận chuyển</span><span>{{ number_format($totals['shipping'], 0, ',', '.') }}đ</span></div>
                    @if ($totals['discount'] > 0)
                        <div class="ors-total-row"><span>Giảm giá @if($couponCode)(Mã: {{ $couponCode }})@endif</span><span class="discount">-{{ number_format($totals['discount'], 0, ',', '.') }}đ</span></div>
                    @endif
                </div>

                <div class="ors-grand">
                    <span class="lbl">TỔNG CỘNG</span>
                    <span class="val">{{ number_format($totals['total'], 0, ',', '.') }}đ</span>
                </div>
                <div class="ors-vat">(Đã bao gồm VAT)</div>

                <div class="ors-notice danger">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span>Khoản thanh toán chưa được ghi nhận.<br>Nếu tài khoản đã bị trừ tiền, vui lòng liên hệ hỗ trợ.</span>
                </div>
            </div>
        </aside>
    </div>

    <div class="ors-policy">
        <div class="ors-policy-item"><span class="ors-policy-icon"><i class="fa fa-truck"></i></span><div><div class="ors-policy-title">Miễn phí giao hàng</div><div class="ors-policy-sub">Áp dụng cho đơn hàng công nghệ đủ điều kiện</div></div></div>
        <div class="ors-policy-item"><span class="ors-policy-icon"><i class="fa fa-refresh"></i></span><div><div class="ors-policy-title">Đổi trả hàng</div><div class="ors-policy-sub">Hỗ trợ đổi trả trong 30 ngày nếu có lỗi do NSX</div></div></div>
        <div class="ors-policy-item"><span class="ors-policy-icon"><i class="fa fa-shield"></i></span><div><div class="ors-policy-title">Bảo hành chính hãng</div><div class="ors-policy-sub">Cam kết sản phẩm chính hãng 100%</div></div></div>
        <div class="ors-policy-item"><span class="ors-policy-icon"><i class="fa fa-headphones"></i></span><div><div class="ors-policy-title">Hỗ trợ tư vấn 24/7</div><div class="ors-policy-sub">Đội ngũ chuyên viên sẵn sàng hỗ trợ</div></div></div>
    </div>

    @if (!empty($recommendedProducts) && $recommendedProducts->count() > 0)
    <div class="ors-recommend">
        <div class="ors-recommend-head">
            <h2>Có thể bạn cũng thích</h2>
            <a href="{{ url('/san-pham') }}">Xem tất cả <i class="fa fa-arrow-right"></i></a>
        </div>
        <div class="ors-recommend-grid">
            @foreach ($recommendedProducts->take(5) as $product)
                <div>@include('partials.home.product-card', ['product' => $product, 'compact' => true])</div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
