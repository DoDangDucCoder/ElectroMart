@extends('layouts.home-tech')
@section('body_class','accp-page')
@section('content')
<div class="accp-wrap">
    @include('partials.storefront.breadcrumb', ['class' => 'accp-crumb', 'separator' => '›', 'items' => [['label' => 'Tài khoản', 'url' => url('/account')], ['label' => 'Đơn hàng của tôi', 'current' => true]]])
    <div class="accp-grid">
        @include('pages.account.partials.sidebar', ['currentSection' => 'orders', 'customer' => $customer])
        <main>
            <div class="accp-page-title"><h1>Quản lý đơn hàng</h1><p>Theo dõi tất cả đơn hàng của bạn</p></div>
            <div class="accp-stats" style="grid-template-columns:repeat(5,1fr)">
                <div class="accp-stat"><div class="accp-stat-icon blue"><i class="fa fa-list"></i></div><div><div class="num">{{ $stats['total'] }}</div><div class="lbl">Tất cả</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon amber"><i class="fa fa-clock-o"></i></div><div><div class="num">{{ $stats['pending'] }}</div><div class="lbl">Chờ xác nhận</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon teal"><i class="fa fa-truck"></i></div><div><div class="num">{{ $stats['shipping'] }}</div><div class="lbl">Đang giao</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon green"><i class="fa fa-check-circle"></i></div><div><div class="num">{{ $stats['completed'] }}</div><div class="lbl">Hoàn thành</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon red"><i class="fa fa-times-circle"></i></div><div><div class="num">{{ $stats['cancelled'] }}</div><div class="lbl">Đã hủy</div></div></div>
            </div>
            <div class="accp-tabs">
                <button class="accp-tab active">Tất cả</button>
                <button class="accp-tab">Chờ xác nhận</button>
                <button class="accp-tab">Đang xử lý</button>
                <button class="accp-tab">Đang giao</button>
                <button class="accp-tab">Hoàn thành</button>
                <button class="accp-tab">Đã hủy</button>
                <button class="accp-tab">Trả hàng</button>
            </div>
            <div class="accp-toolbar">
                <div class="accp-search"><i class="fa fa-search"></i><input type="text" placeholder="Tìm theo mã đơn, sản phẩm..."></div>
                <input type="text" class="accp-select" style="width:200px" placeholder="Chọn khoảng ngày">
            </div>

            @forelse($orders as $order)
            @php
                $statusMap = ['pending' => ['Chờ xác nhận', 'amber'], 'confirmed' => ['Đã xác nhận', 'teal'], 'processing' => ['Đang xử lý', 'teal'], 'shipping' => ['Đang giao', 'blue'], 'shipped' => ['Đang giao', 'blue'], 'completed' => ['Hoàn thành', 'green'], 'cancelled' => ['Đã hủy', 'red']];
                $statusInfo = $statusMap[$order->status] ?? ['Không xác định', 'gray'];
                $firstItem = $order->items->first();
                $orderCode = $order->code;
            @endphp
            <div class="accp-card" style="padding:18px">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;flex-wrap:wrap;gap:8px">
                    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
                        <span style="font-size:13px;font-weight:700;color:#111827">#{{ $orderCode }}</span>
                        <span style="font-size:12px;color:#6b7280"><i class="fa fa-clock-o"></i> {{ ($order->placed_at ?? $order->created_at)?->format('d/m/Y H:i') }}</span>
                        @if($order->shipping && $order->shipping->tracking_number)
                        <span style="font-size:11px;color:#3b82f6"><i class="fa fa-truck"></i> {{ $order->shipping->tracking_number }}</span>
                        @endif
                    </div>
                    <span class="accp-badge {{ $statusInfo[1] }}">{{ $statusInfo[0] }}</span>
                </div>
                @foreach($order->items->take(2) as $item)
                <div style="display:flex;align-items:center;gap:14px;padding:10px 0;border-top:1px solid #f3f4f6">
                    <div style="width:56px;height:56px;background:#f8fafc;border-radius:8px;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
                        @if($item->product && $item->product->product_image)
                        <img src="{{ \App\Support\AssetUrl::productVariant($item->product->product_image, 'thumb') }}" alt="" style="width:100%;height:100%;object-fit:cover">
                        @else
                        <i class="fa fa-laptop" style="font-size:24px;color:#0d9488;opacity:.5"></i>
                        @endif
                    </div>
                    <div style="flex:1"><div style="font-size:13px;font-weight:600;color:#111827">{{ $item->product_name ?? $item->product?->name ?? 'Sản phẩm' }}</div><div style="font-size:12px;color:#6b7280">x{{ $item->quantity }}</div></div>
                    <div style="font-size:15px;font-weight:700;color:#0d9488">{{ number_format($item->price) }}đ</div>
                </div>
                @endforeach
                @if($order->items->count() > 2)
                <div style="font-size:12px;color:#6b7280;padding:4px 0">...và {{ $order->items->count() - 2 }} sản phẩm khác</div>
                @endif
                <div style="display:flex;justify-content:space-between;align-items:center;border-top:1px solid #f3f4f6;padding-top:12px">
                    <div style="font-size:13px;color:#6b7280">Tổng: <strong style="color:#111827">{{ number_format($order->total) }}đ</strong></div>
                    <div style="display:flex;gap:8px">
                        @if(in_array($order->status, ['shipping', 'shipped']))
                        <a href="{{ route('customer.account.order-tracking', ['orderCode' => $orderCode]) }}" class="accp-btn accp-btn--primary accp-btn--sm"><i class="fa fa-truck"></i> Theo dõi</a>
                        @endif
                        @if(in_array($order->status, ['pending', 'confirmed', 'processing'], true))
                        <button type="button" class="accp-btn accp-btn--outline accp-btn--sm js-cancel-order-trigger" style="color:#dc2626;border-color:#dc2626" data-order-code="{{ $orderCode }}"><i class="fa fa-times"></i> Hủy đơn</button>
                        @endif
                        @if($order->status === 'completed')
                        <a href="#" class="accp-btn accp-btn--primary accp-btn--sm"><i class="fa fa-refresh"></i> Mua lại</a>
                        <a href="#" class="accp-btn accp-btn--outline accp-btn--sm"><i class="fa fa-star"></i> Đánh giá</a>
                        @endif
                        <a href="{{ route('customer.account.order-tracking', ['orderCode' => $orderCode]) }}" class="accp-btn accp-btn--outline accp-btn--sm">Chi tiết</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="accp-card" style="padding:40px;text-align:center">
                <i class="fa fa-shopping-cart" style="font-size:48px;color:#d1d5db;margin-bottom:12px"></i>
                <div style="font-size:15px;font-weight:600;color:#6b7280;margin-bottom:8px">Chưa có đơn hàng nào</div>
                <a href="{{ url('/san-pham') }}" class="accp-btn accp-btn--primary">Mua sắm ngay</a>
            </div>
            @endforelse

            {{-- Real pagination --}}
            @if($orders->hasPages())
            <div style="display:flex;justify-content:center;margin-top:20px">
                {{ $orders->links('vendor.pagination.shop-pagination') }}
            </div>
            @endif
        </main>
        <aside class="accp-aside">
            <div class="accp-aside-card accp-contact-card"><h4><i class="fa fa-headphones" style="color:#0d9488;margin-right:6px"></i>Hỗ trợ</h4><div class="hotline">1900 1234</div><div class="hotline-sub">8:00 - 21:00</div><button type="button" class="accp-chat-btn" data-chatbot-launch><i class="fa fa-comment-o"></i> Chat với chúng tôi</button></div>
        </aside>
    </div>
</div>
@include('pages.history.partials.cancel_order_modal')
@endsection
