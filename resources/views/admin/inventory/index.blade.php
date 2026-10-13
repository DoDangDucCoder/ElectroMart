@extends('admin_layout')

@section('admin_content')
    @php
        $statusLabels = [
            'all' => 'Tất cả',
            'in_stock' => 'Còn hàng',
            'low_stock' => 'Sắp hết',
            'out_of_stock' => 'Hết hàng',
        ];

        $formatMoney = static fn ($value): string => number_format((float) $value, 0, ',', '.') . 'đ';
        $stockStatus = static function ($product) use ($fallbackThreshold): array {
            $stock = (int) ($product->stock_quantity ?? 0);
            $threshold = (int) ($product->stock_alert_threshold ?: $fallbackThreshold);

            if ($stock <= 0) {
                return ['label' => 'Hết hàng', 'class' => 'red'];
            }

            if ($stock <= $threshold) {
                return ['label' => 'Sắp hết', 'class' => 'amber'];
            }

            return ['label' => 'Còn hàng', 'class' => 'green'];
        };
    @endphp

    <style>
        .adm-inventory-management{padding:20px 16px}
        .adm-inventory-management .adm-page-title{display:flex;align-items:flex-start;gap:14px;margin-bottom:18px}
        .adm-inventory-management .adm-page-title-icon{width:44px;height:44px;display:inline-flex;align-items:center;justify-content:center;border-radius:10px;background:#fff;border:1px solid var(--adm-border);color:var(--adm-blue);box-shadow:var(--adm-shadow);font-size:20px;flex:0 0 auto}
        .adm-inventory-management .adm-page-title h1{margin-top:2px}
        .adm-inventory-management .adm-stats{grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:20px}
        .adm-inventory-management .adm-stat{min-height:88px;flex-direction:row;align-items:center;gap:14px;padding:16px 18px;overflow:hidden}
        .adm-inventory-management .adm-stat .icon{width:48px;height:48px;border-radius:14px;font-size:21px}
        .adm-inventory-management .adm-stat .num{font-size:22px}
        .adm-inventory-management .adm-stat .lbl{font-size:12px}
        .adm-inventory-management .adm-summary-card{background:#fff;border:1px solid var(--adm-border);border-radius:var(--adm-radius);box-shadow:var(--adm-shadow);margin-bottom:14px}
        .adm-inventory-management .adm-card-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;padding:16px 18px;border-bottom:1px solid var(--adm-border)}
        .adm-inventory-management .adm-card-head strong{font-size:16px;color:var(--adm-dark)}
        .adm-inventory-management .adm-card-head p{margin:4px 0 0;color:var(--adm-muted);font-size:12px}
        .adm-inventory-management .adm-summary-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:0;padding:16px}
        .adm-inventory-management .adm-summary-item{display:flex;align-items:center;gap:14px;min-height:58px;padding:0 18px;background:#fff;border-right:1px solid var(--adm-border)}
        .adm-inventory-management .adm-summary-item:last-child{border-right:0}
        .adm-inventory-management .adm-summary-icon{width:38px;height:38px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;flex:0 0 auto}
        .adm-inventory-management .adm-summary-icon.blue{background:#eff6ff;color:var(--adm-blue)}
        .adm-inventory-management .adm-summary-icon.amber{background:#fffbeb;color:var(--adm-amber)}
        .adm-inventory-management .adm-summary-icon.green{background:#f0fdf4;color:var(--adm-green)}
        .adm-inventory-management .adm-summary-item strong{display:block;color:var(--adm-blue);font-size:18px;margin-bottom:4px}
        .adm-inventory-management .adm-summary-item span{color:var(--adm-muted);font-size:12px}
        .adm-inventory-management .adm-toolbar{display:grid;grid-template-columns:minmax(220px,1fr) 170px 220px 200px 100px 112px;gap:12px;margin-bottom:14px;align-items:end;padding:16px;background:#fff;border:1px solid var(--adm-border);border-radius:var(--adm-radius);box-shadow:var(--adm-shadow)}
        .adm-inventory-management .adm-search,.adm-inventory-management .adm-select,.adm-inventory-management .adm-btn{min-height:40px;height:40px}
        .adm-inventory-management .adm-btn{justify-content:center;font-size:13px;padding-left:12px;padding-right:12px}
        .adm-inventory-management .adm-filter-label{display:block;margin-bottom:6px;font-size:11px;color:var(--adm-muted);font-weight:700}
        .adm-inventory-management .adm-table-wrap{border-radius:var(--adm-radius);overflow-x:auto}
        .adm-inventory-management .adm-table-title{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 18px;border-bottom:1px solid var(--adm-border)}
        .adm-inventory-management .adm-table-title strong{font-size:16px;color:var(--adm-dark)}
        .adm-inventory-management .adm-table-title p{margin:4px 0 0;color:var(--adm-muted);font-size:12px}
        .adm-inventory-management .adm-table-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:flex-end}
        .adm-inventory-management .adm-table{width:100%;min-width:980px;table-layout:fixed;font-size:12.5px}
        .adm-inventory-management .adm-table thead th{padding:12px 10px;text-align:center!important;vertical-align:middle}
        .adm-inventory-management .adm-table td{height:62px;padding:10px;text-align:center;vertical-align:middle}
        .adm-inventory-management .adm-sku-col{width:128px}
        .adm-inventory-management .adm-image-col{width:86px}
        .adm-inventory-management .adm-product-col{width:280px;text-align:center!important}
        .adm-inventory-management .adm-brand-col{width:110px}
        .adm-inventory-management .adm-category-col{width:110px}
        .adm-inventory-management .adm-small-col{width:82px}
        .adm-inventory-management .adm-money-col{width:120px;white-space:nowrap}
        .adm-inventory-management .adm-status-col{width:108px}
        .adm-inventory-management .adm-date-col{width:118px}
        .adm-inventory-management .adm-product-thumb{width:62px;height:48px;border-radius:5px;object-fit:contain;background:#fff;border:1px solid #edf2f7;display:block;margin:0 auto}
        .adm-inventory-management .adm-product-name{display:block;color:var(--adm-blue);font-weight:800;line-height:1.35}
        .adm-inventory-management .adm-table td{border-bottom:1px solid #edf2f7}
        @media(max-width:1500px){.adm-inventory-management .adm-toolbar{grid-template-columns:minmax(200px,1fr) 150px 190px 180px 96px 108px}}
        @media(max-width:1100px){.adm-inventory-management .adm-stats,.adm-inventory-management .adm-summary-grid,.adm-inventory-management .adm-toolbar{grid-template-columns:1fr}}
    </style>

    <div class="adm-page adm-inventory-management">
        <div class="adm-page-title">
            <span class="adm-page-title-icon"><i class="fa fa-cube"></i></span>
            <div>
                <h1>Tồn kho sản phẩm</h1>
                <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › Sản phẩm › Tồn kho</div>
            </div>
        </div>

        <div class="adm-stats">
            <div class="adm-stat" data-filter-field="status" data-filter-value="all"><div class="icon blue"><i class="fa fa-cube"></i></div><div><div class="num">{{ number_format($stats['total_products']) }}</div><div class="lbl">Sản phẩm</div></div></div>
            <div class="adm-stat"><div class="icon blue"><i class="fa fa-tags"></i></div><div><div class="num">{{ number_format($stats['total_skus']) }}</div><div class="lbl">Mã tồn kho / biến thể</div></div></div>
            <div class="adm-stat"><div class="icon teal"><i class="fa fa-database"></i></div><div><div class="num">{{ number_format($stats['total_stock_quantity']) }}</div><div class="lbl">Tổng tồn</div></div></div>
            <div class="adm-stat"><div class="icon purple"><i class="fa fa-money"></i></div><div><div class="num">{{ $formatMoney($stats['total_inventory_value']) }}</div><div class="lbl">Giá trị tồn ước tính</div></div></div>
        </div>

        <section class="adm-summary-card">
            <div class="adm-card-head">
                <div>
                    <strong>Tổng quan tồn kho</strong>
                    <p>Giá trị tồn kho = số lượng tồn * giá vốn. Ngưỡng cảnh báo mặc định là {{ $fallbackThreshold }} nếu sản phẩm chưa cấu hình.</p>
                </div>
                <span class="adm-badge blue">Chỉ đọc</span>
            </div>
            <div class="adm-summary-grid">
                <div class="adm-summary-item"><span class="adm-summary-icon blue"><i class="fa fa-cube"></i></span><div><strong>{{ number_format($stats['product_stock_quantity']) }}</strong><span>Tồn sản phẩm gốc</span></div></div>
                <div class="adm-summary-item"><span class="adm-summary-icon blue"><i class="fa fa-tag"></i></span><div><strong>{{ number_format($stats['variant_stock_quantity']) }}</strong><span>Tồn biến thể</span></div></div>
                <div class="adm-summary-item"><span class="adm-summary-icon amber"><i class="fa fa-shield"></i></span><div><strong style="color:var(--adm-amber)">{{ $fallbackThreshold }}</strong><span>Ngưỡng cảnh báo mặc định</span></div></div>
                <div class="adm-summary-item"><span class="adm-summary-icon green"><i class="fa fa-calculator"></i></span><div><strong style="color:var(--adm-dark)">Giá trị tồn kho =</strong><span>Số lượng tồn × Giá vốn</span></div></div>
            </div>
        </section>

        <form method="get" action="{{ route('admin.inventory') }}" class="adm-toolbar">
            <div>
                <label class="adm-filter-label" for="q">Từ khóa</label>
                <div class="adm-search">
                    <i class="fa fa-search"></i>
                    <input type="search" name="q" id="q" value="{{ $filters['q'] }}" placeholder="Nhập tên sản phẩm, mã tồn kho, barcode...">
                </div>
            </div>
            <div>
                <label class="adm-filter-label" for="status">Trạng thái tồn</label>
                <select name="status" id="status" class="adm-select">
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="adm-filter-label" for="category_id">Danh mục</label>
                <select name="category_id" id="category_id" class="adm-select">
                    <option value="0">Tất cả danh mục</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) $filters['category_id'] === (int) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="adm-filter-label" for="brand_id">Thương hiệu</label>
                <select name="brand_id" id="brand_id" class="adm-select">
                    <option value="0">Tất cả thương hiệu</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" @selected((int) $filters['brand_id'] === (int) $brand->id)>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="adm-btn primary"><i class="fa fa-search"></i> Lọc tồn kho</button>
            <a href="{{ route('admin.inventory') }}" class="adm-btn outline">Xóa lọc</a>
        </form>

        <div class="adm-table-wrap">
            <div class="adm-table-title">
                <div>
                    <strong>Danh sách tồn kho cấp sản phẩm</strong>
                    <p>Chỉ đọc. Muốn thay đổi số lượng, dùng luồng quản lý sản phẩm hiện có hoặc thiết kế movement log riêng.</p>
                </div>
                <div class="adm-table-actions">
                    <a href="{{ route('admin.inventory.export', request()->except('page')) }}" class="adm-btn outline"><i class="fa fa-download"></i> Xuất CSV</a>
                    <span class="adm-badge blue">{{ number_format($products->total()) }} kết quả</span>
                </div>
            </div>

            @if ($products->isEmpty())
                <div class="g-admin-empty-state">
                    <h3>Không có sản phẩm phù hợp</h3>
                    <p>Thử thay đổi bộ lọc hoặc kiểm tra dữ liệu sản phẩm hiện tại.</p>
                </div>
            @else
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th class="adm-sku-col">Mã tồn kho</th>
                            <th class="adm-image-col">Ảnh</th>
                            <th class="adm-product-col">@include('admin.partials.sort_header', ['label' => 'Tên sản phẩm', 'asc' => 'name_asc', 'desc' => 'name_desc', 'filters' => $filters])</th>
                            <th class="adm-brand-col">Thương hiệu</th>
                            <th class="adm-category-col">Danh mục</th>
                            <th class="adm-small-col">@include('admin.partials.sort_header', ['label' => 'Số lượng tồn', 'asc' => 'stock_asc', 'desc' => 'stock_desc', 'filters' => $filters])</th>
                            <th class="adm-money-col">@include('admin.partials.sort_header', ['label' => 'Giá vốn', 'asc' => 'cost_asc', 'desc' => 'cost_desc', 'filters' => $filters])</th>
                            <th class="adm-status-col">Trạng thái</th>
                            <th class="adm-date-col">@include('admin.partials.sort_header', ['label' => 'Cập nhật', 'asc' => 'updated_asc', 'desc' => 'updated', 'filters' => $filters])</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            @php
                                $stock = (int) ($product->stock_quantity ?? 0);
                                $threshold = (int) ($product->stock_alert_threshold ?: $fallbackThreshold);
                                $costPrice = (float) ($product->cost_price ?? 0);
                                $status = $stockStatus($product);
                            @endphp
                            <tr>
                                <td class="adm-sku-col">{{ $product->sku ?: 'Chưa có mã tồn kho' }}</td>
                                <td class="adm-image-col">
                                    @php
                                        $thumb = $product->thumbnail_path && file_exists(public_path($product->thumbnail_path))
                                            ? asset($product->thumbnail_path)
                                            : asset('uploads/product/no-image.svg');
                                    @endphp
                                    <img class="adm-product-thumb" src="{{ $thumb }}" alt="{{ $product->name }}">
                                </td>
                                <td class="adm-product-col">
                                    <span class="adm-product-name">{{ $product->name }}</span>
                                    @if (! $product->is_active)
                                        <span class="adm-badge gray" style="margin-top:4px">Tạm ẩn</span>
                                    @endif
                                </td>
                                <td class="adm-brand-col">{{ $product->brand?->name ?: 'Chưa gán' }}</td>
                                <td class="adm-category-col">{{ $product->primaryCategory?->name ?: 'Chưa gán' }}</td>
                                <td class="adm-small-col"><strong>{{ number_format($stock) }}</strong></td>
                                <td class="adm-money-col">{{ $formatMoney($costPrice) }}</td>
                                <td class="adm-status-col"><span class="adm-badge {{ $status['class'] }}">{{ $status['label'] }}</span></td>
                                <td class="adm-date-col">{{ $product->updated_at ? $product->updated_at->format('d/m/Y H:i') : 'Không rõ' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @include('admin.partials.pagination', ['paginator' => $products, 'label' => 'sản phẩm'])
            @endif
        </div>
    </div>
@endsection
