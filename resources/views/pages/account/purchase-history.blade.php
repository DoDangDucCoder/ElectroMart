@extends('layouts.home-tech')
@section('body_class','accp-page')
@section('content')
<div class="accp-wrap">
    @include('partials.storefront.breadcrumb', ['class' => 'accp-crumb', 'separator' => '›', 'items' => [['label' => 'Tài khoản', 'url' => url('/account')], ['label' => 'Lịch sử mua hàng', 'current' => true]]])
    <div class="accp-grid">
        @include('pages.account.partials.sidebar', ['currentSection' => 'history', 'customer' => $customer])
        <main>
            <div class="accp-page-title"><h1>Lịch sử mua hàng</h1><p>Xem lại tất cả đơn hàng đã hoàn thành</p></div>

            <div class="accp-stats">
                <div class="accp-stat"><div class="accp-stat-icon teal"><i class="fa fa-shopping-cart"></i></div><div><div class="num">{{ $stats['total_orders'] }}</div><div class="lbl">Tổng đơn đã mua</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon blue"><i class="fa fa-money"></i></div><div><div class="num" style="font-size:18px">{{ $stats['total_spent'] > 0 ? number_format($stats['total_spent'] / 1000000, 1) . 'tr' : '0đ' }}</div><div class="lbl">Tổng chi tiêu</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon green"><i class="fa fa-calendar"></i></div><div><div class="num">{{ $stats['this_month'] }}</div><div class="lbl">Đơn tháng này</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon star"><i class="fa fa-star"></i></div><div><div class="num">{{ number_format($stats['loyalty_points']) }}</div><div class="lbl">Điểm tích lũy</div></div></div>
            </div>

            <div class="accp-toolbar">
                <div class="accp-search"><i class="fa fa-search"></i><input type="text" placeholder="Tìm theo mã đơn, sản phẩm..."></div>
                <input type="text" class="accp-select" style="width:200px" placeholder="Chọn khoảng ngày">
                <select class="accp-select"><option>Mới nhất</option><option>Cũ nhất</option><option>Giá cao → thấp</option></select>
            </div>

            @forelse($orders as $order)
            @php
                $orderCode = $order->code;
            @endphp
            <div class="accp-card" style="padding:18px">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:8px">
                    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
                        <span style="font-size:13px;font-weight:700;color:#111827">#{{ $orderCode }}</span>
                        <span style="font-size:12px;color:#6b7280"><i class="fa fa-clock-o"></i> {{ ($order->completed_at ?? $order->created_at)?->format('d/m/Y') }}</span>
                    </div>
                    <span class="accp-badge green"><i class="fa fa-check-circle"></i> Hoàn thành</span>
                </div>
                @foreach($order->items->take(2) as $item)
                <div style="display:flex;align-items:center;gap:14px;padding:12px 0;border-top:1px solid #f3f4f6">
                    <div style="width:64px;height:64px;background:#f8fafc;border-radius:8px;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
                        @if($item->product && $item->product->product_image)
                        <img src="{{ \App\Support\AssetUrl::productVariant($item->product->product_image, 'thumb') }}" alt="" style="width:100%;height:100%;object-fit:cover">
                        @else
                        <i class="fa fa-laptop" style="font-size:28px;color:#0d9488;opacity:.5"></i>
                        @endif
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:13.5px;font-weight:600;color:#111827">{{ $item->product_name ?? $item->product?->name ?? 'Sản phẩm' }}</div>
                        <div style="font-size:12px;color:#6b7280;margin-top:2px">x{{ $item->quantity }}</div>
                    </div>
                    <div style="text-align:right">
                        <div style="font-size:15px;font-weight:700;color:#0d9488">{{ number_format($item->price) }}đ</div>
                    </div>
                </div>
                @endforeach
                @if($order->items->count() > 2)
                <div style="font-size:12px;color:#6b7280;padding:4px 0">...và {{ $order->items->count() - 2 }} sản phẩm khác</div>
                @endif
                <div style="display:flex;justify-content:space-between;align-items:center;border-top:1px solid #f3f4f6;padding-top:12px;margin-top:4px">
                    <div style="font-size:13px;color:#6b7280">Tổng: <strong style="color:#111827;font-size:15px">{{ number_format($order->total) }}đ</strong></div>
                    <div style="display:flex;gap:8px">
                        <a href="#" class="accp-btn accp-btn--primary accp-btn--sm"><i class="fa fa-refresh"></i> Mua lại</a>
                        <a href="#" class="accp-btn accp-btn--outline accp-btn--sm"><i class="fa fa-star"></i> Đánh giá</a>
                        <a href="{{ route('customer.account.order-tracking', ['orderCode' => $orderCode]) }}" class="accp-btn accp-btn--outline accp-btn--sm">Chi tiết</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="accp-card" style="padding:40px;text-align:center">
                <i class="fa fa-history" style="font-size:48px;color:#d1d5db;margin-bottom:12px"></i>
                <div style="font-size:15px;font-weight:600;color:#6b7280;margin-bottom:8px">Chưa có đơn hàng hoàn thành nào</div>
                <a href="{{ url('/san-pham') }}" class="accp-btn accp-btn--primary">Bắt đầu mua sắm</a>
            </div>
            @endforelse

            @if($orders->hasPages())
            <div style="display:flex;justify-content:center;margin-top:20px">
                {{ $orders->links('vendor.pagination.shop-pagination') }}
            </div>
            @endif
        </main>

        <aside class="accp-aside">
            <div class="accp-aside-card accp-contact-card">
                <h4><i class="fa fa-headphones" style="color:#0d9488;margin-right:6px"></i>Hỗ trợ khách hàng</h4>
                <div class="hotline">1900 1234</div><div class="hotline-sub">8:00 - 21:00 (T2 - CN)</div>
                <div style="margin-top:10px"><a href="mailto:support@dangduc.vn" class="email">support@dangduc.vn</a><div class="email-sub">Phản hồi trong 30 phút</div></div>
                <button class="accp-chat-btn"><i class="fa fa-comment-o"></i> Chat với chúng tôi</button>
            </div>
            <div class="accp-aside-card">
                <h4><i class="fa fa-lightbulb-o" style="color:#0d9488;margin-right:6px"></i>Mẹo mua sắm</h4>
                <ul class="accp-aside-tips">
                    <li><i class="fa fa-check"></i> Đánh giá sản phẩm để nhận điểm tích lũy.</li>
                    <li><i class="fa fa-check"></i> Theo dõi giá để mua đúng thời điểm.</li>
                    <li><i class="fa fa-check"></i> Sử dụng mã giảm giá khi thanh toán.</li>
                </ul>
            </div>
        </aside>
    </div>
</div>
@endsection
