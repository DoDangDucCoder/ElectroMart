@extends('layouts.home-tech')
@section('body_class','accp-page')
@section('content')
@php
    $statusMap = ['pending' => ['Chờ xác nhận', 'amber'], 'confirmed' => ['Đã xác nhận', 'teal'], 'processing' => ['Đang xử lý', 'teal'], 'shipping' => ['Đang giao', 'blue'], 'shipped' => ['Đang giao', 'blue'], 'completed' => ['Hoàn thành', 'green'], 'cancelled' => ['Đã hủy', 'red']];
    $statusInfo = $statusMap[$order->status] ?? ['Không xác định', 'gray'];
    $orderCode = $order->code;
    $shippingInfo = $order->shipping;
    $addressInfo = $order->address;
    $shippingAddress = $shippingInfo
        ? implode(', ', array_filter([
            $shippingInfo->address_line1,
            $shippingInfo->address_line2,
            $shippingInfo->ward?->name,
            $shippingInfo->province?->name,
            $shippingInfo->city?->name,
            $shippingInfo->country,
        ]))
        : implode(', ', array_filter([
            $addressInfo?->address_line1,
            $addressInfo?->address_line2,
            $addressInfo?->ward?->name,
            $addressInfo?->province?->name,
            $addressInfo?->city?->name,
            $addressInfo?->country,
        ]));
    $shippingMethodLabel = $shippingInfo?->shipping_method
        ? ucfirst(str_replace('_', ' ', $shippingInfo->shipping_method))
        : 'Giao hàng tiêu chuẩn';
    $firstItem = $order->items->first();
@endphp
<div class="accp-wrap">
    @include('partials.storefront.breadcrumb', ['class' => 'accp-crumb', 'separator' => '›', 'items' => [['label' => 'Tài khoản', 'url' => url('/account')], ['label' => 'Đơn hàng', 'url' => url('/account/orders')], ['label' => 'Theo dõi #' . $orderCode, 'current' => true]]])
    <div class="accp-grid">
        @include('pages.account.partials.sidebar', ['currentSection' => 'tracking', 'customer' => $customer])
        <main>
            <div class="accp-page-title"><h1>Theo dõi đơn hàng #{{ $orderCode }}</h1></div>

            {{-- Order info bar --}}
            <div class="accp-card" style="padding:18px;margin-bottom:16px">
                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
                    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
                        <div><span style="font-size:12px;color:#6b7280">Mã đơn:</span> <strong style="font-size:14px;color:#111827">#{{ $orderCode }}</strong></div>
                        <div><span style="font-size:12px;color:#6b7280">Ngày đặt:</span> <strong style="font-size:13px">{{ ($order->placed_at ?? $order->created_at)?->format('d/m/Y') }}</strong></div>
                        <span class="accp-badge {{ $statusInfo[1] }}"><i class="fa fa-{{ in_array($order->status, ['shipping','shipped']) ? 'truck' : 'circle' }}"></i> {{ $statusInfo[0] }}</span>
                    </div>
                    @if($shippingInfo && $shippingInfo->tracking_number)
                    <div style="display:flex;align-items:center;gap:8px;font-size:13px">
                        <span style="color:#6b7280">Mã vận đơn:</span>
                        <code style="background:#f0fdfa;color:#0d9488;padding:4px 10px;border-radius:6px;font-weight:600;font-size:12px">{{ $shippingInfo->tracking_number }}</code>
                        <button style="background:none;border:none;color:#0d9488;cursor:pointer;font-size:13px" title="Sao chép" onclick="navigator.clipboard.writeText('{{ $shippingInfo->tracking_number }}')"><i class="fa fa-copy"></i></button>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Timeline --}}
            <div class="accp-card" style="padding:24px;margin-bottom:16px">
                <h3 style="font-size:16px;font-weight:700;color:#111827;margin:0 0 20px"><i class="fa fa-map-marker" style="color:#0d9488;margin-right:6px"></i>Trạng thái vận chuyển</h3>
                <div class="tk-timeline">
                    @foreach($steps as $i => $s)
                    <div class="tk-step {{ $s['done'] ? 'done' : '' }} {{ ($s['active'] ?? false) ? 'active' : '' }}">
                        <div class="tk-dot">
                            @if($s['done'])<i class="fa fa-check"></i>
                            @elseif($s['active'] ?? false)<i class="fa fa-circle"></i>
                            @else<span></span>@endif
                        </div>
                        <div class="tk-info">
                            <div class="tk-title">{{ $s['title'] }}</div>
                            <div class="tk-time">{{ $s['time'] ? (is_string($s['time']) ? $s['time'] : $s['time']->format('d/m/Y H:i')) : '' }}</div>
                            @if($s['desc'] ?? '')<div class="tk-desc">{{ $s['desc'] }}</div>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Order details --}}
            <div class="accp-card" style="padding:18px">
                <h3 style="font-size:15px;font-weight:700;color:#111827;margin:0 0 14px">Chi tiết đơn hàng</h3>
                @foreach($order->items as $item)
                <div style="display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid #f3f4f6">
                    <div style="width:64px;height:64px;background:#f8fafc;border-radius:8px;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
                        @if($item->product && $item->product->product_image)
                        <img src="{{ \App\Support\AssetUrl::productVariant($item->product->product_image, 'thumb') }}" alt="" style="width:100%;height:100%;object-fit:cover">
                        @else
                        <i class="fa fa-laptop" style="font-size:28px;color:#0d9488;opacity:.5"></i>
                        @endif
                    </div>
                    <div style="flex:1"><div style="font-size:14px;font-weight:600;color:#111827">{{ $item->product_name ?? $item->product?->name ?? 'Sản phẩm' }}</div><div style="font-size:12px;color:#6b7280;margin-top:2px">x{{ $item->quantity }}</div></div>
                    <div style="font-size:16px;font-weight:700;color:#0d9488">{{ number_format($item->price) }}đ</div>
                </div>
                @endforeach
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;padding-top:14px">
                    <div><span style="font-size:12px;color:#6b7280">Phí vận chuyển:</span> <strong style="color:{{ ($shippingInfo->shipping_fee ?? 0) > 0 ? '#111827' : '#16a34a' }}">{{ ($shippingInfo->shipping_fee ?? 0) > 0 ? number_format($shippingInfo->shipping_fee).'đ' : 'Miễn phí' }}</strong></div>
                    <div><span style="font-size:12px;color:#6b7280">Tổng cộng:</span> <strong style="font-size:18px;color:#0d9488">{{ number_format($order->total) }}đ</strong></div>
                    @if($order->address || $shippingInfo)
                    <div style="grid-column:span 2;padding-top:10px;border-top:1px solid #f3f4f6">
                        <span style="font-size:12px;color:#6b7280">Giao đến:</span>
                        <div style="font-size:13px;color:#111827;font-weight:500;margin-top:4px"><i class="fa fa-map-marker" style="color:#0d9488;margin-right:4px"></i>{{ $shippingInfo->recipient_name ?? $customer->name }}, {{ $shippingInfo->recipient_phone ?? $customer->phone }}, {{ $shippingAddress }}</div>
                    </div>
                    @endif
                </div>
                <div style="display:flex;gap:10px;margin-top:16px">
                    <a href="#" class="accp-btn accp-btn--outline"><i class="fa fa-headphones"></i> Liên hệ hỗ trợ</a>
                    @if(in_array($order->status, ['pending', 'confirmed', 'processing'], true))
                    <button type="button" class="accp-btn accp-btn--outline js-cancel-order-trigger" style="color:#dc2626;border-color:#dc2626" data-order-code="{{ $orderCode }}"><i class="fa fa-times"></i> Hủy đơn</button>
                    @endif
                    @if(in_array($order->status, ['shipping', 'shipped']))
                    <a href="#" class="accp-btn accp-btn--primary"><i class="fa fa-check"></i> Xác nhận đã nhận</a>
                    @endif
                </div>
            </div>
        </main>
        <aside class="accp-aside">
            <div class="accp-aside-card"><h4><i class="fa fa-truck" style="color:#0d9488;margin-right:6px"></i>Thông tin giao hàng</h4>
                <div style="font-size:12px;color:#6b7280;margin-top:8px">Đơn vị: <strong style="color:#111827">{{ $shippingMethodLabel }}</strong></div>
                @if($order->shipped_at)
                <div style="font-size:12px;color:#6b7280;margin-top:4px">Dự kiến: <strong style="color:#0d9488">{{ $order->shipped_at->copy()->addDays(3)->format('d/m/Y') }}</strong></div>
                @endif
                <div style="font-size:12px;color:#6b7280;margin-top:4px">Loại: <strong>{{ $shippingMethodLabel }}</strong></div>
            </div>
            <div class="accp-aside-card accp-contact-card"><h4><i class="fa fa-headphones" style="color:#0d9488;margin-right:6px"></i>Hỗ trợ</h4><div class="hotline">1900 1234</div><div class="hotline-sub">8:00 - 21:00</div><button type="button" class="accp-chat-btn" data-chatbot-launch><i class="fa fa-comment-o"></i> Chat</button></div>
        </aside>
    </div>
</div>
@include('pages.history.partials.cancel_order_modal')
<style>
.tk-timeline{position:relative;padding-left:30px}
.tk-step{position:relative;padding-bottom:24px;padding-left:16px}
.tk-step:last-child{padding-bottom:0}
.tk-step::before{content:'';position:absolute;left:-21px;top:20px;bottom:0;width:2px;background:#e5e7eb}
.tk-step:last-child::before{display:none}
.tk-step.done::before{background:#0d9488}
.tk-dot{position:absolute;left:-30px;top:2px;width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;z-index:1}
.tk-step.done .tk-dot{background:#0d9488;color:#fff}
.tk-step.active .tk-dot{background:#3b82f6;color:#fff;animation:pulse 2s infinite}
.tk-step:not(.done):not(.active) .tk-dot{background:#e5e7eb;color:#9ca3af}
.tk-step:not(.done):not(.active) .tk-dot span{width:8px;height:8px;border-radius:50%;background:#d1d5db}
.tk-title{font-size:14px;font-weight:600;color:#111827}
.tk-step:not(.done):not(.active) .tk-title{color:#9ca3af}
.tk-time{font-size:12px;color:#6b7280;margin-top:2px}
.tk-desc{font-size:12px;color:#6b7280;margin-top:4px;line-height:1.4}
@keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(59,130,246,.4)}70%{box-shadow:0 0 0 8px rgba(59,130,246,0)}}
</style>
@endsection
