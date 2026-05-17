@extends('admin_layout')

@section('admin_content')

<div class="adm-page">
    <div class="adm-page-title">
        <h1><i class="fa fa-cube" style="color:var(--adm-teal);margin-right:8px"></i>Quản lý sản phẩm</h1>
        <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › Quản lý sản phẩm</div>
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
                            <th>Mã</th>
                            <th>Sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Thương hiệu</th>
                            <th>Giá bán</th>
                            <th>Tồn kho</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td class="id">SP{{ str_pad((string) $product->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                <div style="font-size:11px;color:var(--adm-muted)">{{ $product->slug }}</div>
                            </td>
                            <td>{{ $product->primaryCategory?->name ?? 'Chưa phân loại' }}</td>
                            <td>{{ $product->brand?->name ?? 'Không chọn' }}</td>
                            <td>{{ number_format((float) $product->final_price, 0, ',', '.') }}đ</td>
                            <td>{{ number_format((int) $product->stock_quantity) }}</td>
                            <td>
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
                            <td>
                                <div class="adm-actions">
                                    <a href="{{ url('/edit-product/' . $product->id) }}" title="Sửa"><i class="fa fa-pencil"></i></a>
                                    <a href="{{ url('/chi-tiet/' . $product->slug) }}" title="Xem storefront" target="_blank" rel="noopener"><i class="fa fa-eye"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="g-admin-empty-state">Chưa có sản phẩm nào phù hợp với bộ lọc hiện tại.</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="adm-pagination">
                    <span class="info">Hiển thị {{ $products->count() }} / {{ $products->total() }} sản phẩm</span>
                    <div class="pages">{{ $products->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
