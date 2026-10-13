@extends('layouts.home-tech')
@section('body_class', 'accp-page')
@section('content')
<style>
.rt-item{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px 18px;margin-bottom:12px;transition:box-shadow .15s}
.rt-item:hover{box-shadow:0 2px 8px rgba(0,0,0,.05)}
.rt-head{display:flex;align-items:center;gap:10px;margin-bottom:10px;flex-wrap:wrap}
.rt-code{font-size:15px;font-weight:800;color:#111827}
.rt-dates{font-size:11px;color:#9ca3af}
.rt-body{display:grid;grid-template-columns:1fr 160px 180px 160px;gap:14px;align-items:flex-start}
@media(max-width:900px){.rt-body{grid-template-columns:1fr 1fr;gap:10px}}
@media(max-width:600px){.rt-body{grid-template-columns:1fr}}
.rt-product{display:flex;gap:10px;align-items:center}
.rt-prod-img{width:56px;height:56px;border-radius:8px;border:1px solid #e5e7eb;overflow:hidden;flex-shrink:0}
.rt-prod-img img{width:100%;height:100%;object-fit:cover}
.rt-prod-info{min-width:0}
.rt-prod-name{font-size:12.5px;font-weight:600;color:#111827;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.rt-prod-meta{font-size:10.5px;color:#9ca3af;margin-top:2px}
.rt-reason{font-size:12px;color:#374151}
.rt-reason .label{font-size:10px;color:#9ca3af;margin-bottom:2px}
.rt-refund{min-width:0}
.rt-refund .label{font-size:10px;color:#9ca3af;margin-bottom:2px}
.rt-refund .amount{font-size:16px;font-weight:800;color:#dc2626}
.rt-refund .method{font-size:10.5px;color:#6b7280;margin-top:2px}
.rt-status-col{text-align:right}
.rt-status-col .date{font-size:10px;color:#9ca3af;margin-top:4px}
.rt-actions{display:flex;gap:6px;margin-top:6px;justify-content:flex-end;flex-wrap:wrap}
</style>

@php
    $returns = $returns ?? collect();
    $stats   = $stats ?? ['total' => 0, 'approved' => 0, 'pending' => 0, 'refunded' => 0, 'total_refund' => 0];
    $visibleRecommendedProducts = collect($recommendedProducts ?? []);
    $visibleRecommendedProducts = $visibleRecommendedProducts
        ->take($visibleRecommendedProducts->count() >= 8 ? 8 : min(4, $visibleRecommendedProducts->count()));
@endphp

<div class="accp-wrap">
    @include('partials.storefront.breadcrumb', ['class' => 'accp-crumb', 'separator' => '›', 'items' => [['label' => 'Tài khoản', 'url' => route('customer.account')], ['label' => 'Trả hàng / Hoàn tiền', 'current' => true]]])
    <div class="accp-page-title"><h1>Trả hàng / hoàn tiền</h1><p>Quản lý và theo dõi các yêu cầu trả hàng và hoàn tiền của bạn.</p></div>

    <div class="accp-grid">
        @include('pages.account.partials.sidebar', ['currentSection' => 'returns'])

        <main>
            <div class="accp-stats">
                <div class="accp-stat"><div class="accp-stat-icon red"><i class="fa fa-undo"></i></div><div><div class="num">{{ $stats['total'] }}</div><div class="lbl">Tổng yêu cầu</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon green"><i class="fa fa-check-circle"></i></div><div><div class="num">{{ $stats['approved'] }}</div><div class="lbl">Đã chấp thuận</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon amber"><i class="fa fa-clock-o"></i></div><div><div class="num">{{ $stats['pending'] }}</div><div class="lbl">Chờ xử lý</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon teal"><i class="fa fa-money"></i></div><div><div class="num" style="font-size:18px">{{ number_format((float) $stats['total_refund'], 0, ',', '.') }}đ</div><div class="lbl">Tổng hoàn tiền</div></div></div>
            </div>

            <div class="accp-toolbar">
                <div class="accp-search"><i class="fa fa-search"></i><input type="text" placeholder="Tìm theo mã yêu cầu, tên sản phẩm..."></div>
                <select class="accp-select"><option>30 ngày qua</option></select>
                <select class="accp-select"><option>Tất cả trạng thái</option></select>
                <select class="accp-select"><option>Mới nhất</option></select>
            </div>

            <div class="accp-tabs">
                <button class="accp-tab active">Tất cả ({{ $stats['total'] }})</button>
                <button class="accp-tab">Chờ xử lý ({{ $stats['pending'] }})</button>
                <button class="accp-tab">Đã chấp thuận ({{ $stats['approved'] }})</button>
                <button class="accp-tab">Đã hoàn tiền ({{ $stats['refunded'] }})</button>
            </div>

            @forelse ($returns as $r)
            @php
                $product = optional($r->orderItem)->product ?? null;
                $productImage = \App\Support\AssetUrl::productVariant($product?->product_image ?? $product?->thumbnail_path ?? $product?->thumbnail ?? $product?->image ?? null, 'thumb');
            @endphp
            <div class="rt-item">
                <div class="rt-head">
                    <span class="rt-code">#{{ $r->code }}</span>
                    <span class="rt-dates">
                        Ngày tạo: {{ optional($r->created_at)->format('d/m/Y - H:i') }}<br>
                        @if ($r->order)
                            Đơn hàng: <a href="{{ route('customer.account.order-tracking', ['orderCode' => $r->order->code]) }}" style="color:#0d9488;text-decoration:none;font-weight:600">#{{ $r->order->code }}</a>
                        @endif
                    </span>
                </div>
                <div class="rt-body">
                    <div class="rt-product">
                        <div class="rt-prod-img"><img src="{{ $productImage }}" alt="{{ $r->orderItem?->product_name ?? $product?->name ?? 'Sản phẩm' }}"></div>
                        <div class="rt-prod-info">
                            <div class="rt-prod-name">{{ $r->orderItem?->product_name ?? $product?->name ?? 'Sản phẩm không còn tồn tại' }}</div>
                            <div class="rt-prod-meta">SL: {{ $r->quantity }} sản phẩm</div>
                        </div>
                    </div>
                    <div class="rt-reason">
                        <div class="label">Lý do trả hàng</div>
                        <div>{{ $r->reason }}</div>
                    </div>
                    <div class="rt-refund">
                        <div class="label">Số tiền hoàn</div>
                        <div class="amount">{{ number_format((float) $r->refund_amount, 0, ',', '.') }}đ</div>
                        @if ($r->refund_method)<div class="method">Phương thức: {{ $r->refund_method }}</div>@endif
                    </div>
                    <div class="rt-status-col">
                        <span class="accp-badge {{ $r->statusClass() }}">{{ $r->statusLabel() }}</span>
                        @if ($r->refunded_at)<div class="date">Ngày hoàn: {{ $r->refunded_at->format('d/m/Y') }}</div>@endif
                    </div>
                </div>
            </div>
            @empty
            <div style="background:#fff;border:1px dashed #cbd5e1;border-radius:10px;padding:32px;text-align:center;color:#6b7280">
                <i class="fa fa-undo" style="font-size:32px;color:#cbd5e1;display:block;margin-bottom:6px"></i>
                <div style="font-size:14px;font-weight:600;color:#374151">Bạn chưa có yêu cầu trả hàng/hoàn tiền nào</div>
                <div style="font-size:12px;margin-top:2px">Nếu cần đổi trả, hãy vào chi tiết đơn hàng để gửi yêu cầu.</div>
            </div>
            @endforelse

            @if ($returns instanceof \Illuminate\Contracts\Pagination\Paginator && $returns->hasPages())
            <div style="margin-top:16px">{{ $returns->links('vendor.pagination.shop-pagination') }}</div>
            @endif
        </main>

        <aside class="accp-aside">
            <div class="accp-aside-card accp-contact-card">
                <h4>Bạn cần hỗ trợ?</h4>
                <p style="font-size:12px;color:#6b7280;margin:0 0 10px">Đội ngũ DangDuc luôn sẵn sàng hỗ trợ bạn về các đơn hàng đổi trả và hoàn tiền.</p>
                <div style="padding:4px 0;border-bottom:1px solid #f3f4f6"><span style="font-size:11px;color:#9ca3af;display:block">Hotline</span><span class="hotline">1900 1234</span><div class="hotline-sub">(8:00 - 21:00 mỗi ngày)</div></div>
                <div style="padding:6px 0;border-bottom:1px solid #f3f4f6"><span style="font-size:11px;color:#9ca3af;display:block">Email</span><a class="email" href="mailto:support@dangduc.vn">support@dangduc.vn</a><div class="email-sub">Phản hồi trong 24h</div></div>
                <div style="padding:6px 0"><span style="font-size:11px;color:#9ca3af;display:block">Chat trực tuyến</span><span style="font-size:12px;color:#111827;font-weight:600">Trò chuyện ngay</span></div>
            </div>
            <div class="accp-aside-card">
                <h4>Lưu ý hoàn tiền</h4>
                <ul class="accp-aside-tips">
                    <li><i class="fa fa-info-circle"></i>Thời gian hoàn tiền dự kiến 3 - 7 ngày làm việc tùy thuộc vào phương thức thanh toán và ngân hàng.</li>
                    <li><i class="fa fa-info-circle"></i>Tiền sẽ được hoàn vào tài khoản/ví/thẻ mà bạn đã sử dụng khi thanh toán.</li>
                </ul>
            </div>
            <div class="accp-aside-card">
                <h4>Mua sắm an tâm</h4>
                <div class="accp-aside-trust">
                    <div class="accp-aside-trust-item"><i class="fa fa-check-circle"></i><span>100% sản phẩm chính hãng, bảo vệ quyền lợi của bạn.</span></div>
                </div>
            </div>
        </aside>
    </div>

    {{-- Recommend --}}
    @if ($visibleRecommendedProducts->isNotEmpty())
    <div class="accp-recommend" style="margin-top:20px">
        <div class="accp-recommend-head"><h3>Có thể bạn cũng thích</h3><a href="{{ url('/san-pham') }}">Xem tất cả <i class="fa fa-arrow-right"></i></a></div>
        <div class="accp-recommend-grid">
            @foreach ($visibleRecommendedProducts as $p)
                @php $pc = ['id'=>$p->id,'name'=>$p->name??'','url'=>url('/chi-tiet/'.($p->slug ?? $p->product_slug ?? $p->id)),'image'=>\App\Support\AssetUrl::productVariant($p->product_image ?? $p->thumbnail_path ?? null, 'thumb'),'original_price'=>$p->price??0,'price'=>$p->sale_price??$p->price??0]; @endphp
                <div>@include('partials.home.product-card', ['product' => $pc, 'compact' => true])</div>
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
