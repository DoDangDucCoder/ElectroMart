@extends('layouts.home-tech')
@section('body_class','accp-page')
@section('content')
<div class="accp-wrap">
    @include('partials.storefront.breadcrumb', ['class' => 'accp-crumb', 'separator' => '›', 'items' => [['label' => 'Tài khoản', 'url' => url('/account')], ['label' => 'Sản phẩm yêu thích', 'current' => true]]])
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
            <div class="accp-product-grid accp-product-grid--compact">
                @forelse($products as $p)
                <div>
                    @include('partials.home.product-card', ['product' => $p, 'compact' => true])
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
            <div class="accp-aside-card accp-contact-card"><h4><i class="fa fa-headphones" style="color:#0d9488;margin-right:6px"></i>Hỗ trợ</h4><div class="hotline">1900 1234</div><div class="hotline-sub">8:00 - 21:00</div><button type="button" class="accp-chat-btn" data-chatbot-launch><i class="fa fa-comment-o"></i> Chat</button></div>
        </aside>
    </div>
</div>
@endsection
