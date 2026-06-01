@extends('admin_layout')

@section('admin_content')
<style>
    .adm-product-management {
        padding: 20px 16px;
    }

    .adm-product-management .adm-layout {
        grid-template-columns: 1fr;
    }

    .adm-product-management .adm-page-title {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 18px;
    }

    .adm-product-management .adm-page-title-icon {
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #fff;
        border: 1px solid var(--adm-border);
        color: var(--adm-blue);
        box-shadow: var(--adm-shadow);
        font-size: 20px;
        flex: 0 0 auto;
    }

    .adm-product-management .adm-page-title h1 {
        margin-top: 2px;
    }

    .adm-product-management .adm-stats {
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .adm-product-management .adm-stat {
        min-height: 88px;
        flex-direction: row;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        overflow: hidden;
    }

    .adm-product-management .adm-stat .icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        font-size: 21px;
    }

    .adm-product-management .adm-stat .num {
        font-size: 24px;
    }

    .adm-product-management .adm-stat .lbl {
        font-size: 12px;
    }

    .adm-product-management .adm-toolbar {
        display: grid;
        grid-template-columns: minmax(220px, 1fr) 170px 98px 152px;
        gap: 12px;
        margin-bottom: 14px;
        align-items: stretch;
        padding: 16px;
        background: #fff;
        border: 1px solid var(--adm-border);
        border-radius: var(--adm-radius);
        box-shadow: var(--adm-shadow);
    }

    .adm-product-management .adm-search,
    .adm-product-management .adm-select,
    .adm-product-management .adm-btn {
        min-height: 40px;
        height: 40px;
    }

    .adm-product-management .adm-btn {
        justify-content: center;
        font-size: 13px;
        padding-left: 12px;
        padding-right: 12px;
    }

    .adm-product-management .adm-table-wrap,
    .adm-product-management .adm-toolbar {
        width: 100%;
    }

    .adm-product-management .adm-table-wrap {
        border-radius: var(--adm-radius);
        overflow-x: auto;
    }

    .adm-product-management .adm-table {
        table-layout: fixed;
        font-size: 12.5px;
        width: 100%;
        min-width: 1040px;
    }

    .adm-product-management .adm-table th {
        padding: 12px 10px;
        text-align: center;
    }

    .adm-product-management .adm-table thead th {
        text-align: center !important;
        vertical-align: middle;
    }

    .adm-product-management .adm-table td {
        padding: 12px 10px;
        height: 118px;
        text-align: center;
    }

    .adm-product-management .adm-product-id-col {
        width: 64px;
    }

    .adm-product-management .adm-table td.id {
        color: var(--adm-blue);
        font-weight: 700;
        white-space: nowrap;
    }

    .adm-product-management .adm-product-image-col {
        width: 132px;
    }

    .adm-product-management .adm-product-thumb {
        width: 118px;
        height: 94px;
        display: block;
        margin: 0 auto;
        object-fit: contain;
        border-radius: 6px;
        background: #fff;
    }

    .adm-product-management .adm-product-name-cell {
        width: 260px;
        text-align: left !important;
    }

    .adm-product-management .adm-product-name-cell strong {
        display: block;
        max-width: 280px;
        color: #0f172a;
        font-size: 13.5px;
        line-height: 1.4;
        font-weight: 800;
    }

    .adm-product-management .adm-product-name-cell div {
        max-width: 250px;
        line-height: 1.35;
    }

    .adm-product-management .adm-actions a {
        width: 34px;
        height: 34px;
        border-radius: 8px;
    }

    .adm-product-management .adm-actions {
        justify-content: center;
    }

    .adm-product-management .adm-category-col {
        width: 104px;
    }

    .adm-product-management .adm-brand-col {
        width: 100px;
    }

    .adm-product-management .adm-price-col {
        width: 104px;
        white-space: nowrap;
    }

    .adm-product-management .adm-stock-col {
        width: 70px;
    }

    .adm-product-management .adm-status-col {
        width: 106px;
    }

    .adm-product-management .adm-action-col {
        width: 86px;
    }

    @media (max-width: 1500px) {
        .adm-product-management .adm-toolbar {
            grid-template-columns: minmax(200px, 1fr) 160px 94px 146px;
        }

    }

    @media (max-width: 1100px) {
        .adm-product-management {
            padding: 24px 20px;
        }

        .adm-product-management .adm-stats,
        .adm-product-management .adm-toolbar {
            grid-template-columns: 1fr;
        }

        .adm-product-management .adm-table-wrap {
            overflow-x: auto;
        }
    }
</style>

<div class="adm-page adm-product-management">
    <div class="adm-page-title">
        <span class="adm-page-title-icon"><i class="fa fa-cube"></i></span>
        <div>
            <h1>Quản lý sản phẩm</h1>
            <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › Quản lý sản phẩm</div>
        </div>
    </div>

    <div class="adm-stats">
        <div class="adm-stat">
            <div class="icon blue"><i class="fa fa-cubes"></i></div>
            <div><div class="num">{{ number_format($stats['total']) }}</div><div class="lbl">Tổng sản phẩm</div></div>
        </div>
        <div class="adm-stat">
            <div class="icon green"><i class="fa fa-check-circle"></i></div>
            <div><div class="num" style="color:var(--adm-green)">{{ number_format($stats['active']) }}</div><div class="lbl">Đang hoạt động</div></div>
        </div>
        <div class="adm-stat">
            <div class="icon amber"><i class="fa fa-exclamation-triangle"></i></div>
            <div><div class="num" style="color:var(--adm-amber)">{{ number_format($stats['low_stock']) }}</div><div class="lbl">Sắp hết hàng</div></div>
        </div>
        <div class="adm-stat">
            <div class="icon red"><i class="fa fa-times-circle"></i></div>
            <div><div class="num">{{ number_format($stats['out_of_stock']) }}</div><div class="lbl">Hết hàng</div></div>
        </div>
        <div class="adm-stat">
            <div class="icon" style="background:rgba(148,163,184,.15);color:#94a3b8"><i class="fa fa-eye-slash"></i></div>
            <div><div class="num">{{ number_format($stats['hidden']) }}</div><div class="lbl">Tạm ẩn</div></div>
        </div>
    </div>

    <div class="adm-layout">
        <div>
            <form class="adm-toolbar" method="GET" action="{{ route('admin.products.management') }}">
                <div class="adm-search">
                    <i class="fa fa-search"></i>
                    <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Tìm theo tên hoặc slug sản phẩm...">
                </div>
                <select class="adm-select" name="status">
                    <option value="all" @selected($filters['status'] === 'all')>Tất cả trạng thái</option>
                    <option value="active" @selected($filters['status'] === 'active')>Đang hoạt động</option>
                    <option value="hidden" @selected($filters['status'] === 'hidden')>Tạm ẩn</option>
                    <option value="low_stock" @selected($filters['status'] === 'low_stock')>Sắp hết hàng</option>
                    <option value="out_of_stock" @selected($filters['status'] === 'out_of_stock')>Hết hàng</option>
                </select>
                <button class="adm-btn outline" type="submit"><i class="fa fa-filter"></i> Lọc</button>
                <a href="{{ url('/add-product') }}" class="adm-btn primary"><i class="fa fa-plus"></i> Thêm sản phẩm</a>
            </form>

            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th class="adm-product-id-col">#</th>
                            <th class="adm-product-image-col">Ảnh</th>
                            <th class="adm-product-name-cell">Sản phẩm</th>
                            <th class="adm-category-col">Danh mục</th>
                            <th class="adm-brand-col">Thương hiệu</th>
                            <th class="adm-price-col">Giá bán</th>
                            <th class="adm-price-col">Giá vốn</th>
                            <th class="adm-stock-col">Tồn kho</th>
                            <th class="adm-status-col">Trạng thái</th>
                            <th class="adm-action-col">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td class="id adm-product-id-col">SP{{ str_pad((string) $product->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="adm-product-image-col">
                                <img
                                    src="{{ \App\Support\AssetUrl::productVariant($product->thumbnail_path, 'thumb') }}"
                                    alt="{{ $product->name }}"
                                    width="118"
                                    height="94"
                                    loading="lazy"
                                    class="adm-product-thumb">
                            </td>
                            <td class="adm-product-name-cell">
                                <strong>{{ $product->name }}</strong>
                                <div style="font-size:11px;color:var(--adm-muted)">{{ $product->slug }}</div>
                            </td>
                            <td class="adm-category-col">{{ $product->primaryCategory?->name ?? 'Chưa phân loại' }}</td>
                            <td class="adm-brand-col">{{ $product->brand?->name ?? 'Không chọn' }}</td>
                            <td class="adm-price-col">{{ number_format((float) $product->final_price, 0, ',', '.') }}đ</td>
                            <td class="adm-price-col">{{ number_format((float) $product->cost_price, 0, ',', '.') }}đ</td>
                            <td class="adm-stock-col">{{ number_format((int) $product->stock_quantity) }}</td>
                            <td class="adm-status-col">
                                @if (! $product->is_active)
                                    <span class="adm-badge gray">Tạm ẩn</span>
                                @elseif ((int) $product->stock_quantity <= 0)
                                    <span class="adm-badge red">Hết hàng</span>
                                @elseif ((int) $product->stock_quantity <= 10)
                                    <span class="adm-badge amber">Sắp hết</span>
                                @else
                                    <span class="adm-badge green">Hoạt động</span>
                                @endif
                            </td>
                            <td class="adm-action-col">
                                <div class="adm-actions">
                                    <a href="{{ url('/edit-product/' . $product->id) }}" title="Sửa"><i class="fa fa-pencil"></i></a>
                                    <a href="{{ url('/chi-tiet/' . $product->slug) }}" title="Xem storefront" target="_blank" rel="noopener"><i class="fa fa-eye"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="g-admin-empty-state">Chưa có sản phẩm nào phù hợp với bộ lọc hiện tại.</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                @include('admin.partials.pagination', ['paginator' => $products, 'label' => 'sản phẩm'])
            </div>
        </div>
    </div>
</div>
@endsection
