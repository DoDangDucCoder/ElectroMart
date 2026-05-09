@extends('layouts.home-tech')
@section('body_class', 'g-compare cmp-page')

@php
    $productCount = $products->count();
    $maxCompare = 4;
@endphp

@section('content')
<style>
body.cmp-page{background:#f3f5f7;font-family:'Inter',-apple-system,sans-serif}
.cmp-wrap{max-width:1180px;margin:0 auto;padding:0 16px 40px}
.cmp-crumb{font-size:13px;color:#6b7280;padding:14px 0;display:flex;align-items:center;gap:8px}
.cmp-crumb a{color:#6b7280;text-decoration:none}.cmp-crumb a:hover{color:#0d9488}
.cmp-crumb .sep{color:#cbd5e1}.cmp-crumb .current{color:#111827;font-weight:500}
.cmp-head{margin-bottom:20px}.cmp-head h1{font-size:26px;font-weight:800;color:#0f172a;margin:0 0 6px}
.cmp-head p{font-size:14px;color:#6b7280;margin:0;max-width:700px}
/* Toolbar */
.cmp-toolbar{display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap}
.cmp-search{flex:1;min-width:200px;position:relative}
.cmp-search input{width:100%;height:42px;border:1px solid #e5e7eb;border-radius:8px;padding:0 14px 0 38px;font-size:13px;color:#111827}
.cmp-search input:focus{outline:none;border-color:#0d9488;box-shadow:0 0 0 3px rgba(13,148,136,.12)}
.cmp-search i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:14px}
.cmp-search button{position:absolute;right:5px;top:5px;height:32px;padding:0 12px;border:0;border-radius:6px;background:#0d9488;color:#fff;font-size:12px;font-weight:600}
.cmp-select{height:42px;border:1px solid #e5e7eb;border-radius:8px;padding:0 32px 0 14px;font-size:13px;color:#111827;background:#fff;appearance:none;background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'><polyline points='6 9 12 15 18 9'/></svg>");background-repeat:no-repeat;background-position:right 10px center}
.cmp-clear-btn{height:42px;padding:0 18px;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;transition:all .2s}
.cmp-clear-btn:hover{background:#dc2626;color:#fff;border-color:#dc2626}
/* Table */
.cmp-card{background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.04);overflow:hidden;margin-bottom:24px}
.cmp-table-wrap{overflow-x:auto}
.cmp-table{width:100%;border-collapse:collapse;min-width:700px}
.cmp-table th,.cmp-table td{padding:14px 16px;text-align:left;border-bottom:1px solid #f1f5f9;font-size:13px;vertical-align:top}
.cmp-table th{font-weight:600;color:#6b7280;background:#f8fafc;text-transform:uppercase;font-size:12px;letter-spacing:.3px;white-space:nowrap;min-width:120px}
.cmp-table td{color:#374151}
/* Product header cells */
.cmp-prod-cell{text-align:center;padding:20px 12px;position:relative}
.cmp-prod-remove{position:absolute;top:8px;right:8px;width:24px;height:24px;border-radius:50%;background:#f3f4f6;color:#9ca3af;border:none;cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;transition:all .15s}
.cmp-prod-remove:hover{background:#fef2f2;color:#dc2626}
.cmp-prod-img{width:140px;height:140px;margin:0 auto 12px;border-radius:10px;overflow:hidden;background:#fff;border:1px solid #e5e7eb}
.cmp-prod-img img{width:100%;height:100%;object-fit:contain}
.cmp-prod-brand{display:inline-flex;align-items:center;gap:4px;font-size:11px;color:#6b7280;margin-bottom:4px}
.cmp-prod-brand .badge{width:18px;height:18px;border-radius:50%;background:#0d9488;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:9px;font-weight:700}
.cmp-prod-name{font-size:13px;font-weight:700;color:#111827;margin-bottom:8px;line-height:1.35}
.cmp-prod-price{display:flex;flex-direction:column;align-items:center;gap:2px;margin-bottom:8px}
.cmp-prod-price .sale{font-size:16px;font-weight:800;color:#dc2626}
.cmp-prod-price .original{font-size:12px;color:#9ca3af;text-decoration:line-through}
.cmp-prod-price .discount-badge{display:inline-block;padding:2px 8px;border-radius:4px;background:#fef2f2;color:#dc2626;font-size:11px;font-weight:700}
.cmp-prod-stock{font-size:12px;color:#16a34a;margin-bottom:10px;display:flex;align-items:center;justify-content:center;gap:4px}
.cmp-prod-actions{display:flex;gap:6px;justify-content:center;flex-wrap:wrap}
.cmp-btn{height:34px;padding:0 14px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:5px;transition:all .15s;text-decoration:none;border:none}
.cmp-btn--outline{background:#fff;color:#374151;border:1px solid #d1d5db}
.cmp-btn--outline:hover{border-color:#0d9488;color:#0d9488}
.cmp-btn--primary{background:#0d9488;color:#fff}
.cmp-btn--primary:hover{background:#0f766e;color:#fff;text-decoration:none}
/* Highlight rows */
.cmp-table tbody tr:nth-child(even){background:#fafbfc}
.cmp-table tbody tr:hover{background:#f0fdfa}
.cmp-highlight-row td{font-weight:600;color:#111827}
/* Ưu điểm list */
.cmp-pros{list-style:none;padding:0;margin:0}
.cmp-pros li{font-size:12px;padding:3px 0;display:flex;align-items:flex-start;gap:6px;line-height:1.4}
.cmp-pros li i{color:#0d9488;margin-top:3px;flex-shrink:0}
/* Recommend section */
.cmp-recommend{margin-bottom:32px}
.cmp-recommend-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.cmp-recommend-head h2{font-size:18px;font-weight:700;color:#111827;margin:0}
.cmp-recommend-head a{font-size:13px;color:#0d9488;text-decoration:none;font-weight:500}
.cmp-recommend-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
@media(max-width:991px){.cmp-recommend-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:600px){.cmp-recommend-grid{grid-template-columns:repeat(2,1fr)}}
/* Policy */
.cmp-policy{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px 24px;display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:32px}
@media(max-width:768px){.cmp-policy{grid-template-columns:repeat(2,1fr)}}
.cmp-policy-item{display:flex;gap:12px;align-items:flex-start}
.cmp-policy-icon{width:40px;height:40px;border-radius:10px;background:#f0fdfa;color:#0d9488;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.cmp-policy-title{font-size:13px;font-weight:700;color:#111827}
.cmp-policy-sub{font-size:11.5px;color:#6b7280;line-height:1.4;margin-top:2px}
/* Empty state */
.cmp-empty{text-align:center;padding:60px 20px}
.cmp-empty i{font-size:48px;color:#cbd5e1;margin-bottom:16px}
.cmp-empty h3{font-size:18px;font-weight:700;color:#111827;margin:0 0 8px}
.cmp-empty p{font-size:14px;color:#6b7280;margin:0 0 20px}
.cmp-empty a{display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:#0d9488;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;font-size:14px}
.cmp-empty a:hover{background:#0f766e;color:#fff;text-decoration:none}
</style>

<div class="cmp-wrap">
    @include('partials.storefront.breadcrumb', [
        'class' => 'cmp-crumb',
        'separator' => '›',
        'items' => [
            [
                'label' => 'So sánh sản phẩm',
                'current' => true,
            ],
        ],
    ])

    <div class="cmp-head">
        <h1>So sánh sản phẩm</h1>
        <p>So sánh thông số kỹ thuật, giá cả và tính năng để chọn sản phẩm phù hợp nhất với nhu cầu của bạn.</p>
    </div>

    <div class="cmp-toolbar">
        <form class="cmp-search" action="{{ url('/san-pham') }}" method="GET">
            <i class="fa fa-search"></i>
            <input type="text" name="keywords_submit" placeholder="Tìm sản phẩm để so sánh" id="cmpSearchInput">
            <button type="submit">Tìm</button>
        </form>
        <select class="cmp-select"><option>Tất cả danh mục</option></select>
        <select class="cmp-select"><option>Sắp xếp: Mặc định</option></select>
        @if ($productCount > 0)
        <button type="button" class="cmp-clear-btn" onclick="clearCompare()"><i class="fa fa-trash-o"></i> Xóa tất cả</button>
        @endif
    </div>

    @if ($products->isEmpty())
        <div class="cmp-card">
            <div class="cmp-empty">
                <i class="fa fa-balance-scale"></i>
                <h3>Chưa có sản phẩm để so sánh</h3>
                <p>Thêm sản phẩm vào danh sách so sánh để xem bảng thông số chi tiết.</p>
                <a href="{{ url('/san-pham') }}"><i class="fa fa-search"></i> Tìm sản phẩm</a>
            </div>
        </div>
    @else
        <div class="cmp-card">
            <div class="cmp-table-wrap">
                <table class="cmp-table">
                    <thead>
                        <tr>
                            <th style="min-width:140px">Sản phẩm</th>
                            @foreach ($products as $product)
                                @php
                                    $img = \App\Support\AssetUrl::product($product->product_image);
                                    $name = \App\Support\StorefrontLabel::neutralizeSource($product->product_name);
                                    $brand = \App\Support\StorefrontLabel::neutralizeSource($product->brand?->brand_name ?? $product->brand?->name ?? $product->brand_name ?? '-');
                                    $brandInitial = strtoupper(mb_substr($brand, 0, 2));
                                    $price = (float)($product->price ?? $product->product_price ?? 0);
                                    $salePrice = (float)($product->product_price_sale ?? $product->sale_price ?? $price);
                                    $hasDiscount = $salePrice < $price && $salePrice > 0;
                                    $discountPct = $hasDiscount ? round(($price - $salePrice) / $price * 100) : 0;
                                    $compareProductId = $product->product_id ?? $product->id ?? null;
                                @endphp
                                <th class="cmp-prod-cell">
                                    <button type="button" class="cmp-prod-remove" title="Xóa"><i class="fa fa-times"></i></button>
                                    <div class="cmp-prod-img"><img src="{{ $img }}" alt="{{ $name }}"></div>
                                    <div class="cmp-prod-brand"><span class="badge">{{ $brandInitial }}</span> {{ $brand }}</div>
                                    <div class="cmp-prod-name">{{ $name }}</div>
                                    <div class="cmp-prod-price">
                                        <span class="sale">{{ number_format($hasDiscount ? $salePrice : $price, 0, ',', '.') }}đ</span>
                                        @if ($hasDiscount)
                                            <span class="original">{{ number_format($price, 0, ',', '.') }}đ</span>
                                            <span class="discount-badge">-{{ $discountPct }}%</span>
                                        @endif
                                    </div>
                                    <div class="cmp-prod-stock"><i class="fa fa-check-circle"></i> Còn hàng</div>
                                    <div class="cmp-prod-actions">
                                        <a href="{{ url('chi-tiet/' . $product->product_slug) }}" class="cmp-btn cmp-btn--outline"><i class="fa fa-eye"></i> Xem chi tiết</a>
                                        @if ($compareProductId)
                                            <button type="button" class="cmp-btn cmp-btn--primary" onclick="if(typeof Addtocart==='function'){Addtocart({{ $compareProductId }});}">
                                                <i class="fa fa-cart-plus"></i> Thêm vào giỏ
                                            </button>
                                        @else
                                            <a href="{{ url('chi-tiet/' . $product->product_slug) }}" class="cmp-btn cmp-btn--primary">
                                                <i class="fa fa-eye"></i> Xem sản phẩm
                                            </a>
                                        @endif
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (['Thương hiệu', 'Danh mục', 'Giá gốc', 'Khuyến mãi', 'Giá sau giảm'] as $rowIdx => $rowLabel)
                        <tr class="{{ $rowIdx === 4 ? 'cmp-highlight-row' : '' }}">
                            <th>{{ $rowLabel }}</th>
                            @foreach ($products as $product)
                                <td>
                                    @if ($rowLabel === 'Thương hiệu') {{ \App\Support\StorefrontLabel::neutralizeSource($product->brand?->brand_name ?? $product->brand?->name ?? '-') }}
                                    @elseif ($rowLabel === 'Danh mục') {{ \App\Support\StorefrontLabel::neutralizeSource($product->primaryCategory?->category_name ?? $product->primaryCategory?->name ?? '-') }}
                                    @elseif ($rowLabel === 'Giá gốc') {{ number_format((float)($product->price ?? $product->product_price ?? 0), 0, ',', '.') }}đ
                                    @elseif ($rowLabel === 'Khuyến mãi')
                                        @if ((float)$product->sale_amount > 0)
                                            @if ($product->sale_type === 'percent') -{{ rtrim(rtrim(number_format((float)$product->sale_value, 2, '.', ''), '0'), '.') }}%
                                            @else -{{ number_format((float)$product->sale_value, 0, ',', '.') }}đ @endif
                                        @else - @endif
                                    @elseif ($rowLabel === 'Giá sau giảm')
                                        @include('partials.storefront.product-price', ['product' => $product, 'currency' => 'VND', 'showExplanation' => false, 'compact' => true])
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @endforeach

                        @foreach ($specRows as $specRow)
                        <tr>
                            <th>{{ \App\Support\StorefrontLabel::neutralizeSource($specRow['label'] ?? '-') }}</th>
                            @foreach ($products as $product)
                                @php $value = $specRow['values'][$product->id] ?? '-'; $value = \App\Support\StorefrontLabel::neutralizeSource((string) $value, '-'); @endphp
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Recommended --}}
    @if (!empty($suggestedProducts) && $suggestedProducts->count() > 0)
    <div class="cmp-recommend">
        <div class="cmp-recommend-head">
            <h2>Sản phẩm gợi ý để so sánh</h2>
            <a href="{{ url('/san-pham') }}">Xem tất cả <i class="fa fa-arrow-right"></i></a>
        </div>
        <div class="cmp-recommend-grid">
            @foreach ($suggestedProducts->take(4) as $product)
                <div>@include('partials.home.product-card', ['product' => $product, 'compact' => true])</div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="cmp-policy">
        <div class="cmp-policy-item"><span class="cmp-policy-icon"><i class="fa fa-truck"></i></span><div><div class="cmp-policy-title">Miễn phí giao hàng</div><div class="cmp-policy-sub">Áp dụng cho đơn hàng công nghệ đủ điều kiện</div></div></div>
        <div class="cmp-policy-item"><span class="cmp-policy-icon"><i class="fa fa-refresh"></i></span><div><div class="cmp-policy-title">Đổi trả dễ dàng</div><div class="cmp-policy-sub">Hỗ trợ đổi trả trong 30 ngày nếu có lỗi do NSX</div></div></div>
        <div class="cmp-policy-item"><span class="cmp-policy-icon"><i class="fa fa-shield"></i></span><div><div class="cmp-policy-title">Bảo hành chính hãng</div><div class="cmp-policy-sub">An tâm sử dụng với bảo hành chính hãng</div></div></div>
        <div class="cmp-policy-item"><span class="cmp-policy-icon"><i class="fa fa-headphones"></i></span><div><div class="cmp-policy-title">Hỗ trợ tư vấn 24/7</div><div class="cmp-policy-sub">Tư vấn chọn laptop, điện thoại và phụ kiện phù hợp</div></div></div>
    </div>
</div>

<script type="text/javascript">
    function clearCompare() {
        localStorage.removeItem('compare');
        window.location.href = "{{ url('/so-sanh') }}";
    }
    (function pruneInvalidCompareStorage() {
        var validIds = @json($products->pluck('product_id')->map(fn ($id) => (string) $id)->values());
        if (!validIds.length) return;
        try {
            var raw = localStorage.getItem('compare');
            var data = raw ? JSON.parse(raw) : [];
            if (!Array.isArray(data)) return;
            var next = data.filter(function(item) { return item && validIds.indexOf(String(item.id)) !== -1; });
            if (next.length !== data.length) localStorage.setItem('compare', JSON.stringify(next));
        } catch (e) { localStorage.removeItem('compare'); }
    })();
    (function syncCompareIds() {
        var hasProducts = {{ $products->count() }};
        if (hasProducts) return;
        var currentIds = new URLSearchParams(window.location.search).get('ids') || '';
        var raw = localStorage.getItem('compare');
        if (!raw) return;
        try {
            var data = JSON.parse(raw);
            if (!Array.isArray(data) || data.length === 0) return;
            var ids = data.map(function(item) { return item.id; }).filter(Boolean).join(',');
            if (!ids || ids === currentIds) return;
            window.location.href = "{{ url('/so-sanh') }}" + '?ids=' + encodeURIComponent(ids);
        } catch (e) { return; }
    })();
</script>
@endsection
