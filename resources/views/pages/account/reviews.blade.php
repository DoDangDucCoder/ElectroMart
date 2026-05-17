@extends('layouts.home-tech')
@section('body_class', 'accp-page')
@section('content')
<style>
.rv-item{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px;margin-bottom:12px;transition:box-shadow .15s}
.rv-item:hover{box-shadow:0 2px 8px rgba(0,0,0,.05)}
.rv-head{display:flex;align-items:flex-start;gap:14px;margin-bottom:10px}
.rv-prod{display:flex;gap:14px;flex:1;min-width:0}
.rv-img{width:70px;height:70px;border-radius:8px;border:1px solid #e5e7eb;overflow:hidden;flex-shrink:0}
.rv-img img{width:100%;height:100%;object-fit:cover}
.rv-prod-info h4{font-size:14px;font-weight:700;color:#111827;margin:0 0 2px;line-height:1.35}
.rv-prod-info .specs{font-size:11px;color:#9ca3af;margin-bottom:4px}
.rv-stars{display:flex;gap:1px;margin-bottom:2px}
.rv-stars i{color:#e5e7eb;font-size:13px}
.rv-stars i.active{color:#eab308}
.rv-actions{display:flex;gap:6px;flex-shrink:0}
.rv-content{margin-left:84px}
@media(max-width:768px){.rv-content{margin-left:0}}
.rv-title{font-size:14px;font-weight:700;color:#111827;margin:0 0 4px}
.rv-text{font-size:13px;color:#374151;line-height:1.55;margin:0 0 8px}
.rv-images{display:flex;gap:6px;margin-bottom:8px}
.rv-images img{width:52px;height:52px;border-radius:6px;border:1px solid #e5e7eb;object-fit:cover;cursor:pointer}
.rv-date{font-size:11px;color:#9ca3af}
.rv-bottom{display:flex;gap:8px;margin-top:8px;flex-wrap:wrap}
/* Review-able products */
.rv-able{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-top:14px}
@media(max-width:900px){.rv-able{grid-template-columns:repeat(2,1fr)}}
@media(max-width:600px){.rv-able{grid-template-columns:repeat(2,1fr)}}
.rv-able-item{border:1px solid #e5e7eb;border-radius:10px;padding:14px;background:#fff;text-align:center}
.rv-able-item img{width:60px;height:60px;object-fit:contain;margin:0 auto 8px;display:block}
.rv-able-item .name{font-size:12px;font-weight:600;color:#111827;margin-bottom:2px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.rv-able-item .price{font-size:12px;color:#dc2626;font-weight:700;margin-bottom:2px}
.rv-able-item .date{font-size:10px;color:#9ca3af;margin-bottom:6px}
/* Pagination */
.rv-pagination{display:flex;align-items:center;gap:4px;justify-content:center;margin-top:16px}
.rv-pagination a,.rv-pagination span{display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none;color:#374151;border:1px solid #e5e7eb;transition:all .15s}
.rv-pagination a:hover{border-color:#0d9488;color:#0d9488}
.rv-pagination .active{background:#0d9488;color:#fff;border-color:#0d9488}
</style>

@php
    $reviews            = $reviews ?? collect();
    $stats              = $stats ?? ['total' => 0, 'approved' => 0, 'pending' => 0, 'avg' => 0];
    $reviewableProducts = $reviewableProducts ?? collect();
    $visibleReviewableProducts = collect($reviewableProducts)
        ->take($reviewableProducts->count() >= 8 ? 8 : min(4, $reviewableProducts->count()));
    $statusMap          = ['approved' => ['Đã đăng', 'green'], 'pending' => ['Chờ duyệt', 'amber'], 'rejected' => ['Bị từ chối', 'red']];
@endphp

<div class="accp-wrap">
    @include('partials.storefront.breadcrumb', ['class' => 'accp-crumb', 'separator' => '›', 'items' => [['label' => 'Tài khoản', 'url' => route('customer.account')], ['label' => 'Nhận xét của tôi', 'current' => true]]])

    <div class="accp-grid">
        @include('pages.account.partials.sidebar', ['currentSection' => 'reviews'])

        <main>
            <div style="margin-bottom:20px"><h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0 0 4px">Nhận xét / đánh giá của tôi</h2><p style="font-size:13px;color:#6b7280;margin:0">Quản lý và theo dõi tất cả những đánh giá bạn đã viết cho sản phẩm.</p></div>

            <div class="accp-stats" style="grid-template-columns:repeat(4,1fr)">
                <div class="accp-stat"><div class="accp-stat-icon teal"><i class="fa fa-comments"></i></div><div><div class="num">{{ $stats['total'] }}</div><div class="lbl">Tổng số đánh giá</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon green"><i class="fa fa-check-circle"></i></div><div><div class="num">{{ $stats['approved'] }}</div><div class="lbl">Đã đăng</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon amber"><i class="fa fa-clock-o"></i></div><div><div class="num">{{ $stats['pending'] }}</div><div class="lbl">Chờ duyệt</div></div></div>
                <div class="accp-stat"><div class="accp-stat-icon star"><i class="fa fa-star"></i></div><div><div class="num">{{ $stats['avg'] ? number_format($stats['avg'], 1) . ' / 5' : '—' }}</div><div class="lbl">Đánh giá trung bình</div></div></div>
            </div>

            <div class="accp-toolbar">
                <div class="accp-search"><i class="fa fa-search"></i><input type="text" placeholder="Tìm theo sản phẩm hoặc nội dung..."></div>
                <button class="accp-tab active">Tất cả</button>
                <button class="accp-tab">Đã đăng</button>
                <button class="accp-tab">Chờ duyệt</button>
                <button class="accp-tab">1 sao</button>
                <button class="accp-tab">2 sao</button>
                <button class="accp-tab">3 sao</button>
                <button class="accp-tab">4 sao</button>
                <button class="accp-tab">5 sao</button>
                <select class="accp-select"><option>Sắp xếp: Mới nhất</option></select>
            </div>

            {{-- Review list --}}
            @forelse ($reviews as $rv)
            @php
                $st = $statusMap[$rv->status] ?? ['Khác', 'gray'];
                $product = $rv->product;
                $productUrl = $product ? url('/chi-tiet/' . ($product->slug ?? $product->id)) : '#';
                $productImage = \App\Support\AssetUrl::productVariant($product?->thumbnail ?? $product?->image ?? null, 'thumb');
            @endphp
            <div class="rv-item">
                <div class="rv-head">
                    <div class="rv-prod">
                        <div class="rv-img"><img src="{{ $productImage }}" alt=""></div>
                        <div class="rv-prod-info">
                            <h4>{{ $product?->name ?? 'Sản phẩm đã xoá' }}</h4>
                            <div class="rv-stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa fa-star {{ $i <= $rv->rating ? 'active' : '' }}"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <span class="accp-badge {{ $st[1] }}">{{ $st[0] }}</span>
                    <div class="rv-actions">
                        <a href="{{ $productUrl }}" class="accp-btn accp-btn--outline accp-btn--sm">Xem sản phẩm</a>
                    </div>
                </div>
                <div class="rv-content">
                    @if ($rv->title)<div class="rv-title">{{ $rv->title }}</div>@endif
                    <div class="rv-text">{{ $rv->content }}</div>
                    <div class="rv-date">Đã đánh giá ngày {{ optional($rv->created_at)->format('d/m/Y') }}</div>
                </div>
            </div>
            @empty
            <div style="background:#fff;border:1px dashed #cbd5e1;border-radius:10px;padding:32px;text-align:center;color:#6b7280">
                <i class="fa fa-comment-o" style="font-size:32px;color:#cbd5e1;display:block;margin-bottom:6px"></i>
                <div style="font-size:14px;font-weight:600;color:#374151">Bạn chưa viết đánh giá nào</div>
                <div style="font-size:12px;margin-top:2px">Đánh giá sản phẩm đã mua để chia sẻ trải nghiệm với cộng đồng.</div>
            </div>
            @endforelse

            @if ($reviews instanceof \Illuminate\Contracts\Pagination\Paginator && $reviews->hasPages())
            <div class="rv-pagination" style="margin-top:16px">{{ $reviews->links('vendor.pagination.shop-pagination') }}</div>
            @endif
        </main>

        <aside class="accp-aside">
            <div class="accp-aside-card">
                <h4>Mẹo đánh giá hữu ích</h4>
                <ul class="accp-aside-tips">
                    <li><i class="fa fa-lightbulb-o"></i><div><strong style="display:block;font-size:12px;color:#111827;margin-bottom:2px">Chia sẻ trải nghiệm thực tế</strong>Chọn nguyên nhân mua khác có quyết định quan trọng nhất.</div></li>
                    <li><i class="fa fa-pencil"></i><div><strong style="display:block;font-size:12px;color:#111827;margin-bottom:2px">Đánh giá chi tiết</strong>Nội dung từ 50 ký tự trở lên và có sắc thái rõ ràng.</div></li>
                    <li><i class="fa fa-camera"></i><div><strong style="display:block;font-size:12px;color:#111827;margin-bottom:2px">Thêm hình ảnh / video</strong>Hình ảnh thực tế giúp đánh giá đáng tin cậy hơn.</div></li>
                    <li><i class="fa fa-heart"></i><div><strong style="display:block;font-size:12px;color:#111827;margin-bottom:2px">Giữ thái độ văn minh</strong>Lịch sự, tôn trọng mọi người.</div></li>
                </ul>
            </div>
            <div class="accp-aside-card accp-contact-card">
                <h4>Bạn cần hỗ trợ?</h4>
                <div style="padding:6px 0"><span style="font-size:11px;color:#9ca3af;display:block">Hotline</span><span class="hotline">1900 1234</span><div class="hotline-sub">8:00 - 21:00 (T2 - CN)</div></div>
                <div style="padding:6px 0;border-top:1px solid #f3f4f6"><span style="font-size:11px;color:#9ca3af;display:block">Email</span><a class="email" href="mailto:support@dangduc.vn">support@dangduc.vn</a><div class="email-sub">Phản hồi trong 15 phút</div></div>
                <button class="accp-chat-btn"><i class="fa fa-comments"></i> Chat với chúng tôi</button>
            </div>
            <div class="accp-aside-card">
                <div class="accp-aside-trust">
                    <div class="accp-aside-trust-item"><i class="fa fa-check-circle"></i><span>100% hàng chính hãng. Cam kết chất lượng sản phẩm.</span></div>
                    <div class="accp-aside-trust-item"><i class="fa fa-refresh"></i><span>Đổi trả dễ dàng. 15 ngày nếu có lỗi từ NSX.</span></div>
                    <div class="accp-aside-trust-item"><i class="fa fa-shield"></i><span>Bảo hành chính hãng. Hỗ trợ bảo hành toàn quốc.</span></div>
                    <div class="accp-aside-trust-item"><i class="fa fa-lock"></i><span>Mua sắm an toàn. Thanh toán bảo mật tuyệt đối.</span></div>
                </div>
            </div>
        </aside>
    </div>

    {{-- Products you can review --}}
    @if ($visibleReviewableProducts->isNotEmpty())
    <div class="accp-recommend" style="margin-top:24px">
        <div class="accp-recommend-head"><h3>Sản phẩm bạn có thể đánh giá</h3></div>
        <div class="rv-able">
            @foreach ($visibleReviewableProducts as $able)
            <div class="rv-able-item">
                <img src="{{ \App\Support\AssetUrl::productVariant($able->thumbnail ?? $able->image ?? null, 'thumb') }}" alt="">
                <div class="name">{{ $able->name }}</div>
                <div class="price">{{ number_format((float) ($able->sale_price ?: $able->price), 0, ',', '.') }}đ</div>
                <a href="{{ url('/chi-tiet/' . ($able->slug ?? $able->id)) }}" class="accp-btn accp-btn--outline accp-btn--sm accp-btn--block" style="margin-top:4px">Viết đánh giá</a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
