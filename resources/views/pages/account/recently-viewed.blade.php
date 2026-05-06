@extends('layouts.home-tech')
@section('body_class', 'accp-page')
@section('content')
<link rel="stylesheet" href="{{ asset('frontend/css/account-shared.css') }}">
<style>
.rvp-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:16px}
@media(max-width:1100px){.rvp-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:600px){.rvp-grid{grid-template-columns:repeat(2,1fr)}}
.rvp-card{background:#fff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;transition:box-shadow .2s;position:relative}
.rvp-card:hover{box-shadow:0 4px 12px rgba(0,0,0,.08)}
.rvp-fav{position:absolute;top:8px;right:8px;width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.9);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#d1d5db;font-size:14px;z-index:2;transition:color .15s}
.rvp-fav:hover,.rvp-fav.active{color:#dc2626}
.rvp-img{height:140px;display:flex;align-items:center;justify-content:center;padding:12px;background:#fafafa}
.rvp-img img{max-width:100%;max-height:100%;object-fit:contain}
.rvp-body{padding:12px}
.rvp-brand{font-size:10px;font-weight:700;color:#0d9488;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px}
.rvp-name{font-size:12.5px;font-weight:600;color:#111827;line-height:1.35;margin-bottom:6px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:34px}
.rvp-price{font-size:14px;font-weight:800;color:#dc2626;margin-bottom:2px}
.rvp-price .old{font-size:11px;color:#9ca3af;text-decoration:line-through;font-weight:400;margin-left:6px}
.rvp-rating{display:flex;align-items:center;gap:4px;margin-bottom:6px}
.rvp-rating .stars{display:flex;gap:1px}.rvp-rating .stars i{font-size:10px;color:#eab308}
.rvp-rating .count{font-size:10px;color:#9ca3af}
.rvp-meta{font-size:10px;color:#9ca3af;display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
.rvp-meta .stock{color:#16a34a;font-weight:600}
.rvp-meta .stock.out{color:#dc2626}
.rvp-actions{display:flex;gap:6px}
.rvp-btn-detail{flex:1;height:32px;background:#fff;color:#0d9488;border:1.5px solid #0d9488;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;text-decoration:none;display:flex;align-items:center;justify-content:center}
.rvp-btn-detail:hover{background:#0d9488;color:#fff}
.rvp-btn-cart{width:32px;height:32px;background:#0d9488;color:#fff;border:none;border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;transition:background .15s;flex-shrink:0}
.rvp-btn-cart:hover{background:#0f766e}
.rvp-toolbar-extra{display:flex;align-items:center;gap:8px;font-size:12px;color:#6b7280}
.rvp-toolbar-extra a{color:#dc2626;text-decoration:none;font-weight:600;font-size:12px}
.rvp-toolbar-extra label{display:flex;align-items:center;gap:4px;cursor:pointer}
.rvp-toolbar-extra input[type="checkbox"]{accent-color:#0d9488;width:14px;height:14px}
/* Pagination */
.rvp-pagination{display:flex;align-items:center;gap:4px;justify-content:center;margin:16px 0}
.rvp-pagination a,.rvp-pagination span{display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none;color:#374151;border:1px solid #e5e7eb;transition:all .15s}
.rvp-pagination a:hover{border-color:#0d9488;color:#0d9488}
.rvp-pagination .active{background:#0d9488;color:#fff;border-color:#0d9488}
.rvp-pagination .dots{border:none;color:#9ca3af}
/* Recommend row */
.rvp-recommend-row{display:flex;gap:14px;overflow-x:auto;padding-bottom:8px}
.rvp-recommend-item{min-width:140px;max-width:160px;display:flex;gap:10px;align-items:center;flex-shrink:0}
.rvp-recommend-item img{width:50px;height:50px;border-radius:6px;border:1px solid #e5e7eb;object-fit:contain;flex-shrink:0}
.rvp-recommend-item .info .name{font-size:11px;font-weight:600;color:#111827;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.rvp-recommend-item .info .price{font-size:12px;font-weight:700;color:#dc2626}
</style>

@php
    $viewedProducts = $viewedProducts ?? collect();
    $paginator      = $paginator ?? null;
    $stats          = $stats ?? ['total' => 0, 'last_7_days' => 0, 'on_sale' => 0, 'in_stock' => 0];
@endphp

<div class="accp-wrap">
    @include('partials.storefront.breadcrumb', ['class' => 'accp-crumb', 'separator' => '›', 'items' => [['label' => 'Tài khoản', 'url' => route('customer.account')], ['label' => 'Sản phẩm đã xem', 'current' => true]]])

    <div class="accp-grid">
        @include('pages.account.partials.sidebar', ['currentSection' => 'recently-viewed'])

        <main>
            <div style="margin-bottom:20px"><h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0 0 4px">Sản phẩm đã xem</h2><p style="font-size:13px;color:#6b7280;margin:0">Các sản phẩm bạn đã xem gần đây</p></div>

            <div class="accp-stats">
                <div class="accp-stat"><div class="accp-stat-icon teal"><i class="fa fa-eye"></i></div><div><div class="num">{{ $stats['total'] }}</div><div class="lbl">sản phẩm đã xem</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon blue"><i class="fa fa-calendar"></i></div><div><div class="num">{{ $stats['last_7_days'] }}</div><div class="lbl">trong 7 ngày qua</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon amber"><i class="fa fa-tag"></i></div><div><div class="num">{{ $stats['on_sale'] }}</div><div class="lbl">sản phẩm đang giảm giá</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon green"><i class="fa fa-check-circle"></i></div><div><div class="num">{{ $stats['in_stock'] }}</div><div class="lbl">sản phẩm còn hàng</div></div></div>
            </div>

            <div class="accp-toolbar">
                <div class="accp-search"><i class="fa fa-search"></i><input type="text" placeholder="Tìm theo tên sản phẩm..."></div>
                <select class="accp-select"><option>Tất cả danh mục</option></select>
                <select class="accp-select"><option>Mới xem gần đây</option></select>
                <div class="rvp-toolbar-extra">
                    <a href="#" onclick="return false"><i class="fa fa-trash-o"></i> Xóa lịch sử</a>
                    <label><input type="checkbox"> Chọn tất cả</label>
                </div>
            </div>

            @if ($viewedProducts->isEmpty())
                <div style="background:#fff;border:1px dashed #cbd5e1;border-radius:10px;padding:32px;text-align:center;color:#6b7280">
                    <i class="fa fa-eye" style="font-size:32px;color:#cbd5e1;display:block;margin-bottom:6px"></i>
                    <div style="font-size:14px;font-weight:600;color:#374151">Bạn chưa xem sản phẩm nào</div>
                    <div style="font-size:12px;margin-top:2px">Các sản phẩm bạn đã xem gần đây sẽ hiển thị tại đây.</div>
                </div>
            @else
            <div class="rvp-grid">
                @foreach ($viewedProducts as $row)
                @php
                    $p = $row['product'];
                    $price = (float) ($p->sale_price > 0 ? $p->sale_price : $p->price);
                    $oldPrice = (float) $p->price;
                    $hasDiscount = $p->sale_price && $p->sale_price > 0 && $p->sale_price < $p->price;
                    $discountPercent = $hasDiscount && $oldPrice > 0 ? round((1 - $price / $oldPrice) * 100) : 0;
                    $thumb = $p->thumbnail ?? $p->image ?? 'uploads/product/no-image.svg';
                    $brandName = $p->brand?->name ?? '';
                    $detailUrl = url('/chi-tiet/' . ($p->slug ?? $p->id));
                @endphp
                <div class="rvp-card">
                    <button class="rvp-fav" title="Yêu thích"><i class="fa fa-heart-o"></i></button>
                    @if ($discountPercent > 0)<span style="position:absolute;top:8px;left:8px;background:#dc2626;color:#fff;font-size:10px;font-weight:700;padding:2px 6px;border-radius:4px;z-index:2">-{{ $discountPercent }}%</span>@endif
                    <div class="rvp-img"><a href="{{ $detailUrl }}"><img src="{{ asset($thumb) }}" alt="{{ $p->name }}"></a></div>
                    <div class="rvp-body">
                        @if ($brandName)<div class="rvp-brand">{{ $brandName }}</div>@endif
                        <div class="rvp-name"><a href="{{ $detailUrl }}" style="color:inherit;text-decoration:none">{{ $p->name }}</a></div>
                        <div class="rvp-price">{{ number_format($price, 0, ',', '.') }}đ @if($hasDiscount)<span class="old">{{ number_format($oldPrice, 0, ',', '.') }}đ</span>@endif</div>
                        <div class="rvp-meta"><span>Đã xem {{ $row['last_viewed_at'] ? $row['last_viewed_at']->diffForHumans() : '' }}</span><span class="stock {{ $p->is_active ? '' : 'out' }}">{{ $p->is_active ? 'Còn hàng' : 'Hết hàng' }}</span></div>
                        <div class="rvp-actions">
                            <a href="{{ $detailUrl }}" class="rvp-btn-detail">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if ($paginator && $paginator->hasPages())
            <div class="rvp-pagination" style="margin-top:16px">{{ $paginator->links() }}</div>
            @endif
            @endif
        </main>

        <aside class="accp-aside">
            <div class="accp-aside-card accp-contact-card">
                <h4>Hỗ trợ khách hàng</h4>
                <div style="padding:4px 0"><span style="font-size:11px;color:#9ca3af;display:block">Hotline:</span><span class="hotline">1900 1234</span><div class="hotline-sub">8:00 - 21:00 (T2 - CN)</div></div>
                <div style="padding:6px 0;border-top:1px solid #f3f4f6"><span style="font-size:11px;color:#9ca3af;display:block">Email:</span><a class="email" href="mailto:support@dangduc.vn">support@dangduc.vn</a><div class="email-sub">Phản hồi trong 30 phút</div></div>
                <div style="padding:6px 0;border-top:1px solid #f3f4f6"><span style="font-size:11px;color:#9ca3af;display:block">Chat trực tuyến</span><span style="font-size:12px;color:#6b7280">Hỗ trợ nhanh chóng 24/7</span></div>
                <button class="accp-chat-btn-outline"><i class="fa fa-phone"></i> Liên hệ ngay</button>
            </div>
            <div class="accp-aside-card">
                <h4>Mẹo mua sắm thông minh</h4>
                <ul class="accp-aside-tips">
                    <li><i class="fa fa-check"></i>Thêm sản phẩm yêu thích để dễ dàng theo dõi.</li>
                    <li><i class="fa fa-check"></i>So sánh và xem đánh giá trước khi quyết định.</li>
                    <li><i class="fa fa-check"></i>Săn ưu đãi và voucher để tiết kiệm hơn.</li>
                </ul>
                <a href="{{ url('/khuyen-mai') }}" class="accp-btn accp-btn--outline accp-btn--block" style="margin-top:10px">Xem ưu đãi hôm nay</a>
            </div>
        </aside>
    </div>

    {{-- Recommend: chờ recommendation engine thật --}}
</div>
@endsection
