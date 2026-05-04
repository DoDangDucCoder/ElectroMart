@extends('layouts.home-tech')
@section('body_class','accp-page')
@push('styles')<link href="{{ asset('frontend/css/account-shared.css') }}" rel="stylesheet">@endpush
@section('content')
<div class="accp-wrap">
    <nav class="accp-crumb"><a href="{{ url('/') }}">Trang chủ</a><span class="sep"><i class="fa fa-angle-right"></i></span><a href="{{ url('/account') }}">Tài khoản</a><span class="sep"><i class="fa fa-angle-right"></i></span><span class="current">Sản phẩm yêu thích</span></nav>
    <div class="accp-grid">
        @include('pages.account.partials.sidebar', ['currentSection' => 'wishlist', 'customer' => $customer])
        <main>
            <div class="accp-page-title"><h1>Sản phẩm yêu thích</h1><p>{{ $products->count() }} sản phẩm đã lưu</p></div>
            <div class="accp-stats">
                <div class="accp-stat"><div class="accp-stat-icon red"><i class="fa fa-heart"></i></div><div><div class="num">{{ $stats['total'] }}</div><div class="lbl">Yêu thích</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon green"><i class="fa fa-tag"></i></div><div><div class="num">{{ $stats['on_sale'] }}</div><div class="lbl">Đang giảm giá</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon amber"><i class="fa fa-exclamation-triangle"></i></div><div><div class="num">{{ $stats['out_of_stock'] }}</div><div class="lbl">Hết hàng</div></div></div>
            </div>
            <div class="accp-toolbar">
                <div class="accp-search"><i class="fa fa-search"></i><input type="text" placeholder="Tìm trong danh sách yêu thích..."></div>
                <select class="accp-select"><option>Tất cả danh mục</option></select>
                <select class="accp-select"><option>Mới nhất</option></select>
            </div>
            <div class="wl-grid">
                @forelse($products as $p)
                @php
                    $originalPrice = (float) ($p->price ?? 0);
                    $salePrice = (float) ($p->sale_price ?? $p->final_price ?? $originalPrice);
                    $hasSale = $salePrice > 0 && $salePrice < $originalPrice;
                    $discountPercent = $hasSale ? (int) round((($originalPrice - $salePrice) / $originalPrice) * 100) : 0;
                    $inStock = ($p->stock_quantity ?? 0) > 0;
                    $brandName = $p->brand?->brand_name ?? $p->brand?->name ?? '';
                @endphp
                <div class="wl-card">
                    <div class="wl-card-img">
                        <button class="wl-heart active"><i class="fa fa-heart"></i></button>
                        @if($discountPercent > 0)<span class="wl-disc">-{{ $discountPercent }}%</span>@endif
                        @if($p->product_image)
                        <img src="{{ asset($p->product_image) }}" alt="{{ $p->name }}" style="width:100%;height:100%;object-fit:contain">
                        @else
                        <i class="fa fa-laptop" style="font-size:48px;color:#0d9488;opacity:.3"></i>
                        @endif
                    </div>
                    <div class="wl-card-body">
                        @if($brandName)<span class="wl-brand">{{ $brandName }}</span>@endif
                        <h4 class="wl-name">{{ $p->name }}</h4>
                        <div style="margin-bottom:6px">
                            <span style="font-size:16px;font-weight:800;color:#0d9488">{{ number_format($hasSale ? $salePrice : $originalPrice) }}đ</span>
                            @if($hasSale)<span style="font-size:12px;color:#9ca3af;text-decoration:line-through;margin-left:6px">{{ number_format($originalPrice) }}đ</span>@endif
                        </div>
                        <div style="font-size:11px;font-weight:600;margin-bottom:10px;color:{{ $inStock ? '#16a34a' : '#dc2626' }}"><i class="fa {{ $inStock ? 'fa-check-circle' : 'fa-times-circle' }}"></i> {{ $inStock ? 'Còn hàng' : 'Hết hàng' }}</div>
                        @if($inStock)
                        <a href="#" class="accp-btn accp-btn--primary accp-btn--sm accp-btn--block"><i class="fa fa-shopping-cart"></i> Thêm vào giỏ</a>
                        @else
                        <button class="accp-btn accp-btn--outline accp-btn--sm accp-btn--block" disabled><i class="fa fa-bell"></i> Thông báo khi có hàng</button>
                        @endif
                    </div>
                </div>
                @empty
                <div style="grid-column:1/-1;text-align:center;padding:40px">
                    <i class="fa fa-heart-o" style="font-size:48px;color:#d1d5db;margin-bottom:12px"></i>
                    <div style="font-size:15px;font-weight:600;color:#6b7280;margin-bottom:8px">Chưa có sản phẩm yêu thích nào</div>
                    <a href="{{ url('/san-pham') }}" class="accp-btn accp-btn--primary">Khám phá sản phẩm</a>
                </div>
                @endforelse
            </div>
        </main>
        <aside class="accp-aside">
            <div class="accp-aside-card accp-contact-card"><h4><i class="fa fa-headphones" style="color:#0d9488;margin-right:6px"></i>Hỗ trợ</h4><div class="hotline">1900 1234</div><div class="hotline-sub">8:00 - 21:00</div><button class="accp-chat-btn"><i class="fa fa-comment-o"></i> Chat</button></div>
        </aside>
    </div>
</div>
<style>
.wl-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
@media(max-width:1100px){.wl-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:768px){.wl-grid{grid-template-columns:repeat(2,1fr)}}
.wl-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;transition:box-shadow .2s}.wl-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.08)}
.wl-card-img{position:relative;height:160px;background:#f8fafc;display:flex;align-items:center;justify-content:center;overflow:hidden}
.wl-heart{position:absolute;top:8px;right:8px;width:32px;height:32px;border-radius:50%;background:#fff;border:none;color:#d1d5db;font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 1px 4px rgba(0,0,0,.1);z-index:1}.wl-heart.active{color:#ef4444}
.wl-disc{position:absolute;top:8px;left:8px;background:#ef4444;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:4px;z-index:1}
.wl-card-body{padding:14px}
.wl-brand{font-size:10px;font-weight:700;color:#0d9488;text-transform:uppercase;letter-spacing:.5px}
.wl-name{font-size:13px;font-weight:600;color:#111827;margin:4px 0 6px;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
</style>
@endsection
