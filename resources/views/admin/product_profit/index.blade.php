@extends('admin_layout')

@section('admin_content')
    @php
        $formatMoney = static fn ($value): string => number_format((float) $value, 0, ',', '.') . 'đ';
    @endphp

    <style>
        .adm-profit-management{padding:20px 16px}
        .adm-profit-management .adm-page-title{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:18px}
        .adm-profit-management .adm-title-left{display:flex;align-items:flex-start;gap:14px}
        .adm-profit-management .adm-page-title-icon{width:44px;height:44px;display:inline-flex;align-items:center;justify-content:center;border-radius:10px;background:#fff;border:1px solid var(--adm-border);color:var(--adm-blue);box-shadow:var(--adm-shadow);font-size:20px;flex:0 0 auto}
        .adm-profit-management .adm-page-title h1{margin-top:2px}
        .adm-profit-management .adm-page-title p{margin:6px 0 0;color:var(--adm-muted);font-size:13px}
        .adm-profit-management .adm-updated{height:38px;display:inline-flex;align-items:center;gap:8px;padding:0 14px;border:1px solid var(--adm-border);border-radius:8px;background:#fff;color:var(--adm-muted);box-shadow:var(--adm-shadow);white-space:nowrap}
        .adm-profit-management .adm-stats{grid-template-columns:repeat(5,minmax(0,1fr));gap:16px;margin-bottom:20px}
        .adm-profit-management .adm-stat{min-height:88px;flex-direction:row;align-items:center;gap:14px;padding:16px 18px;overflow:hidden}
        .adm-profit-management .adm-stat .icon{width:48px;height:48px;border-radius:14px;font-size:21px}
        .adm-profit-management .adm-stat .num{font-size:22px;white-space:nowrap}
        .adm-profit-management .adm-stat .lbl{font-size:12px}
        .adm-profit-management .adm-toolbar{display:grid;grid-template-columns:minmax(150px,1fr) minmax(150px,1fr) minmax(180px,1fr) minmax(180px,1fr) 230px;gap:14px;margin-bottom:14px;align-items:end;padding:18px;background:#fff;border:1px solid var(--adm-border);border-radius:var(--adm-radius);box-shadow:var(--adm-shadow)}
        .adm-profit-management .adm-toolbar-actions{display:grid;gap:8px}
        .adm-profit-management .adm-search,.adm-profit-management .adm-select,.adm-profit-management .adm-btn{min-height:40px;height:40px}
        .adm-profit-management .adm-btn{justify-content:center;font-size:13px;padding-left:12px;padding-right:12px}
        .adm-profit-management .adm-filter-label{display:block;margin-bottom:6px;font-size:11px;color:var(--adm-muted);font-weight:700}
        .adm-profit-management .adm-table-wrap{border-radius:var(--adm-radius);overflow-x:auto;background:#fff;border:1px solid var(--adm-border);box-shadow:var(--adm-shadow)}
        .adm-profit-management .adm-table-title{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 18px;border-bottom:1px solid var(--adm-border)}
        .adm-profit-management .adm-table-title strong{font-size:16px;color:var(--adm-dark)}
        .adm-profit-management .adm-table{width:100%;min-width:1080px;table-layout:fixed;font-size:12.5px}
        .adm-profit-management .adm-table thead th{padding:12px 10px;text-align:center!important;vertical-align:middle}
        .adm-profit-management .adm-table td{height:58px;padding:10px;text-align:center;vertical-align:middle;border-bottom:1px solid #edf2f7}
        .adm-profit-management .adm-image-col{width:82px}
        .adm-profit-management .adm-product-col{width:280px;text-align:center!important}
        .adm-profit-management .adm-category-col{width:145px}
        .adm-profit-management .adm-brand-col{width:130px}
        .adm-profit-management .adm-qty-col{width:120px}
        .adm-profit-management .adm-money-col{width:145px;white-space:nowrap}
        .adm-profit-management .adm-product-thumb{width:62px;height:48px;border-radius:5px;object-fit:contain;background:#fff;border:1px solid #edf2f7;display:block;margin:0 auto}
        .adm-profit-management .adm-product-name{display:block;color:#0f172a;font-weight:800;line-height:1.35}
        .adm-profit-management .adm-product-slug{display:block;margin-top:3px;color:var(--adm-muted);font-size:11px;line-height:1.3}
        .adm-profit-management .adm-profit-value{color:var(--adm-green);font-weight:800}
        @media(max-width:1500px){.adm-profit-management .adm-toolbar{grid-template-columns:repeat(4,minmax(0,1fr));}.adm-profit-management .adm-toolbar-actions{grid-column:1/-1;grid-template-columns:repeat(4,1fr)}}
        @media(max-width:1100px){.adm-profit-management .adm-page-title,.adm-profit-management .adm-title-left{display:block}.adm-profit-management .adm-updated{margin-top:12px}.adm-profit-management .adm-stats,.adm-profit-management .adm-toolbar,.adm-profit-management .adm-toolbar-actions{grid-template-columns:1fr}}
    </style>

    <div class="adm-page adm-profit-management">
        <div class="adm-page-title">
            <div class="adm-title-left">
                <span class="adm-page-title-icon"><i class="fa fa-line-chart"></i></span>
                <div>
                    <h1>Lợi nhuận sản phẩm</h1>
                    <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › Báo cáo › Sản phẩm</div>
                    <p>Theo dõi số lượng đã bán, doanh thu thực thu và lợi nhuận của từng sản phẩm dựa trên các đơn hoàn tất.</p>
                </div>
            </div>
            <span class="adm-updated"><i class="fa fa-calendar"></i> Cập nhật lúc {{ now()->format('d/m/Y H:i') }}</span>
        </div>

        @unless ($summary['tables_ready'] ?? false)
            <div class="alert alert-warning">
                Bảng dữ liệu đơn hàng của báo cáo lợi nhuận chưa sẵn sàng. Trang đang hiển thị ở chế độ an toàn cho đến khi
                <code>orders</code> và <code>order_items</code> được tạo trong cơ sở dữ liệu.
            </div>
        @endunless

        <div class="adm-stats">
            <div class="adm-stat"><div class="icon purple"><i class="fa fa-shopping-bag"></i></div><div><div class="num">{{ number_format($summary['products_count']) }}</div><div class="lbl">Sản phẩm có doanh thu</div></div></div>
            <div class="adm-stat"><div class="icon blue"><i class="fa fa-shopping-cart"></i></div><div><div class="num">{{ number_format($summary['sold_quantity']) }}</div><div class="lbl">Tổng số lượng đã bán</div></div></div>
            <div class="adm-stat"><div class="icon green"><i class="fa fa-money"></i></div><div><div class="num">{{ $formatMoney($summary['revenue']) }}</div><div class="lbl">Doanh thu thực thu</div></div></div>
            <div class="adm-stat"><div class="icon amber"><i class="fa fa-database"></i></div><div><div class="num">{{ $formatMoney($summary['cost_total']) }}</div><div class="lbl">Giá vốn</div></div></div>
            <div class="adm-stat"><div class="icon green"><i class="fa fa-line-chart"></i></div><div><div class="num" style="color:var(--adm-green)">{{ $formatMoney($summary['profit']) }}</div><div class="lbl">Lợi nhuận</div></div></div>
        </div>

        <form method="GET" action="{{ url('/product-profit') }}" class="adm-toolbar">
            <div>
                <label class="adm-filter-label" for="date_from">Từ ngày</label>
                <input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="adm-select">
            </div>
            <div>
                <label class="adm-filter-label" for="date_to">Đến ngày</label>
                <input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="adm-select">
            </div>
            <div>
                <label class="adm-filter-label" for="category_id">Danh mục</label>
                <select id="category_id" name="category_id" class="adm-select">
                    <option value="">Tất cả danh mục</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="adm-filter-label" for="brand_id">Thương hiệu</label>
                <select id="brand_id" name="brand_id" class="adm-select">
                    <option value="">Tất cả thương hiệu</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" @selected((string) ($filters['brand_id'] ?? '') === (string) $brand->id)>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="adm-toolbar-actions">
                <button type="submit" class="adm-btn primary"><i class="fa fa-filter"></i> Lọc báo cáo</button>
                <a href="{{ route('admin.product-profit.export', request()->except('page')) }}" class="adm-btn outline"><i class="fa fa-download"></i> Xuất CSV</a>
                <a href="{{ route('admin.product-profit.print', request()->except('page')) }}" target="_blank" class="adm-btn outline"><i class="fa fa-print"></i> In báo cáo</a>
                <a href="{{ url('/product-profit') }}" class="adm-btn outline"><i class="fa fa-refresh"></i> Xóa bộ lọc</a>
            </div>
        </form>

        <div class="adm-table-wrap">
            <div class="adm-table-title">
                <strong>Danh sách lợi nhuận sản phẩm</strong>
                <span class="adm-badge blue">{{ number_format($rows->total()) }} sản phẩm</span>
            </div>
            <table class="adm-table">
                <thead>
                    <tr>
                        <th class="adm-image-col">Ảnh</th>
                        <th class="adm-product-col">Sản phẩm</th>
                        <th class="adm-category-col">Danh mục</th>
                        <th class="adm-brand-col">Thương hiệu</th>
                        <th class="adm-qty-col">@include('admin.partials.sort_header', ['label' => 'Số lượng đã bán', 'asc' => 'sold_asc', 'desc' => 'sold_desc', 'filters' => $filters])</th>
                        <th class="adm-money-col">@include('admin.partials.sort_header', ['label' => 'Doanh thu', 'asc' => 'revenue_asc', 'desc' => 'revenue_desc', 'filters' => $filters])</th>
                        <th class="adm-money-col">@include('admin.partials.sort_header', ['label' => 'Giá vốn', 'asc' => 'cost_asc', 'desc' => 'cost_desc', 'filters' => $filters])</th>
                        <th class="adm-money-col">@include('admin.partials.sort_header', ['label' => 'Lợi nhuận', 'asc' => 'profit_asc', 'desc' => 'profit_desc', 'filters' => $filters])</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        @php
                            $thumb = ! empty($row->thumbnail_path) && file_exists(public_path($row->thumbnail_path))
                                ? asset($row->thumbnail_path)
                                : asset('uploads/product/no-image.svg');
                        @endphp
                        <tr>
                            <td class="adm-image-col">
                                <img class="adm-product-thumb" src="{{ $thumb }}" alt="{{ $row->product_name }}">
                            </td>
                            <td class="adm-product-col">
                                <span class="adm-product-name">{{ $row->product_name }}</span>
                                @if ($row->product_slug)
                                    <span class="adm-product-slug">{{ $row->product_slug }}</span>
                                @endif
                            </td>
                            <td class="adm-category-col">{{ $row->category_name ?: 'Đang cập nhật' }}</td>
                            <td class="adm-brand-col">{{ $row->brand_name ?: 'Đang cập nhật' }}</td>
                            <td class="adm-qty-col">{{ number_format((int) $row->sold_quantity) }}</td>
                            <td class="adm-money-col">{{ $formatMoney($row->revenue) }}</td>
                            <td class="adm-money-col">{{ $formatMoney($row->cost_total) }}</td>
                            <td class="adm-money-col"><span class="adm-profit-value">{{ $formatMoney($row->profit) }}</span> <i class="fa fa-arrow-up" style="color:var(--adm-green);margin-left:4px"></i></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding:36px;text-align:center;color:var(--adm-muted)">Chưa có dữ liệu lợi nhuận phù hợp với bộ lọc hiện tại.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @include('admin.partials.pagination', ['paginator' => $rows, 'label' => 'sản phẩm'])
        </div>
    </div>
@endsection
