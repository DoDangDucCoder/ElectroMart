@extends('layouts.home-tech')
@section('body_class', 'accp-page')
@section('content')
<link rel="stylesheet" href="{{ asset('frontend/css/account-shared.css') }}">
<style>
.cord-item{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px;margin-bottom:12px;transition:box-shadow .15s}
.cord-item:hover{box-shadow:0 2px 8px rgba(0,0,0,.05)}
.cord-head{display:flex;align-items:center;gap:12px;margin-bottom:12px;flex-wrap:wrap}
.cord-code{font-size:15px;font-weight:800;color:#111827}
.cord-dates{font-size:11.5px;color:#9ca3af;margin-left:auto}
.cord-body{display:flex;gap:16px;align-items:flex-start}
@media(max-width:768px){.cord-body{flex-direction:column}}
.cord-reason{flex:1;min-width:0}
.cord-reason .label{font-size:11px;color:#6b7280;margin-bottom:4px}
.cord-reason .value{font-size:13px;color:#111827;font-weight:600}
.cord-products{display:flex;gap:10px;align-items:center;flex:1;min-width:0}
.cord-prod-img{width:60px;height:60px;border-radius:8px;border:1px solid #e5e7eb;overflow:hidden;flex-shrink:0}
.cord-prod-img img{width:100%;height:100%;object-fit:cover}
.cord-prod-info{min-width:0}
.cord-prod-name{font-size:12.5px;font-weight:600;color:#111827;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.cord-prod-meta{font-size:11px;color:#9ca3af;margin-top:2px}
.cord-summary{text-align:right;min-width:140px}
.cord-total-label{font-size:11px;color:#6b7280}
.cord-total{font-size:18px;font-weight:800;color:#dc2626}
.cord-refund-status{font-size:11px;margin-top:4px}
.cord-actions{display:flex;gap:6px;margin-top:8px;justify-content:flex-end;flex-wrap:wrap}
</style>

@php
    $cancelledOrders = $cancelledOrders ?? collect();
    $stats           = $stats ?? ['total' => 0, 'refunded' => 0, 'pending' => 0, 'total_value' => 0];

    $refundLabel = function ($order) {
        return match ($order->payment_status) {
            'refunded' => ['Đã hoàn tiền', 'green'],
            'paid'     => ['Chờ hoàn tiền', 'amber'],
            default    => ['Không áp dụng hoàn tiền', 'gray'],
        };
    };
@endphp

<div class="accp-wrap">
    <div class="accp-crumb"><a href="{{ url('/') }}">Trang chủ</a><span class="sep">›</span><a href="{{ route('customer.account') }}">Tài khoản</a><span class="sep">›</span><span class="current">Đơn hàng đã hủy</span></div>
    <div class="accp-page-title"><h1>Đơn hàng đã hủy</h1><p>Quản lý và xem lại các đơn hàng đã bị hủy của bạn</p></div>

    <div class="accp-grid">
        @include('pages.account.partials.sidebar', ['currentSection' => 'cancelled-orders'])

        <main>
            {{-- Stats --}}
            <div class="accp-stats">
                <div class="accp-stat"><div class="accp-stat-icon teal"><i class="fa fa-times-circle"></i></div><div><div class="num">{{ $stats['total'] }}</div><div class="lbl">Tổng đơn đã hủy</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon green"><i class="fa fa-check-circle"></i></div><div><div class="num">{{ $stats['refunded'] }}</div><div class="lbl">Đã hoàn tiền</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon amber"><i class="fa fa-clock-o"></i></div><div><div class="num">{{ $stats['pending'] }}</div><div class="lbl">Chờ xử lý hoàn tiền</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon red"><i class="fa fa-money"></i></div><div><div class="num" style="font-size:18px">{{ number_format((float) $stats['total_value'], 0, ',', '.') }}đ</div><div class="lbl">Tổng giá trị đơn bị hủy</div></div></div>
            </div>

            {{-- Toolbar --}}
            <div class="accp-toolbar">
                <div class="accp-search"><i class="fa fa-search"></i><input type="text" placeholder="Tìm theo mã đơn hàng hoặc tên sản phẩm..."></div>
                <select class="accp-select"><option>30 ngày qua</option></select>
                <select class="accp-select"><option>Lý do hủy</option></select>
                <button class="accp-filter-btn"><i class="fa fa-sliders"></i> Bộ lọc</button>
            </div>

            {{-- Tabs --}}
            <div class="accp-tabs">
                <button class="accp-tab active">Tất cả ({{ $stats['total'] }})</button>
                <button class="accp-tab">Đã hoàn tiền ({{ $stats['refunded'] }})</button>
                <button class="accp-tab">Chờ hoàn tiền ({{ $stats['pending'] }})</button>
            </div>

            {{-- Order list --}}
            @forelse ($cancelledOrders as $order)
            @php [$refundLabelText, $refundLabelClass] = $refundLabel($order); @endphp
            <div class="cord-item">
                <div class="cord-head">
                    <span class="cord-code">#{{ $order->code }}</span>
                    <span class="accp-badge red">Đã hủy</span>
                    <span class="cord-dates">
                        Đặt hàng: {{ optional($order->placed_at ?? $order->created_at)->format('d/m/Y - H:i') }}<br>
                        Hủy đơn: {{ optional($order->cancelled_at)->format('d/m/Y - H:i') ?: '—' }}
                    </span>
                </div>
                <div class="cord-body">
                    <div class="cord-reason">
                        <div class="label">Lý do hủy:</div>
                        <div class="value">{{ data_get($order->meta, 'cancel_reason', $order->note ?: 'Khách đổi ý') }}</div>
                    </div>
                    <div class="cord-products">
                        @foreach ($order->items->take(2) as $item)
                        @php $product = $item->product; $productImage = $product?->thumbnail ?? $product?->image ?? 'uploads/product/no-image.svg'; @endphp
                        <div class="cord-prod-img"><img src="{{ asset($productImage) }}" alt=""></div>
                        <div class="cord-prod-info">
                            <div class="cord-prod-name">{{ $product?->name ?? $item->name ?? 'Sản phẩm' }}</div>
                            <div class="cord-prod-meta">x{{ $item->quantity }} sản phẩm</div>
                        </div>
                        @endforeach
                        @if ($order->items->count() > 2)
                            <div style="font-size:11px;color:#9ca3af">+{{ $order->items->count() - 2 }} sản phẩm khác</div>
                        @endif
                    </div>
                    <div class="cord-summary">
                        <div class="cord-total-label">Tổng tiền</div>
                        <div class="cord-total">{{ number_format((float) $order->total, 0, ',', '.') }}đ</div>
                        <div class="cord-refund-status"><span class="accp-badge {{ $refundLabelClass }}">{{ $refundLabelText }}</span></div>
                        <div class="cord-actions">
                            <a href="{{ url('/view-history-order/' . $order->code) }}" class="accp-btn accp-btn--outline accp-btn--sm">Xem chi tiết</a>
                            <a href="{{ url('/san-pham') }}" class="accp-btn accp-btn--outline accp-btn--sm"><i class="fa fa-shopping-cart"></i> Mua lại</a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div style="background:#fff;border:1px dashed #cbd5e1;border-radius:10px;padding:32px;text-align:center;color:#6b7280">
                <i class="fa fa-times-circle" style="font-size:32px;color:#cbd5e1;display:block;margin-bottom:6px"></i>
                <div style="font-size:14px;font-weight:600;color:#374151">Bạn chưa có đơn hàng nào bị huỷ</div>
                <div style="font-size:12px;margin-top:2px">Mọi đơn huỷ sẽ xuất hiện tại đây.</div>
            </div>
            @endforelse
        </main>

        <aside class="accp-aside">
            <div class="accp-aside-card accp-contact-card">
                <h4>Bạn cần hỗ trợ?</h4>
                <p style="font-size:12px;color:#6b7280;margin:0 0 12px">Đội ngũ DangDuc luôn sẵn sàng hỗ trợ bạn về các đơn hàng đã hủy và hoàn tiền.</p>
                <div style="padding:6px 0;border-bottom:1px solid #f3f4f6"><span style="font-size:11px;color:#9ca3af;display:block">Hotline</span><span class="hotline">1900 1234</span></div>
                <div style="padding:8px 0;border-bottom:1px solid #f3f4f6"><span style="font-size:11px;color:#9ca3af;display:block">Email</span><a class="email" href="mailto:support@dangduc.vn">support@dangduc.vn</a><div class="email-sub">Phản hồi trong 24h</div></div>
                <div style="padding:8px 0"><span style="font-size:11px;color:#9ca3af;display:block">Chat trực tuyến</span><span style="font-size:13px;font-weight:600;color:#111827">Trò chuyện ngay</span></div>
                <button class="accp-chat-btn"><i class="fa fa-comments"></i> Liên hệ hỗ trợ</button>
            </div>
            <div class="accp-aside-card">
                <h4>Lưu ý về hoàn tiền</h4>
                <ul class="accp-aside-tips">
                    <li><i class="fa fa-info-circle"></i>Thời gian hoàn tiền dự kiến 3 - 7 ngày làm việc tùy thuộc vào phương thức thanh toán và ngân hàng.</li>
                </ul>
                <div style="margin-top:8px;text-align:right"><a href="#" style="font-size:12px;color:#0d9488;text-decoration:none;font-weight:600"><i class="fa fa-headphones"></i> Liên hệ hỗ trợ</a></div>
            </div>
        </aside>
    </div>

    {{-- Recommended products --}}
    @if (!empty($recommendedProducts) && $recommendedProducts->count() > 0)
    <div class="accp-recommend">
        <div class="accp-recommend-head"><h3>Có thể bạn cũng thích</h3><a href="{{ url('/san-pham') }}">Xem tất cả <i class="fa fa-arrow-right"></i></a></div>
        <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:14px">
            @foreach ($recommendedProducts->take(6) as $p)
                @php
                    $productCard = [
                        'id' => $p->id,
                        'name' => $p->product_name ?? $p->name ?? 'Sản phẩm',
                        'url' => url('/chi-tiet-san-pham/' . ($p->product_slug ?? $p->slug ?? $p->id)),
                        'image' => !empty($p->product_image) ? asset('uploads/product/' . $p->product_image) : asset('uploads/product/no-image.svg'),
                        'original_price' => $p->product_price ?? $p->original_price ?? 0,
                        'price' => $p->product_price_sale ?? $p->price ?? $p->product_price ?? 0,
                    ];
                @endphp
                <div>@include('partials.home.product-card', ['product' => $productCard, 'compact' => true])</div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="accp-support-bar">
        <div class="item"><span class="icon-wrap"><i class="fa fa-question-circle"></i></span><div><strong>Cần hỗ trợ về đơn hàng?</strong><span> Đội ngũ chăm sóc khách hàng luôn sẵn sàng hỗ trợ bạn.</span></div></div>
        <div class="item" style="margin-left:auto"><span class="icon-wrap"><i class="fa fa-phone"></i></span><div><strong>Hotline: 1900 1234</strong></div></div>
        <div class="item"><span class="icon-wrap"><i class="fa fa-envelope-o"></i></span><div><strong>Email: support@dangduc.vn</strong></div></div>
        <div class="item"><span class="icon-wrap"><i class="fa fa-comments-o"></i></span><div><strong>Chat với chúng tôi</strong></div></div>
    </div>
</div>
@endsection
